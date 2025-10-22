<?php

namespace App\Filament\Resources\RoomResource\Pages;

use App\Filament\Resources\RoomResource;
use App\Models\Branch;
use App\Models\Room;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;

class ListRooms extends Page implements HasForms
{
    use InteractsWithForms;
    protected static string $resource = RoomResource::class;

    protected string $view = 'filament.resources.room-resource.pages.list-rooms';

    public ?int $selectedBranchId = null;
    public ?Room $selectedRoom = null;
    public bool $showViewModal = false;
    public bool $showEditModal = false;
    public bool $editMode = false;

    // Filters
    public $filterFloor = '';
    public $filterRoomType = '';
    public $filterStatus = '';

    public $branch_id = null;
    public $room_number = null;
    public $floor = null;
    public $room_type = null;
    public $monthly_rent = null;
    public $deposit = null;
    public $status = null;
    public $move_in_date = null;
    public $move_out_date = null;
    public $tenant_name = null;

    // Edit mode properties
    public $editFloor = null;
    public $editRoomNumber = null;
    public $editMonthlyRent = null;
    public $editDeposit = null;
    public $editRoomType = null;
    public $editStatus = null;

    public function mount(): void
    {
        // 세션에서 선택된 지점 가져오기, 없으면 첫 번째 지점을 기본 선택
        $branches = Branch::where('user_id', auth()->id())->get();
        $this->selectedBranchId = session('current_branch_id', $branches->first()?->id);

        // 세션에 저장되지 않았다면 저장
        if (!session()->has('current_branch_id') && $this->selectedBranchId) {
            session(['current_branch_id' => $this->selectedBranchId]);
        }
    }

