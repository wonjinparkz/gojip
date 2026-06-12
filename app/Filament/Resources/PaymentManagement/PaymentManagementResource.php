<?php

namespace App\Filament\Resources\PaymentManagement;

use App\Filament\Resources\PaymentManagement\Pages\ListPaymentManagement;
use App\Filament\Resources\PaymentManagement\Schemas\PaymentManagementForm;
use App\Filament\Resources\PaymentManagement\Tables\PaymentManagementTable;
use App\Models\Tenant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentManagementResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = '수납 관리';

    protected static ?string $modelLabel = '수납';

    protected static ?string $pluralModelLabel = '수납 관리';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return PaymentManagementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentManagementTable::configure($table);
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
            'index' => ListPaymentManagement::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with('room')
            ->whereNotNull('room_id')
            ->whereHas('branch', function (Builder $query) {
                $query->where('user_id', auth()->id());
            });

        $branchId = session('current_branch_id');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query;
    }
}
