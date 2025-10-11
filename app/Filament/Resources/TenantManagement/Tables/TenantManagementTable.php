<?php

namespace App\Filament\Resources\TenantManagement\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
                    ->sortable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('연락처')
                    ->searchable(),
                Tables\Columns\TextColumn::make('gender')
                    ->label('성별')
                    ->formatStateUsing(fn (string $state = null): string => match($state) {
                        'male' => '남성',
                        'female' => '여성',
                        default => '-',
                    })
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('last_payment_date')
                    ->label('마지막 입금일')
                    ->date('Y.m.d')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('결제 방법')
                    ->formatStateUsing(fn (string $state = null): string => match($state) {
                        'card' => '카드',
                        'transfer' => '계좌이체',
                        'cash' => '현금',
                        default => '-',
                    })
                    ->placeholder('-'),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('납부 상태')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'paid' => '납부완료',
                        'overdue' => '연체',
                        'pending' => '미납',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'paid',
                        'danger' => 'overdue',
                        'warning' => 'pending',
                    ]),
                Tables\Columns\IconColumn::make('is_blacklisted')
                    ->label('블랙리스트')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('gender')
                    ->label('성별')
                    ->options([
                        'male' => '남성',
                        'female' => '여성',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('납부 상태')
                    ->options([
                        'paid' => '납부완료',
                        'pending' => '미납',
                        'overdue' => '연체',
                    ]),
                Tables\Filters\TernaryFilter::make('is_blacklisted')
                    ->label('블랙리스트')
                    ->placeholder('전체')
                    ->trueLabel('블랙리스트만')
                    ->falseLabel('정상만'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
