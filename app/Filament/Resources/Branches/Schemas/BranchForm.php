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
                        Forms\Components\TextInput::make('address')
                            ->label('주소')
                            ->maxLength(255),
                        Forms\Components\Repeater::make('phone_numbers')
                            ->label('전화번호')
                            ->schema([
                                Forms\Components\TextInput::make('number')
                                    ->label('전화번호')
                                    ->tel()
                                    ->placeholder('예: 02-1234-5678')
                                    ->maxLength(20)
                                    ->required(),
                            ])
                            ->defaultItems(1)
                            ->addActionLabel('전화번호 추가')
                            ->simple()
                            ->columns(1)
                            ->columnSpan(3),
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
