<?php

namespace App\Filament\Resources\TenantManagement\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Illuminate\Database\Eloquent\Builder;

class TenantManagementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('기본 정보')
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->label('지점')
                            ->relationship('branch', 'name', fn (Builder $query) => $query->where('user_id', auth()->id()))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('name')
                            ->label('이름')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('연락처')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('gender')
                            ->label('성별')
                            ->options([
                                'male' => '남성',
                                'female' => '여성',
                            ]),
                    ])->columns(3),

                Section::make('결제 정보')
                    ->schema([
                        Forms\Components\DatePicker::make('last_payment_date')
                            ->label('마지막 입금일')
                            ->displayFormat('Y년 m월 d일'),
                        Forms\Components\Select::make('payment_method')
                            ->label('결제 방법')
                            ->options([
                                'card' => '카드',
                                'transfer' => '계좌이체',
                                'cash' => '현금',
                            ]),
                        Forms\Components\Select::make('payment_status')
                            ->label('최근 납부 상태')
                            ->options([
                                'paid' => '납부완료',
                                'pending' => '미납',
                                'overdue' => '연체',
                            ])
                            ->default('pending')
                            ->required(),
                    ])->columns(3),

                Section::make('블랙리스트')
                    ->schema([
                        Forms\Components\Toggle::make('is_blacklisted')
                            ->label('블랙리스트 여부')
                            ->default(false),
                        Forms\Components\Textarea::make('blacklist_memo')
                            ->label('블랙리스트 메모')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
