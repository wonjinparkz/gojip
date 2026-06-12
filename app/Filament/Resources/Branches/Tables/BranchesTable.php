<?php

namespace App\Filament\Resources\Branches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\Action;
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
                TextColumn::make('business_number')
                    ->label('사업자번호')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state
                        ? preg_replace('/^(\d{3})(\d{2})(\d{5})$/', '$1-$2-$3', $state)
                        : '-')
                    ->toggleable(),
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
                TextColumn::make('start_floor')
                    ->label('시작 층수')
                    ->sortable(),
                TextColumn::make('end_floor')
                    ->label('종료 층수')
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
            ->recordAction(null) // 행 클릭 시 기본 동작 비활성화
            ->recordUrl(null) // 행 클릭 URL 비활성화
            ->recordActions([
                Action::make('edit')
                    ->label('수정')
                    ->icon('heroicon-o-pencil')
                    ->action(fn () => null)
                    ->extraAttributes(fn ($record) => [
                        'onclick' => "openEditBranchModal({$record->id}); return false;",
                    ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
