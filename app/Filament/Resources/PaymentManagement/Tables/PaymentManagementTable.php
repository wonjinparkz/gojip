<?php

namespace App\Filament\Resources\PaymentManagement\Tables;

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

class PaymentManagementTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // 1. 호실 번호
                Tables\Columns\TextColumn::make('room.room_number')
                    ->label('호실 번호')
                    ->sortable()
                    ->searchable(),

                // 2. 입주 상태
                Tables\Columns\TextColumn::make('process_status')
                    ->label('입주 상태')
                    ->formatStateUsing(function ($record) {
                        $statusLabel = $record->process_status_label;

                        $bgColor = match($record->process_status) {
                            'checked_in' => '#dcfce7',
                            'scheduled' => '#fef9c3',
                            'checked_out' => '#fee2e2',
                            default => '#f3f4f6',
                        };
                        $textColor = match($record->process_status) {
                            'checked_in' => '#166534',
                            'scheduled' => '#854d0e',
                            'checked_out' => '#991b1b',
                            default => '#4b5563',
                        };

                        return '<span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background-color: ' . $bgColor . '; color: ' . $textColor . '; white-space: nowrap;">' . e($statusLabel) . '</span>';
                    })
                    ->html(),

                // 3. 이름
                Tables\Columns\TextColumn::make('name')
                    ->label('이름')
                    ->searchable()
                    ->formatStateUsing(fn ($record) => '<span style="font-weight: 600;">' . e($record->name) . '</span>')
                    ->html(),

                // 4. 결제 금액
                Tables\Columns\TextColumn::make('room.monthly_rent')
                    ->label('결제 금액')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state) . '원' : '-')
                    ->placeholder('-'),

                // 5. 결제일
                Tables\Columns\TextColumn::make('payment_due_day')
                    ->label('결제일')
                    ->formatStateUsing(fn ($state) => $state ? $state . '일' : '-')
                    ->placeholder('-'),

                // 6. 실제 결제일
                Tables\Columns\TextColumn::make('actual_payment_date')
                    ->label('실제 결제일')
                    ->date('Y.m.d')
                    ->placeholder('-'),

                // 7. 결제 방법
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('결제 방법')
                    ->formatStateUsing(fn (string $state = null): string => match($state) {
                        'card' => '카드',
                        'transfer' => '계좌이체',
                        'cash' => '현금',
                        default => $state ?? '-',
                    })
                    ->placeholder('-'),

                // 8. 납부 상태
                Tables\Columns\TextColumn::make('payment_status')
                    ->label('납부 상태')
                    ->formatStateUsing(function ($record) {
                        $statusLabel = $record->payment_status_label;

                        $bgColor = match($record->payment_status) {
                            'paid' => '#dcfce7',
                            'overdue' => '#fee2e2',
                            'pending' => '#fef9c3',
                            'waiting' => '#f3f4f6',
                            default => '#f3f4f6',
                        };
                        $textColor = match($record->payment_status) {
                            'paid' => '#166534',
                            'overdue' => '#991b1b',
                            'pending' => '#854d0e',
                            'waiting' => '#4b5563',
                            default => '#4b5563',
                        };

                        return '<span style="display: inline-flex; align-items: center; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background-color: ' . $bgColor . '; color: ' . $textColor . '; white-space: nowrap;">' . e($statusLabel) . '</span>';
                    })
                    ->html(),
            ])
            ->filters([
                // 입주 상태 필터
                Tables\Filters\SelectFilter::make('process_status')
                    ->label('입주 상태')
                    ->options([
                        'checked_in' => '입실자',
                        'scheduled' => '입실예정',
                        'checked_out' => '퇴실자',
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
                        }

                        return $query;
                    }),

                // 납부 상태 필터
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('납부 상태')
                    ->options([
                        'paid' => '납부완료',
                        'pending' => '미납',
                        'overdue' => '연체',
                        'waiting' => '대기',
                    ]),

                // 결제 방법 필터
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('결제 방법')
                    ->options([
                        'card' => '카드',
                        'transfer' => '계좌이체',
                        'cash' => '현금',
                    ]),
            ])
            ->filtersFormColumns(1)
            ->actions([
                Action::make('edit')
                    ->label('수정')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->size('xs')
                    ->action(function ($record, $livewire) {
                        $livewire->dispatch('edit-payment', tenantId: $record->id);
                    }),
                Action::make('markPaid')
                    ->label('납부완료')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->size('xs')
                    ->requiresConfirmation()
                    ->modalHeading('납부 완료 처리')
                    ->modalDescription('납부 완료 처리하시겠습니까?')
                    ->modalSubmitActionLabel('확인')
                    ->action(function ($record) {
                        $record->update([
                            'payment_status' => 'paid',
                            'actual_payment_date' => now()->toDateString(),
                        ]);
                        Notification::make()
                            ->title('납부 완료 처리되었습니다')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->payment_status !== 'paid'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('markPaid')
                        ->label('납부완료 처리')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('납부 완료 처리')
                        ->modalDescription('선택한 입주자의 납부 상태를 완료로 처리하시겠습니까?')
                        ->modalSubmitActionLabel('확인')
                        ->action(function (Collection $records) {
                            $records->each(function ($record) {
                                $record->update([
                                    'payment_status' => 'paid',
                                    'actual_payment_date' => now()->toDateString(),
                                ]);
                            });
                            Notification::make()
                                ->title('선택한 입주자가 납부 완료 처리되었습니다')
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('room.room_number', 'asc');
    }
}
