<?php

namespace App\Filament\Resources\Branches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('지점명')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('address')
                    ->label('주소')
                    ->searchable()
                    ->limit(30),
                TextColumn::make('phone')
                    ->label('전화번호')
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        if (empty($state)) {
                            return '-';
                        }
                        // 콤마로 구분된 전화번호를 줄바꿈으로 표시
                        return str_replace(',', ' / ', $state);
                    }),
                TextColumn::make('rooms_count')
                    ->label('호실 수')
                    ->counts('rooms')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('생성일')
                    ->dateTime('Y.m.d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
