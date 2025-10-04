<?php

namespace App\Filament\Resources\Branches\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class BranchForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('지점 정보')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('지점명')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('start_floor')
                            ->label('시작 층수')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                        Forms\Components\TextInput::make('end_floor')
                            ->label('종료 층수')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->gte('start_floor'),
                    ])->columns(3),
            ]);
    }
}
