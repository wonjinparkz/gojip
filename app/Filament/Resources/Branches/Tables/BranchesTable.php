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
                TextColumn::make('start_floor')
                    ->label('시작 층수')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('end_floor')
                    ->label('종료 층수')
                    ->numeric()
                    ->sortable(),
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
