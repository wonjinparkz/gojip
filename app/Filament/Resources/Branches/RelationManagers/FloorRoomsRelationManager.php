<?php

namespace App\Filament\Resources\Branches\RelationManagers;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FloorRoomsRelationManager extends RelationManager
{
    protected static string $relationship = 'floorRooms';

    protected static ?string $title = '층별 구성';

    protected static ?string $modelLabel = '층';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('floor_number')
                    ->label('층수')
                    ->disabled() // Should not change floor number
                    ->required(),
                Forms\Components\TextInput::make('room_type')
                    ->label('방 타입')
                    ->required()
                    ->default('Standard'),
                Forms\Components\TextInput::make('monthly_rent')
                    ->label('월세')
                    ->numeric()
                    ->required()
                    ->default(0),
                Forms\Components\TextInput::make('room_count')
                    ->label('방 개수')
                    ->numeric()
                    ->required()
                    ->default(1),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('floor_number')
            ->columns([
                Tables\Columns\TextColumn::make('floor_number')
                    ->label('층수')
                    ->formatStateUsing(fn (string $state): string => $state . '층'),
                Tables\Columns\TextColumn::make('room_type')->label('방 타입'),
                Tables\Columns\TextColumn::make('monthly_rent')->label('월세')->money('krw'),
                Tables\Columns\TextColumn::make('room_count')->label('방 개수'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // No create action needed as they are auto-generated
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('수정'),
            ])
            ->bulkActions([
                //
            ]);
    }
}
