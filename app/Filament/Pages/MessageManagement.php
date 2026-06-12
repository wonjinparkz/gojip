<?php

namespace App\Filament\Pages;

use App\Models\SmsHistory;
use App\Models\SmsTemplate;
use App\Models\Tenant;
use App\Services\AligoService;
use BackedEnum;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;

class MessageManagement extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;
    protected static ?string $navigationLabel = '문자 관리';
    protected static ?int $navigationSort = 7;
    protected static ?string $slug = 'message-management';
    protected string $view = 'filament.pages.message-management';

    // 모달 상태
    public bool $showAddModal = false;
    public bool $showEditModal = false;
    public bool $showUseModal = false;

    // 템플릿 폼 데이터
    public ?int $editingTemplateId = null;
    public string $tplTitle = '';
    public string $tplType = '';
    public string $tplContent = '';

    // 사용하기 폼 데이터
    public string $sendRecipient = '전체 입주자';
    public string $sendType = '일반';
    public string $sendContent = '';
    public string $sendPhone = '';

    public function mount(): void
    {
        $this->seedDefaultTemplatesIfNeeded();
    }

    private function seedDefaultTemplatesIfNeeded(): void
    {
        $userId = auth()->id();
        $branchId = session('current_branch_id');
        if (! $userId || ! $branchId) {
            return;
        }
        $exists = SmsTemplate::where('user_id', $userId)
            ->where('branch_id', $branchId)
            ->exists();
        if ($exists) {
            return;
        }
        foreach ($this->getDefaultTemplates() as $i => $t) {
            SmsTemplate::create([
                'user_id' => $userId,
                'branch_id' => $branchId,
                'title' => $t['title'],
                'type' => $t['type'],
                'content' => $t['content'],
                'is_favorite' => false,
                'sort_order' => $i,
            ]);
        }
    }

    private function templatesQuery()
    {
        return SmsTemplate::where('user_id', auth()->id())
            ->where('branch_id', session('current_branch_id'));
    }

    public function getTitle(): string
    {
        return '문자 관리';
    }

    // ── 입주자별 발송 내역 (모달용 JS 데이터) ──
    #[Computed]
    public function residentHistory(): array
    {
        $branchId = session('current_branch_id');
        if (! $branchId) {
            return [];
        }

        $typeMap = [
            '결제' => 'type-unpaid',
            '시설' => 'type-facility',
            '공지' => 'type-notice',
            '입퇴실' => 'type-notice',
            '일반' => 'type-notice',
        ];

        return SmsHistory::where('branch_id', $branchId)
            ->orderBy('sent_at', 'desc')
            ->get()
            ->map(function ($h) use ($typeMap) {
                $sentAt = $h->sent_at;
                $hour = (int) $sentAt->format('H');
                $isPM = $hour >= 12;
                $h12 = $hour % 12 ?: 12;
                return [
                    'name' => (string) ($h->recipient ?? '-'),
                    'room' => (string) ($h->room ?? '-'),
                    'date' => $sentAt->format('Y.n.j'),
                    'time' => ($isPM ? '오후' : '오전').' '.$h12.':'.$sentAt->format('i:s'),
                    'type' => (string) ($h->type ?? '일반'),
                    'typeClass' => $typeMap[$h->type] ?? 'type-notice',
                    'status' => (string) ($h->status ?? '발송 완료'),
                    'statusClass' => $h->status === '발송 실패' ? 'status-fail' : 'status-done',
                    'content' => (string) ($h->content ?? ''),
                    'isBroadcast' => false,
                ];
            })
            ->values()
            ->all();
    }

    // ── 입주자 목록 (입주자 관리 연동) ──
    #[Computed]
    public function residents(): array
    {
        $branchId = session('current_branch_id');
        if (! $branchId) {
            return [];
        }

        $tenants = Tenant::where('branch_id', $branchId)
            ->where('status', 'active')
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->orderByRaw('CAST(room_number AS UNSIGNED) ASC, room_number ASC')
            ->orderBy('name')
            ->get();

        $smsAll = SmsHistory::where('branch_id', $branchId)
            ->selectRaw('phone, COUNT(*) as cnt')
            ->groupBy('phone')->pluck('cnt', 'phone');
        $smsMonth = SmsHistory::where('branch_id', $branchId)
            ->where('sent_at', '>=', now()->startOfMonth())
            ->selectRaw('phone, COUNT(*) as cnt')
            ->groupBy('phone')->pluck('cnt', 'phone');

        return $tenants->map(function ($t) use ($smsAll, $smsMonth) {
            $cleanPhone = preg_replace('/\D/', '', (string) ($t->phone ?? ''));
            $isUnpaid = in_array($t->payment_status, ['pending', 'unpaid', 'overdue', 'waiting']);
            $isNew = $t->move_in_date && $t->move_in_date->gte(now()->subDays(30));
            if ($isUnpaid) {
                $badgeLabel = '미납';
                $badgeClass = 'msg-badge-warning';
                $filterKey = 'unpaid';
            } elseif ($isNew) {
                $badgeLabel = '신규 입주';
                $badgeClass = 'msg-badge-warning';
                $filterKey = 'new';
            } else {
                $badgeLabel = '정상 납부';
                $badgeClass = 'msg-badge-success';
                $filterKey = 'paid';
            }

            return [
                'name' => (string) $t->name,
                'room' => $t->room_number ? $t->room_number.'호' : '-',
                'phone' => (string) ($t->phone ?? ''),
                'badge_label' => $badgeLabel,
                'badge_class' => $badgeClass,
                'filter_key' => $filterKey,
                'total_count' => (int) ($smsAll[$cleanPhone] ?? 0),
                'month_count' => (int) ($smsMonth[$cleanPhone] ?? 0),
            ];
        })->values()->toArray();
    }

    // ── 발송 내역 테이블 ──
    public function table(Table $table): Table
    {
        $branchId = session('current_branch_id');

        return $table
            ->query(SmsHistory::query()->where('branch_id', $branchId))
            ->columns([
                TextColumn::make('sent_at')
                    ->label('발송 일시')
                    ->dateTime('Y.m.d H:i')
                    ->sortable(),
                TextColumn::make('recipient')
                    ->label('수신자')
                    ->searchable(),
                TextColumn::make('room')
                    ->label('호실'),
                TextColumn::make('content')
                    ->label('내용')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->content),
                TextColumn::make('type')
                    ->label('유형')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '결제' => 'warning',
                        '시설' => 'info',
                        '공지' => 'success',
                        '입퇴실' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->label('상태')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        '발송 완료' => 'success',
                        '발송 실패' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('cost')
                    ->label('비용'),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('유형')
                    ->options([
                        '결제' => '결제', '시설' => '시설', '공지' => '공지',
                        '입퇴실' => '입퇴실', '일반' => '일반',
                    ]),
                SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        '발송 완료' => '발송 완료',
                        '발송 실패' => '발송 실패',
                    ]),
            ])
            ->defaultSort('sent_at', 'desc')
            ->striped()
            ->paginated([5, 10, 25, 50])
            ->defaultPaginationPageOption(10);
    }

    // ── 템플릿 데이터 ──
    #[Computed]
    public function templates(): array
    {
        $typeMap = $this->getTypeMap();
        return $this->templatesQuery()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function ($t) use ($typeMap) {
                $info = $typeMap[$t->type] ?? $typeMap['general'];
                return [
                    'id' => $t->id,
                    'title' => $t->title,
                    'type' => $t->type,
                    'content' => $t->content,
                    'isFavorite' => $t->is_favorite,
                    'typeLabel' => $info['label'],
                    'badgeStyle' => $info['style'],
                ];
            })
            ->all();
    }

    // 메인 페이지에 표시할 4개 고정 템플릿
    #[Computed]
    public function featuredTemplates(): array
    {
        $featured = ['미납 안내', '시설 점검 안내', '긴급 공지', '공지사항'];
        $byTitle = collect($this->templates())->keyBy('title');
        return collect($featured)
            ->map(fn ($t) => $byTitle->get($t))
            ->filter()
            ->values()
            ->all();
    }

    // ── 새 템플릿 추가 ──
    public function openAddModal(): void
    {
        $this->tplTitle = '';
        $this->tplType = '';
        $this->tplContent = '';
        $this->showAddModal = true;
    }

    public function saveNewTemplate(): void
    {
        $this->validate([
            'tplTitle' => 'required|max:100',
            'tplType' => 'required',
            'tplContent' => 'required',
        ], [
            'tplTitle.required' => '제목을 입력해주세요.',
            'tplType.required' => '유형을 선택해주세요.',
            'tplContent.required' => '내용을 입력해주세요.',
        ]);

        $userId = auth()->id();
        $branchId = session('current_branch_id');
        if (! $userId || ! $branchId) {
            Notification::make()->title('지점 정보가 없습니다.')->danger()->send();
            return;
        }

        $nextSort = ((int) $this->templatesQuery()->max('sort_order')) + 1;

        SmsTemplate::create([
            'user_id' => $userId,
            'branch_id' => $branchId,
            'title' => $this->tplTitle,
            'type' => $this->tplType,
            'content' => $this->tplContent,
            'is_favorite' => false,
            'sort_order' => $nextSort,
        ]);

        unset($this->templates, $this->featuredTemplates);
        $this->showAddModal = false;

        Notification::make()->title('템플릿이 추가되었습니다.')->success()->send();
    }

    // ── 템플릿 수정 ──
    public function openEditModal(int $id): void
    {
        $tpl = $this->templatesQuery()->find($id);
        if (! $tpl) return;

        $this->editingTemplateId = $id;
        $this->tplTitle = $tpl->title;
        $this->tplType = $tpl->type;
        $this->tplContent = $tpl->content;
        $this->showEditModal = true;
    }

    public function saveEditTemplate(): void
    {
        $this->validate([
            'tplTitle' => 'required|max:100',
            'tplType' => 'required',
            'tplContent' => 'required',
        ]);

        $tpl = $this->templatesQuery()->find($this->editingTemplateId);
        if (! $tpl) {
            Notification::make()->title('템플릿을 찾을 수 없습니다.')->danger()->send();
            return;
        }

        $tpl->update([
            'title' => $this->tplTitle,
            'type' => $this->tplType,
            'content' => $this->tplContent,
        ]);

        unset($this->templates, $this->featuredTemplates);
        $this->showEditModal = false;

        Notification::make()->title('템플릿이 수정되었습니다.')->success()->send();
    }

    // ── 템플릿 삭제 ──
    public function deleteTemplate(int $id): void
    {
        $this->templatesQuery()->where('id', $id)->delete();
        unset($this->templates, $this->featuredTemplates);
        $this->showEditModal = false;

        Notification::make()->title('템플릿이 삭제되었습니다.')->success()->send();
    }

    // ── 템플릿으로 문자 발송 ──
    public function openUseModal(int $id): void
    {
        $tpl = $this->templatesQuery()->find($id);
        if (! $tpl) return;

        $typeMap = $this->getTypeMap();
        $this->sendRecipient = '전체 입주자';
        $this->sendType = $typeMap[$tpl->type]['label'] ?? '일반';
        $this->sendContent = $tpl->content;
        $this->showUseModal = true;
    }

    public function sendMessage(): void
    {
        $this->validate([
            'sendContent' => 'required',
            'sendPhone'   => 'required',
        ], [
            'sendContent.required' => '내용을 입력해주세요.',
            'sendPhone.required'   => '수신번호를 입력해주세요.',
        ]);

        $aligo = app(AligoService::class);
        $status = '발송 완료';
        $cost = '20원';

        try {
            $res = $aligo->send($this->sendPhone, $this->sendContent);
            $resultCode = (string) ($res['result_code'] ?? '');
            if ($resultCode !== '1') {
                $status = '발송 실패';
                Log::warning('aligo send failed', $res);
                Notification::make()
                    ->title('문자 발송 실패')
                    ->body($res['message'] ?? '알 수 없는 오류')
                    ->danger()->send();
            } else {
                Notification::make()
                    ->title('문자가 발송되었습니다.')
                    ->body('msg_id='.($res['msg_id'] ?? '-').' / 잔여건수='.($res['SMS_CNT'] ?? '-'))
                    ->success()->send();
            }
        } catch (\Throwable $e) {
            $status = '발송 실패';
            Log::error('aligo send exception', ['msg' => $e->getMessage()]);
            Notification::make()
                ->title('문자 발송 오류')
                ->body($e->getMessage())
                ->danger()->send();
        }

        SmsHistory::create([
            'branch_id' => session('current_branch_id'),
            'recipient' => $this->sendRecipient,
            'room' => '-',
            'phone' => preg_replace('/\D/', '', $this->sendPhone),
            'content' => $this->sendContent,
            'type' => $this->sendType,
            'status' => $status,
            'cost' => $cost,
            'sent_at' => now(),
        ]);

        $this->showUseModal = false;
    }

    public function sendFromModal(array $payload): void
    {
        $content = trim((string) ($payload['content'] ?? ''));
        $type = (string) ($payload['type'] ?? '일반');
        $recipients = $payload['recipients'] ?? [];

        if ($content === '') {
            Notification::make()->title('내용을 입력해주세요')->danger()->send();
            return;
        }
        if (empty($recipients)) {
            Notification::make()->title('수신자를 선택해주세요')->danger()->send();
            return;
        }

        $aligo = app(AligoService::class);
        $successCount = 0;
        $failCount = 0;
        $errors = [];

        foreach ($recipients as $r) {
            $name = (string) ($r['name'] ?? '-');
            $room = (string) ($r['room'] ?? '-');
            $phone = preg_replace('/\D/', '', (string) ($r['phone'] ?? ''));
            if ($phone === '') {
                $failCount++;
                $errors[] = $name.': 전화번호 없음';
                continue;
            }

            $personalContent = $this->replaceTemplateVars($content, $name, $room, $phone);

            $status = '발송 완료';
            try {
                $res = $aligo->send($phone, $personalContent);
                $code = (string) ($res['result_code'] ?? '');
                if ($code !== '1') {
                    $status = '발송 실패';
                    $errors[] = ($res['message'] ?? '알 수 없는 오류');
                    $failCount++;
                    Log::warning('aligo send failed', $res);
                } else {
                    $successCount++;
                }
            } catch (\Throwable $e) {
                $status = '발송 실패';
                $errors[] = $e->getMessage();
                $failCount++;
                Log::error('aligo send exception', ['msg' => $e->getMessage()]);
            }

            SmsHistory::create([
                'branch_id' => session('current_branch_id'),
                'recipient' => $name,
                'room' => $room,
                'phone' => $phone,
                'content' => $personalContent,
                'type' => $type,
                'status' => $status,
                'cost' => '20원',
                'sent_at' => now(),
            ]);
        }

        if ($failCount === 0) {
            Notification::make()
                ->title('문자가 발송되었습니다')
                ->body($successCount.'명에게 발송 완료')
                ->success()->send();
        } elseif ($successCount === 0) {
            Notification::make()
                ->title('문자 발송에 실패했습니다')
                ->body(implode("\n", array_slice(array_unique($errors), 0, 3)))
                ->danger()->send();
        } else {
            Notification::make()
                ->title('일부 발송 실패')
                ->body("성공 {$successCount}건 / 실패 {$failCount}건\n".implode("\n", array_slice(array_unique($errors), 0, 3)))
                ->warning()->send();
        }

        $this->resetTable();
    }

    private function replaceTemplateVars(string $content, string $name, string $room, string $phone): string
    {
        $branchId = session('current_branch_id');
        $cleanPhone = preg_replace('/\D/', '', $phone);
        $tenant = Tenant::where('branch_id', $branchId)
            ->get()
            ->first(fn ($t) => preg_replace('/\D/', '', (string) ($t->phone ?? '')) === $cleanPhone);

        $roomNumber = $tenant?->room_number ?: rtrim($room, '호');
        $unpaid = '0';
        if ($tenant && in_array($tenant->payment_status, ['pending', 'unpaid', 'overdue', 'waiting'])) {
            $unpaid = number_format((int) ($tenant->monthly_rent ?? 0));
        }

        $dueDate = '-';
        if ($tenant?->payment_due_day) {
            $dueDate = $tenant->payment_due_day.'일';
        } elseif ($tenant?->last_payment_date) {
            $dueDate = $tenant->last_payment_date->copy()->addMonth()->format('n월 j일');
        }

        return strtr($content, [
            '{이름}' => $name,
            '{호실번호}' => (string) $roomNumber,
            '{미납금액}' => $unpaid,
            '{납부일}' => $dueDate,
        ]);
    }

    public function checkRemain(): void
    {
        try {
            $res = app(AligoService::class)->remain();
            $code = (string) ($res['result_code'] ?? '');
            if ($code !== '1') {
                Notification::make()
                    ->title('잔량 조회 실패')
                    ->body($res['message'] ?? '알 수 없는 오류')
                    ->danger()->send();
                return;
            }
            $body = sprintf(
                'SMS %s건 / LMS %s건 / MMS %s건',
                $res['SMS_CNT'] ?? '0',
                $res['LMS_CNT'] ?? '0',
                $res['MMS_CNT'] ?? '0',
            );
            Notification::make()->title('알리고 잔여')->body($body)->success()->send();
        } catch (\Throwable $e) {
            Notification::make()->title('잔량 조회 오류')->body($e->getMessage())->danger()->send();
        }
    }

    // ── 헬퍼 ──
    private function getTypeMap(): array
    {
        return [
            'unpaid' => ['label' => '결제', 'style' => 'background:#fef3c7;color:#92400e;'],
            'facility' => ['label' => '시설', 'style' => 'background:#dbeafe;color:#1e40af;'],
            'notice' => ['label' => '공지', 'style' => 'background:#d1fae5;color:#065f46;'],
            'checkinout' => ['label' => '입퇴실', 'style' => 'background:#ede9fe;color:#6d28d9;'],
            'general' => ['label' => '일반', 'style' => 'background:#f3f4f6;color:#374151;'],
        ];
    }

    private function getDefaultTemplates(): array
    {
        return [
            ['id' => 1, 'title' => '미납 안내', 'type' => 'unpaid', 'content' => '안녕하세요, {이름}님. {호실번호}호 입실료 {미납금액}원이 아직 납부되지 않았습니다.'],
            ['id' => 2, 'title' => '시설 점검 안내', 'type' => 'facility', 'content' => '내일 오전 10시부터 시설 점검이 있습니다. 불편을 드려 죄송합니다. 협조 부탁드립니다.'],
            ['id' => 3, 'title' => '긴급 공지', 'type' => 'notice', 'content' => '긴급 공지입니다. 즉시 확인 부탁드립니다. 관리사무소로 연락 바랍니다.'],
            ['id' => 4, 'title' => '공지사항', 'type' => 'notice', 'content' => '공지사항이 있어 알려드립니다. 자세한 내용은 관리사무소로 문의해주세요.'],
            ['id' => 5, 'title' => '입실 안내', 'type' => 'checkinout', 'content' => '안녕하세요, {이름}님. {호실번호}호 입실을 환영합니다.'],
            ['id' => 6, 'title' => '퇴실 안내', 'type' => 'checkinout', 'content' => '안녕하세요, {이름}님. {호실번호}호 퇴실 처리가 완료되었습니다.'],
        ];
    }
}