    public function getHeading(): string
    {
        $branchId = session('current_branch_id', $this->selectedBranchId);
        $branch = Branch::find($branchId);

        return $branch ? "{$branch->name} 호실 관리" : '호실 관리';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Action::make('createSingle')
                    ->label('호실 하나씩 추가하기')
                    ->icon('heroicon-o-plus')
                    ->form([
                        Forms\Components\TextInput::make('room_number')
                            ->label('호실 번호')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('예: 201'),
                        Forms\Components\TextInput::make('floor')
                            ->label('층')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->placeholder('예: 2'),
                        Forms\Components\TextInput::make('room_type')
                            ->label('방 유형')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('예: 스탠다드룸'),
                        Forms\Components\TextInput::make('monthly_rent')
                            ->label('월세')
                            ->required()
                            ->prefix('₩')
                            ->placeholder('600,000')
                            ->extraInputAttributes([
                                'x-mask:dynamic' => '$money($input)',
                            ])
                            ->dehydrateStateUsing(fn ($state) => $state ? (int) str_replace(',', '', $state) : null),
                        Forms\Components\TextInput::make('deposit')
                            ->label('보증금')
                            ->required()
                            ->prefix('₩')
                            ->default(0)
                            ->placeholder('0')
                            ->extraInputAttributes([
                                'x-mask:dynamic' => '$money($input)',
                            ])
                            ->dehydrateStateUsing(fn ($state) => $state ? (int) str_replace(',', '', $state) : null),
                    ])
                    ->action(function (array $data) {
                        $branchId = session('current_branch_id', $this->selectedBranchId);

                        if (!$branchId) {
                            Notification::make()
                                ->title('지점을 선택해주세요')
                                ->danger()
                                ->send();
                            return;
                        }

                        Room::create([
                            'branch_id' => $branchId,
                            'room_number' => $data['room_number'],
                            'floor' => $data['floor'],
                            'room_type' => $data['room_type'],
                            'monthly_rent' => $data['monthly_rent'],
                            'deposit' => $data['deposit'],
                            'status' => 'available',
                        ]);

                        Notification::make()
                            ->title('호실이 생성되었습니다')
                            ->success()
                            ->send();
                    })
                    ->modalWidth(Width::Large),
                Action::make('createMultiple')
                    ->label('호실 여러 개 추가하기')
                    ->icon('heroicon-o-plus-circle')
                    ->form([
                        Forms\Components\TextInput::make('start_floor')
                            ->label('시작 층')
                            ->required()
                            ->numeric()
                            ->default(1)
                            ->placeholder('예: 2'),
                        Forms\Components\TextInput::make('end_floor')
                            ->label('끝 층')
                            ->required()
                            ->numeric()
                            ->placeholder('예: 5'),
                        Forms\Components\TextInput::make('rooms_per_floor')
                            ->label('층당 방 개수')
                            ->required()
                            ->numeric()
                            ->default(10)
                            ->placeholder('예: 10'),
                        Forms\Components\TextInput::make('room_type')
                            ->label('방 유형')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('예: 스탠다드룸'),
                        Forms\Components\TextInput::make('monthly_rent')
                            ->label('월세')
                            ->required()
                            ->prefix('₩')
                            ->placeholder('600,000')
                            ->extraInputAttributes([
                                'x-mask:dynamic' => '$money($input)',
                            ])
                            ->dehydrateStateUsing(fn ($state) => $state ? (int) str_replace(',', '', $state) : null),
                        Forms\Components\TextInput::make('deposit')
                            ->label('보증금')
                            ->required()
                            ->prefix('₩')
                            ->default(0)
                            ->placeholder('0')
                            ->extraInputAttributes([
                                'x-mask:dynamic' => '$money($input)',
                            ])
                            ->dehydrateStateUsing(fn ($state) => $state ? (int) str_replace(',', '', $state) : null),
                    ])
                    ->action(function (array $data) {
                        $branchId = session('current_branch_id', $this->selectedBranchId);

                        if (!$branchId) {
                            Notification::make()
                                ->title('지점을 선택해주세요')
                                ->danger()
                                ->send();
                            return;
                        }

                        $startFloor = (int) $data['start_floor'];
                        $endFloor = (int) $data['end_floor'];
                        $roomsPerFloor = (int) $data['rooms_per_floor'];

                        if ($startFloor > $endFloor) {
                            Notification::make()
                                ->title('시작 층이 끝 층보다 클 수 없습니다')
                                ->danger()
                                ->send();
                            return;
                        }

                        $createdCount = 0;
                        for ($floor = $startFloor; $floor <= $endFloor; $floor++) {
                            for ($roomNum = 1; $roomNum <= $roomsPerFloor; $roomNum++) {
                                $roomNumber = $floor . str_pad($roomNum, 2, '0', STR_PAD_LEFT);

                                Room::create([
                                    'branch_id' => $branchId,
                                    'room_number' => $roomNumber,
                                    'floor' => $floor,
                                    'room_type' => $data['room_type'],
                                    'monthly_rent' => $data['monthly_rent'],
                                    'deposit' => $data['deposit'],
                                    'status' => 'available',
                                ]);
                                $createdCount++;
                            }
                        }

                        Notification::make()
                            ->title("{$createdCount}개의 호실이 생성되었습니다")
                            ->success()
                            ->send();
                    })
                    ->modalWidth(Width::Large),
            ])
                ->label('방 추가하기')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->button(),
        ];
    }

    public function getRooms()
    {
        // 현재 세션의 지점 ID를 다시 가져오기 (하단 바에서 변경될 수 있음)
        $this->selectedBranchId = session('current_branch_id', $this->selectedBranchId);

        $query = Room::with('branch');

        if ($this->selectedBranchId) {
            $query->where('branch_id', $this->selectedBranchId);
        }

        // Apply filters
        if ($this->filterFloor !== '') {
            $query->where('floor', $this->filterFloor);
        }

        if ($this->filterRoomType !== '') {
            $query->where('room_type', $this->filterRoomType);
        }

        if ($this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        return $query->paginate(12);
    }

    public function getAvailableFloors()
    {
        $this->selectedBranchId = session('current_branch_id', $this->selectedBranchId);

        $query = Room::query();

        if ($this->selectedBranchId) {
            $query->where('branch_id', $this->selectedBranchId);
        }

        return $query->distinct()->pluck('floor')->sort()->values();
    }

    public function getAvailableRoomTypes()
    {
        $this->selectedBranchId = session('current_branch_id', $this->selectedBranchId);

        $query = Room::query();

        if ($this->selectedBranchId) {
            $query->where('branch_id', $this->selectedBranchId);
        }

        return $query->distinct()->pluck('room_type')->sort()->values();
    }

    public function viewRoom($roomId): void
    {
        $this->selectedRoom = Room::with('branch')->find($roomId);
        $this->showViewModal = true;
    }

    public function openEditModal($roomId = null): void
    {
        if ($this->selectedRoom) {
            // Edit mode 속성에 데이터 채우기
            $this->editFloor = $this->selectedRoom->floor;
            $this->editRoomNumber = $this->selectedRoom->room_number;
            $this->editMonthlyRent = $this->selectedRoom->monthly_rent;
            $this->editDeposit = $this->selectedRoom->deposit;
            $this->editRoomType = $this->selectedRoom->room_type;
            $this->editStatus = $this->selectedRoom->status;

            $this->editMode = true;
        }
    }

    public function closeModal(): void
    {
        $this->showViewModal = false;
        $this->showEditModal = false;
        $this->editMode = false;
        $this->selectedRoom = null;

        // 폼 필드 초기화
        $this->branch_id = null;
        $this->room_number = null;
        $this->floor = null;
        $this->room_type = null;
        $this->monthly_rent = null;
        $this->deposit = null;
        $this->status = null;
        $this->move_in_date = null;
        $this->move_out_date = null;
        $this->tenant_name = null;

        // Edit mode 필드 초기화
        $this->editFloor = null;
        $this->editRoomNumber = null;
        $this->editMonthlyRent = null;
        $this->editDeposit = null;
        $this->editRoomType = null;
        $this->editStatus = null;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('branch_id')
                    ->label('지점')
                    ->options(Branch::where('user_id', auth()->id())->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('room_number')
                    ->label('호실 번호')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('예: 201, 202'),
                Forms\Components\TextInput::make('floor')
                    ->label('층수')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('room_type')
                    ->label('호실 타입')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('예: 스탠다드룸'),
                Forms\Components\TextInput::make('monthly_rent')
                    ->label('월세')
                    ->required()
                    ->numeric()
                    ->prefix('₩')
                    ->placeholder('600000'),
                Forms\Components\TextInput::make('deposit')
                    ->label('보증금')
                    ->required()
                    ->numeric()
                    ->prefix('₩')
                    ->default(0)
                    ->placeholder('0'),
                Forms\Components\Select::make('status')
                    ->label('상태')
                    ->required()
                    ->options([
                        'available' => '입주가능',
                        'occupied' => '입주중',
                        'maintenance' => '수리중',
                    ])
                    ->default('available'),
                Forms\Components\DatePicker::make('move_in_date')
                    ->label('입주일')
                    ->displayFormat('Y년 m월 d일'),
                Forms\Components\DatePicker::make('move_out_date')
                    ->label('퇴실일')
                    ->displayFormat('Y년 m월 d일'),
                Forms\Components\TextInput::make('tenant_name')
                    ->label('입주자명')
                    ->maxLength(255)
                    ->placeholder('입주자 이름'),
            ]);
    }

    public function saveRoom(): void
    {
        if ($this->selectedRoom && $this->editMode) {
            $this->selectedRoom->update([
                'floor' => $this->editFloor,
                'room_number' => $this->editRoomNumber,
                'monthly_rent' => $this->editMonthlyRent,
                'deposit' => $this->editDeposit,
                'room_type' => $this->editRoomType,
                'status' => $this->editStatus,
            ]);

            Notification::make()
                ->title('호실 정보가 수정되었습니다')
                ->success()
                ->send();

            // 업데이트된 데이터 다시 로드
            $this->selectedRoom = Room::with('branch')->find($this->selectedRoom->id);
            $this->editMode = false;
        }
    }

    public function cancelEdit(): void
    {
        $this->editMode = false;

        // Edit mode 필드 초기화
        $this->editFloor = null;
        $this->editRoomNumber = null;
        $this->editMonthlyRent = null;
        $this->editDeposit = null;
        $this->editRoomType = null;
        $this->editStatus = null;
    }
}
