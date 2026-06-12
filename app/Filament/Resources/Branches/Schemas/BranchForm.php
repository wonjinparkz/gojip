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
                Section::make('사업자 정보 (현금영수증/세금계산서 발행용)')
                    ->description('팝빌 연동으로 국세청 현금영수증·세금계산서를 발행하려면 등록된 사업자번호가 필요합니다.')
                    ->collapsed()
                    ->schema([
                        Forms\Components\TextInput::make('business_number')
                            ->label('사업자등록번호')
                            ->mask('999-99-99999')
                            ->placeholder('예: 123-45-67890')
                            ->dehydrateStateUsing(fn ($state) => $state ? preg_replace('/\D/', '', $state) : null)
                            ->rule('regex:/^\d{3}-?\d{2}-?\d{5}$/')
                            ->validationMessages([
                                'regex' => '올바른 사업자등록번호 형식이 아닙니다.',
                            ])
                            ->maxLength(12),
                        Forms\Components\TextInput::make('ceo_name')
                            ->label('대표자명')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('business_address')
                            ->label('사업장 주소')
                            ->helperText('비어 있으면 지점 주소를 사용합니다.')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('business_type')
                            ->label('업태')
                            ->placeholder('예: 서비스')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('business_class')
                            ->label('업종')
                            ->placeholder('예: 숙박업')
                            ->maxLength(255),
                    ])->columns(2)->columnSpanFull(),
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
                                    ->mask(\Filament\Support\RawJs::make(<<<'JS'
                                        $input.startsWith('02') ? '99-9999-9999' : '999-9999-9999'
                                    JS))
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
                            }), // 직접 저장하지 않고 mutateFormDataBeforeCreate/Update에서 처리
                        Forms\Components\TextInput::make('start_floor')
                            ->label('시작 층수')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->live()
                            ->afterStateUpdated(fn ($get, $set) => self::updateFloorSettings($get, $set)),
                        Forms\Components\TextInput::make('end_floor')
                            ->label('종료 층수')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->gte('start_floor')
                            ->live()
                            ->afterStateUpdated(fn ($get, $set) => self::updateFloorSettings($get, $set)),
                        
                        Section::make('층별 구성')
                            ->visibleOn('create')
                            ->schema([
                                Forms\Components\Repeater::make('floor_settings')
                                    ->label('층별 설정')
                                    ->schema([
                                        Forms\Components\TextInput::make('floor_number')
                                            ->label('층수')
                                            ->readonly(), // Just for display/reference
                                        Forms\Components\TextInput::make('room_type')
                                            ->label('방 타입')
                                            ->default('Standard')
                                            ->required(),
                                        Forms\Components\TextInput::make('monthly_rent')
                                            ->label('월세')
                                            ->numeric()
                                            ->default(0)
                                            ->required(),
                                        Forms\Components\TextInput::make('room_count')
                                            ->label('방 개수')
                                            ->numeric()
                                            ->default(1)
                                            ->required(),
                                    ])
                                    ->addable(false)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->columns(4),
                            ])
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function updateFloorSettings($get, $set): void
    {
        $start = (int) $get('start_floor');
        $end = (int) $get('end_floor');

        if ($start > 0 && $end > 0 && $end >= $start) {
            $items = [];
            for ($i = $start; $i <= $end; $i++) {
                $items[] = [
                    'floor_number' => $i,
                    'room_type' => 'Standard',
                    'monthly_rent' => 0,
                    'room_count' => 1,
                ];
            }
            $set('floor_settings', $items);
        }
    }
}
