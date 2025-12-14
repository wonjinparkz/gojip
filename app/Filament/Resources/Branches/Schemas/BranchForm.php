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
                            ->columns(1)
                            ->columnSpan(3)
                            ->afterStateHydrated(function ($component, $state, $record) {
                                // DB에서 불러올 때: phone 필드를 배열로 변환
                                if ($record && $record->phone) {
                                    $phones = explode(',', $record->phone);
                                    $phoneArray = array_map(fn($phone) => ['number' => trim($phone)], $phones);
                                    $component->state($phoneArray);
                                }
                            })
                            ->dehydrated(false), // 직접 저장하지 않고 mutateFormDataBeforeCreate/Update에서 처리
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
