<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Filament\Resources\RoomResource\RelationManagers;
use App\Models\Room;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationLabel = '호실 관리';

    protected static ?string $modelLabel = '호실';

    protected static ?string $pluralModelLabel = '호실 관리';

    protected static ?int $navigationSort = 2;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-home';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('branch_id')
                    ->label('지점')
                    ->relationship('branch', 'name', fn (Builder $query) => $query->where('user_id', auth()->id()))
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('room_number')
                    ->label('호실 번호')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('예: 201, 202'),
                Forms\Components\TextInput::make('floor')
                    ->label('층수')
                    ->required()
                    ->numeric()
                    ->default(1),
                Forms\Components\TextInput::make('room_type')
                    ->label('호실 타입')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('예: 스탠다드룸'),
                Forms\Components\TextInput::make('monthly_rent')
                    ->label('월세')
                    ->required()
                    ->numeric()
                    ->prefix('₩')
                    ->placeholder('600000'),
                Forms\Components\TextInput::make('deposit')
                    ->label('보증금')
                    ->required()
                    ->numeric()
                    ->prefix('₩')
                    ->default(0)
                    ->placeholder('0'),
                Forms\Components\Select::make('status')
                    ->label('상태')
                    ->required()
                    ->options([
                        'available' => '입주가능',
                        'occupied' => '입주중',
                        'maintenance' => '수리중',
                    ])
                    ->default('available'),
                Forms\Components\DatePicker::make('move_in_date')
                    ->label('입주일')
                    ->displayFormat('Y년 m월 d일'),
                Forms\Components\DatePicker::make('move_out_date')
                    ->label('퇴실일')
                    ->displayFormat('Y년 m월 d일'),
                Forms\Components\TextInput::make('tenant_name')
                    ->label('입주자명')
                    ->maxLength(255)
                    ->placeholder('입주자 이름'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                View::make('filament.resources.room-resource.table.room-card')
                    ->extraAttributes(['class' => 'w-full']),
            ])
            ->contentGrid([
                'md' => 2,
                'xl' => 4,
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('branch_id')
                    ->label('지점')
                    ->relationship('branch', 'name', fn (Builder $query) => $query->where('user_id', auth()->id())),
                Tables\Filters\SelectFilter::make('floor')
                    ->label('층수')
                    ->options(fn () => Room::distinct()->pluck('floor', 'floor')->sort()),
                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        'available' => '입주가능',
                        'occupied' => '입주중',
                        'maintenance' => '수리중',
                    ]),
            ])
            ->actions([])
            ->bulkActions([])
            ->paginated([12, 24, 48, 'all'])
            ->defaultPaginationPageOption(12)
            ->checkIfRecordIsSelectableUsing(fn () => false); // Disable row selection
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('branch', function (Builder $query) {
                $query->where('user_id', auth()->id());
            });
    }
}
