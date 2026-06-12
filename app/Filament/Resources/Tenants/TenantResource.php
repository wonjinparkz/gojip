<?php

namespace App\Filament\Resources\Tenants;

use App\Filament\Resources\Tenants\Pages\CreateTenant;
use App\Filament\Resources\Tenants\Pages\EditTenant;
use App\Filament\Resources\Tenants\Pages\ListTenants;
use App\Filament\Resources\Tenants\Pages\TmpListTenants;
use App\Filament\Resources\Tenants\Schemas\TenantForm;
use App\Filament\Resources\Tenants\Tables\TenantsTable;
use App\Models\Tenant;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = '입실 관리';

    protected static ?string $modelLabel = '입주자';

    protected static ?string $pluralModelLabel = '입실 관리';

    public static function form(Schema $schema): Schema
    {
        return TenantForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TenantsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // 세션에서 선택된 지점으로 필터링
        $branchId = session('current_branch_id');

        // 세션에 지점 ID가 없으면 사용자의 첫 번째 지점을 자동 설정
        if (!$branchId) {
            $firstBranch = \App\Models\Branch::where('user_id', auth()->id())->first();
            if ($firstBranch) {
                session(['current_branch_id' => $firstBranch->id]);
                $branchId = $firstBranch->id;
            }
        }

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        return $query;
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
            'index' => ListTenants::route('/'),
            'tmp' => TmpListTenants::route('/tmp'),
            'create' => CreateTenant::route('/create'),
            'edit' => EditTenant::route('/{record}/edit'),
        ];
    }
}
