<?php

namespace App\Filament\Resources\PaymentManagement\Schemas;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

class PaymentManagementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('결제 정보')
                ->schema([
                    Forms\Components\Select::make('payment_due_day')
                        ->label('결제일')
                        ->options(array_combine(range(1, 31), array_map(fn ($d) => $d . '일', range(1, 31))))
                        ->placeholder('결제일 선택'),

                    Forms\Components\DatePicker::make('actual_payment_date')
                        ->label('실제 결제일'),

                    Forms\Components\Select::make('payment_method')
                        ->label('결제 방법')
                        ->options([
                            'card' => '카드',
                            'transfer' => '계좌이체',
                            'cash' => '현금',
                        ]),

                    Forms\Components\Select::make('payment_status')
                        ->label('납부 상태')
                        ->options([
                            'paid' => '납부완료',
                            'pending' => '미납',
                            'overdue' => '연체',
                            'waiting' => '대기',
                        ]),
                ])
                ->columns(4),
        ]);
    }
}
