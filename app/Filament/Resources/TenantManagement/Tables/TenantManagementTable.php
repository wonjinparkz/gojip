<?php

namespace App\Filament\Resources\TenantManagement\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TenantManagementTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('이름')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        if ($record->is_blacklisted) {
                            return '<div style="line-height: 1.3;">
                                        <div style="font-size: 0.7rem; color: #ef4444; font-weight: 500; margin-bottom: 0.125rem;">블랙리스트</div>
                                        <div style="color: #ef4444; font-weight: 500;">' . e($record->name) . '</div>
                                    </div>';
                        }
                        return e($record->name);
                    })
                    ->html()
                    ->action(
                        Action::make('edit')
                            ->action(function ($record, \Livewire\Component $livewire) {
                                $livewire->dispatch('edit-tenant-management', tenantId: $record->id);
                            })
                    ),
                Tables\Columns\TextColumn::make('phone')
                    ->label('연락처')
                    ->searchable()
                    ->color(fn ($record) => $record->is_blacklisted ? 'danger' : null),
                Tables\Columns\TextColumn::make('gender')
                    ->label('성별')
                    ->formatStateUsing(fn (string $state = null): string => match($state) {
                        'male' => '남성',
                        'female' => '여성',
                        default => '-',
                    })
                    ->placeholder('-')
                    ->color(fn ($record) => $record->is_blacklisted ? 'danger' : null),
                Tables\Columns\BadgeColumn::make('process_status')
                    ->label('입주자 상태')
                    ->getStateUsing(fn ($record) => $record->process_status_label)
                    ->colors([
                        'danger' => fn ($state, $record) => $record->is_blacklisted,
                        'success' => fn ($state, $record) => !$record->is_blacklisted && $state === '입실자',
                        'warning' => fn ($state, $record) => !$record->is_blacklisted && $state === '입실예정',
                        'gray' => fn ($state, $record) => !$record->is_blacklisted && $state === '대기',
                    ]),
                Tables\Columns\TextColumn::make('last_payment_date')
                    ->label('마지막 입금일')
                    ->date('Y.m.d')
                    ->placeholder('-')
                    ->sortable()
                    ->color(fn ($record) => $record->is_blacklisted ? 'danger' : null),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('결제 방법')
                    ->formatStateUsing(fn (string $state = null): string => match($state) {
                        'card' => '카드',
                        'transfer' => '계좌이체',
                        'cash' => '현금',
                        default => '-',
                    })
                    ->placeholder('-')
                    ->color(fn ($record) => $record->is_blacklisted ? 'danger' : null),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('납부 상태')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'paid' => '납부완료',
                        'overdue' => '연체',
                        'pending' => '미납',
                        'waiting' => '대기',
                        default => $state,
                    })
                    ->colors([
                        'danger' => fn ($state, $record) => $record->is_blacklisted || $state === 'overdue',
                        'success' => fn ($state, $record) => !$record->is_blacklisted && $state === 'paid',
                        'warning' => fn ($state, $record) => !$record->is_blacklisted && $state === 'pending',
                        'gray' => fn ($state, $record) => !$record->is_blacklisted && $state === 'waiting',
                    ]),
                Tables\Columns\IconColumn::make('is_blacklisted')
                    ->label('블랙리스트')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->hidden(), // 블랙리스트 컬럼 숨기기
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->label('성별')
                    ->options([
                        'male' => '남성',
                        'female' => '여성',
                    ]),
                Tables\Filters\SelectFilter::make('process_status')
                    ->label('입주자 상태')
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
                                // 입실 완료했고, 퇴실 완료나 청소 중이 아닌 경우
                                return $query->whereHas('room', function (Builder $q) {
                                    $q->whereNotNull('check_in_completed_at')
                                      ->whereNull('check_out_completed_at')
                                      ->whereNull('cleaning_status');
                                });
                            case 'checked_out':
                                // 퇴실 완료했거나 청소 대기/완료인 경우
                                return $query->whereHas('room', function (Builder $q) {
                                    $q->where(function ($q2) {
                                        $q2->whereNotNull('check_out_completed_at')
                                           ->orWhereIn('cleaning_status', ['waiting', 'completed']);
                                    });
                                });
                            case 'scheduled':
                                // 입실 예정 (입실 완료 안됨)
                                return $query->whereHas('room', function (Builder $q) {
                                    $q->whereNull('check_in_completed_at');
                                });
                            case 'pending':
                                // 대기 (room_id가 없거나, 입실/퇴실 완료 안됨)
                                return $query->where(function (Builder $q) {
                                    $q->whereNull('room_id')
                                      ->orWhereHas('room', function (Builder $q2) {
                                          $q2->whereNull('check_in_completed_at')
                                             ->whereNull('check_out_completed_at')
                                             ->whereNull('cleaning_status');
                                      });
                                });
                        }

                        return $query;
                    }),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('납부 상태')
                    ->options([
                        'paid' => '납부완료',
                        'pending' => '미납',
                        'overdue' => '연체',
                        'waiting' => '대기',
                    ]),
                Tables\Filters\TernaryFilter::make('is_blacklisted')
                    ->label('블랙리스트')
                    ->placeholder('전체')
                    ->trueLabel('블랙리스트만')
                    ->falseLabel('정상만'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
