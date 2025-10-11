<?php

namespace App\Filament\Resources\TenantManagement;

use App\Filament\Resources\TenantManagement\Pages\CreateTenantManagement;
use App\Filament\Resources\TenantManagement\Pages\EditTenantManagement;
use App\Filament\Resources\TenantManagement\Pages\ListTenantManagement;
use App\Filament\Resources\TenantManagement\Schemas\TenantManagementForm;
use App\Filament\Resources\TenantManagement\Tables\TenantManagementTable;
use App\Models\Tenant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TenantManagementResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = '입주자 관리';

    protected static ?string $modelLabel = '입주자';

    protected static ?string $pluralModelLabel = '입주자 관리';

    public static function form(Schema $schema): Schema
    {
        return TenantManagementForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantManagementTable::configure($table);
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
            'index' => ListTenantManagement::route('/'),
            'create' => CreateTenantManagement::route('/create'),
            'edit' => EditTenantManagement::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->whereHas('branch', function (Builder $query) {
                $query->where('user_id', auth()->id());
            });

        // 세션에서 선택된 지점으로 필터링
        $branchId = session('current_branch_id');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query;
    }
}
