<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Models\Branch;
use Filament\Actions;
use Filament\Resources\Pages\Page;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListTenants extends Page
{
    protected static string $resource = TenantResource::class;

    protected string $view = 'filament.resources.tenants.pages.list-tenants';

    public function getHeading(): string
    {
        $branchId = session('current_branch_id');
        $branch = Branch::find($branchId);

        return $branch ? "{$branch->name} 일정 관리" : '일정 관리';
    }

    protected function getHeaderActions(): array
    {
        return [
            // 일정 관리 페이지에는 생성 버튼이 필요 없을 수 있음
        ];
    }
}

