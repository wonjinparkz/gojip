<?php

namespace App\Filament\Resources\TenantManagement\Tables;

use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class TenantManagementTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. 입주 상태
                Tables\Columns\TextColumn::make('process_status')
                    ->label('입주 상태')
                    ->formatStateUsing(function ($record) {
                        $statusLabel = $record->process_status_label;

                        // 퇴실 필요 여부 확인
                        $needsCheckout = false;
                        if ($record->process_status === 'checked_in' && $record->room?->move_out_date) {
                            $needsCheckout = $record->room->move_out_date->isPast();
                        }

                        if ($needsCheckout) {
                            return '<div style="display: inline-flex; flex-direction: column; align-items: center;">
                                <span style="font-size: 0.6rem; color: #dc2626; font-weight: 600; line-height: 1; margin-bottom: 2px;">퇴실 예정</span>
                                <span style="display: inline-flex; align-items: center; padding: 0 0.375rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 500; background-color: #dc2626; color: #ffffff; white-space: nowrap;">' . e($statusLabel) . '</span>
                            </div>';
                        }

                        // 일반 상태 배지
                        $bgColor = match(true) {
                            $record->is_blacklisted => '#fee2e2',
                            $record->process_status === 'checked_in' => '#dcfce7',
                            $record->process_status === 'scheduled' => '#fef9c3',
                            $record->process_status === 'checked_out' => '#f3f4f6',
                            default => '#f3f4f6',
                        };
                        $textColor = match(true) {
                            $record->is_blacklisted => '#991b1b',
                            $record->process_status === 'checked_in' => '#166534',
                            $record->process_status === 'scheduled' => '#854d0e',
                            $record->process_status === 'checked_out' => '#374151',
                            default => '#4b5563',
                        };

                        return '<span style="display: inline-flex; align-items: center; padding: 0 0.375rem; border-radius: 9999px; font-size: 0.65rem; font-weight: 500; background-color: ' . $bgColor . '; color: ' . $textColor . '; white-space: nowrap;">' . e($statusLabel) . '</span>';
                    })
                    ->html(),

                // 2. 이름
                Tables\Columns\TextColumn::make('name')
                    ->label('이름')
                    ->searchable()
                    ->formatStateUsing(function ($record) {
                        if ($record->is_blacklisted) {
                            return '<span style="color: #ef4444; font-weight: 600;">' . e($record->name) . '</span>';
                        }
                        return '<span style="font-weight: 600;">' . e($record->name) . '</span>';
                    })
                    ->html(),

                // 3. 성별
                Tables\Columns\TextColumn::make('gender')
                    ->label('성별')
                    ->formatStateUsing(fn (string $state = null): string => match($state) {
                        'male' => '남성',
                        'female' => '여성',
                        default => '-',
                    })
                    ->placeholder('-')
                    ->color(fn ($record) => $record->is_blacklisted ? 'danger' : null)
                    ->visibleFrom('md'),

                // 4. 연락처
                Tables\Columns\TextColumn::make('phone')
                    ->label('연락처')
                    ->searchable()
                    ->color(fn ($record) => $record->is_blacklisted ? 'danger' : null)
                    ->visibleFrom('md'),

                // 5. 호실 번호
                Tables\Columns\TextColumn::make('room.room_number')
                    ->label('호실 번호')
                    ->placeholder('-')
                    ->color(fn ($record) => $record->is_blacklisted ? 'danger' : null),

                // 6. 호실 유형
                Tables\Columns\TextColumn::make('room.room_type')
                    ->label('호실 유형')
                    ->formatStateUsing(fn (string $state = null): string => match($state) {
                        'single' => '1인실',
                        'double' => '2인실',
                        'triple' => '3인실',
                        'quad' => '4인실',
                        default => $state ?? '-',
                    })
                    ->placeholder('-')
                    ->color(fn ($record) => $record->is_blacklisted ? 'danger' : null)
                    ->visibleFrom('md'),

                // 7. 창 구조
                Tables\Columns\TextColumn::make('room.window_structure')
                    ->label('창 구조')
                    ->formatStateUsing(fn (string $state = null): string => match($state) {
                        'with_window' => '창문있음',
                        'without_window' => '창문없음',
                        default => $state ?? '-',
                    })
                    ->placeholder('-')
                    ->color(fn ($record) => $record->is_blacklisted ? 'danger' : null)
                    ->visibleFrom('md'),

                // 8. 호실 타입
                Tables\Columns\TextColumn::make('room.room_category')
                    ->label('호실 타입')
                    ->formatStateUsing(fn (string $state = null): string => match($state) {
                        'standard' => '일반',
                        'premium' => '프리미엄',
                        'deluxe' => '디럭스',
                        default => $state ?? '-',
                    })
                    ->placeholder('-')
                    ->color(fn ($record) => $record->is_blacklisted ? 'danger' : null)
                    ->visibleFrom('md'),

                // 9. 월 입실료
                Tables\Columns\TextColumn::make('room.monthly_rent')
                    ->label('월 입실료')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state) . '원' : '-')
                    ->placeholder('-')
                    ->color(fn ($record) => $record->is_blacklisted ? 'danger' : null)
                    ->visibleFrom('md'),

                // 10. 입실일
                Tables\Columns\TextColumn::make('room.move_in_date')
                    ->label('입실일')
                    ->date('Y.m.d')
                    ->placeholder('-')
                    ->sortable()
                    ->color(fn ($record) => $record->is_blacklisted ? 'danger' : null)
                    ->visibleFrom('md'),

                // 11. 퇴실일
                Tables\Columns\TextColumn::make('room.move_out_date')
                    ->label('퇴실일')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state) return '-';

                        $moveOutDate = $record->room?->move_out_date;
                        if (!$moveOutDate) return '-';

                        $dateStr = $moveOutDate->format('Y.m.d');

                        // 퇴실 필요 여부 확인 (입실자이면서 퇴실일이 지난 경우)
                        $needsCheckout = false;
                        if ($record->process_status === 'checked_in' && $moveOutDate->isPast()) {
                            $needsCheckout = true;
                            $overdueDays = (int) $moveOutDate->diffInDays(now());
                            return '<span style="color: #dc2626; font-weight: 500;">' . $dateStr . ' <span style="font-size: 0.75rem; font-weight: 600;">(+' . $overdueDays . '일)</span></span>';
                        }

                        if ($record->is_blacklisted) {
                            return '<span style="color: #dc2626;">' . $dateStr . '</span>';
                        }

                        return $dateStr;
                    })
                    ->html()
                    ->placeholder('-')
                    ->sortable()
                    ->visibleFrom('md'),

                // 12. 블랙리스트 등록 여부
                Tables\Columns\TextColumn::make('is_blacklisted')
                    ->label('블랙리스트')
                    ->getStateUsing(fn ($record) => $record->is_blacklisted)
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->is_blacklisted) {
                            return '<span style="display: inline-flex; align-items: center; gap: 0.25rem; color: #dc2626; font-weight: 500;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#dc2626" style="width: 1rem; height: 1rem;"><path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd" /></svg>등록</span>';
                        }
                        return '<span style="color: #6b7280;">미등록</span>';
                    })
                    ->html()
                    ->visibleFrom('md'),
            ])
            ->filters([
                // 1. 입주 상태 필터
                Tables\Filters\SelectFilter::make('process_status')
                    ->label('입주 상태')
                    ->options([
                        'checked_in' => '입실자',
                        'checked_out' => '퇴실자',
                        'scheduled' => '입실예정',
                        'pending' => '대기',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!isset($data['value'])) {
                            return $query;
                        }

                        $status = $data['value'];

                        switch ($status) {
                            case 'checked_in':
                                return $query->whereNotNull('room_id')
                                    ->whereExists(function ($q) {
                                        $q->selectRaw(1)
                                          ->from('rooms')
                                          ->whereColumn('rooms.id', 'tenants.room_id')
                                          ->whereNotNull('rooms.check_in_completed_at')
                                          ->whereNull('rooms.check_out_completed_at')
                                          ->whereNull('rooms.cleaning_status');
                                    });
                            case 'checked_out':
                                return $query->whereNotNull('room_id')
                                    ->whereExists(function ($q) {
                                        $q->selectRaw(1)
                                          ->from('rooms')
                                          ->whereColumn('rooms.id', 'tenants.room_id')
                                          ->where(function ($q2) {
                                              $q2->whereNotNull('rooms.check_out_completed_at')
                                                 ->orWhereIn('rooms.cleaning_status', ['waiting', 'completed']);
                                          });
                                    });
                            case 'scheduled':
                                return $query->whereNotNull('room_id')
                                    ->whereExists(function ($q) {
                                        $today = now()->format('Y-m-d');
                                        $q->selectRaw(1)
                                          ->from('rooms')
                                          ->whereColumn('rooms.id', 'tenants.room_id')
                                          ->whereNotNull('rooms.move_in_date')
                                          ->whereDate('rooms.move_in_date', '>=', $today)
                                          ->whereNull('rooms.check_in_completed_at')
                                          ->whereNull('rooms.check_out_completed_at')
                                          ->whereNull('rooms.cleaning_status');
                                    });
                            case 'pending':
                                return $query->whereNull('room_id');
                        }

                        return $query;
                    }),

                // 2. 호실 유형 필터
                Tables\Filters\SelectFilter::make('room_type')
                    ->label('호실 유형')
                    ->options([
                        'single' => '1인실',
                        'double' => '2인실',
                        'triple' => '3인실',
                        'quad' => '4인실',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!isset($data['value'])) {
                            return $query;
                        }

                        return $query->whereHas('room', function (Builder $q) use ($data) {
                            $q->where('room_type', $data['value']);
                        });
                    }),

                // 3. 창 구조 필터
                Tables\Filters\SelectFilter::make('window_structure')
                    ->label('창 구조')
                    ->options([
                        'with_window' => '창문있음',
                        'without_window' => '창문없음',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!isset($data['value'])) {
                            return $query;
                        }

                        return $query->whereHas('room', function (Builder $q) use ($data) {
                            $q->where('window_structure', $data['value']);
                        });
                    }),

                // 4. 호실 타입 필터
                Tables\Filters\SelectFilter::make('room_category')
                    ->label('호실 타입')
                    ->options([
                        'standard' => '일반',
                        'premium' => '프리미엄',
                        'deluxe' => '디럭스',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (!isset($data['value'])) {
                            return $query;
                        }

                        return $query->whereHas('room', function (Builder $q) use ($data) {
                            $q->where('room_category', $data['value']);
                        });
                    }),

                // 5. 성별 필터
                Tables\Filters\SelectFilter::make('gender')
                    ->label('성별')
                    ->options([
                        'male' => '남성',
                        'female' => '여성',
                    ]),

                // 6. 월 입실료 필터 (범위)
                Tables\Filters\Filter::make('monthly_rent')
                    ->form([
                        Forms\Components\TextInput::make('monthly_rent_from')
                            ->label('월 입실료')
                            ->placeholder('최소')
                            ->numeric()
                            ->suffix('~'),
                        Forms\Components\TextInput::make('monthly_rent_to')
                            ->label("\u{00A0}")
                            ->placeholder('최대')
                            ->numeric()
                            ->suffix('원'),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['monthly_rent_from'],
                                fn (Builder $query, $amount): Builder => $query->whereHas('room', function (Builder $q) use ($amount) {
                                    $q->where('monthly_rent', '>=', $amount);
                                })
                            )
                            ->when(
                                $data['monthly_rent_to'],
                                fn (Builder $query, $amount): Builder => $query->whereHas('room', function (Builder $q) use ($amount) {
                                    $q->where('monthly_rent', '<=', $amount);
                                })
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['monthly_rent_from'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('최소 금액: ' . number_format($data['monthly_rent_from']) . '원')
                                ->removeField('monthly_rent_from');
                        }

                        if ($data['monthly_rent_to'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('최대 금액: ' . number_format($data['monthly_rent_to']) . '원')
                                ->removeField('monthly_rent_to');
                        }

                        return $indicators;
                    }),

                // 7. 납부 상태 필터
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('납부 상태')
                    ->options([
                        'paid' => '납부완료',
                        'pending' => '미납',
                        'overdue' => '연체',
                        'waiting' => '대기',
                    ]),

                // 8. 블랙리스트 필터
                Tables\Filters\TernaryFilter::make('is_blacklisted')
                    ->label('블랙리스트')
                    ->placeholder('전체')
                    ->trueLabel('블랙리스트만')
                    ->falseLabel('정상만'),
            ])
            ->filtersFormColumns(1)
            ->actions([
                Action::make('contact')
                    ->label('연락')
                    ->icon('heroicon-o-phone')
                    ->color('gray')
                    ->size('xs')
                    ->url(fn ($record) => $record->phone ? 'tel:' . $record->phone : null)
                    ->openUrlInNewTab(false)
                    ->visible(fn ($record) => !empty($record->phone)),
                Action::make('checkout')
                    ->label('퇴실')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('danger')
                    ->size('xs')
                    ->requiresConfirmation()
                    ->modalHeading('퇴실 완료 처리')
                    ->modalDescription('퇴실 완료 처리하시겠습니까?')
                    ->modalSubmitActionLabel('확인')
                    ->action(function ($record, $livewire) {
                        $livewire->dispatch('complete-checkout-confirm', tenantId: $record->id);
                    })
                    ->visible(function ($record) {
                        if ($record->process_status === 'checked_in' && $record->room?->move_out_date) {
                            return $record->room->move_out_date->isPast();
                        }
                        return false;
                    }),
                Action::make('edit')
                    ->label('수정')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->size('xs')
                    ->action(function ($record, $livewire) {
                        $livewire->dispatch('edit-tenant-management', tenantId: $record->id);
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->label('선택 삭제')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('입주자 삭제')
                        ->modalDescription('선택한 입주자를 삭제하시겠습니까? 이 작업은 되돌릴 수 없습니다.')
                        ->modalSubmitActionLabel('삭제')
                        ->action(function (Collection $records) {
                            $records->each->delete();
                            Notification::make()
                                ->title('선택한 입주자가 삭제되었습니다')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
