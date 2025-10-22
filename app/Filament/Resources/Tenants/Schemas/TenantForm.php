<?php

namespace App\Filament\Resources\Tenants\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class TenantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('기본 정보')
                    ->schema([
                        Forms\Components\Hidden::make('branch_id'),
                        Forms\Components\Hidden::make('room_number'),
                        Forms\Components\Hidden::make('room_type'),
                        Forms\Components\Hidden::make('monthly_rent'),
                        Forms\Components\Select::make('room_id')
                            ->label('호실')
                            ->required()
                            ->options(function () {
                                return \App\Models\Room::with('branch')
                                    ->get()
                                    ->mapWithKeys(function ($room) {
                                        $deposit = number_format($room->deposit ?? 0);
                                        $rent = number_format($room->monthly_rent ?? 0);
                                        return [
                                            $room->id => "{$room->branch->name} | {$room->room_number}호 | {$room->room_type} | ₩{$rent} | 보증금 ₩{$deposit}"
                                        ];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, $set) {
                                if ($state) {
                                    $room = \App\Models\Room::find($state);
                                    if ($room) {
                                        $set('branch_id', $room->branch_id);
                                        $set('room_number', $room->room_number);
                                        $set('room_type', $room->room_type);
                                        $set('monthly_rent', $room->monthly_rent);
                                    }
                                }
                            })
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
                    ])->columns(2),

                Section::make('입주 정보')
                    ->schema([
                        Forms\Components\DatePicker::make('move_in_date')
                            ->label('입주일')
                            ->displayFormat('Y년 m월 d일'),
                        Forms\Components\DatePicker::make('move_out_date')
                            ->label('퇴실일')
                            ->displayFormat('Y년 m월 d일'),
                        Forms\Components\Select::make('status')
                            ->label('상태')
                            ->options([
                                'active' => '입주중',
                                'inactive' => '퇴실',
                            ])
                            ->default('active')
                            ->required(),
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
                                'waiting' => '대기',
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
