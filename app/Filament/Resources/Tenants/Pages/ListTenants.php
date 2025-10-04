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

    public function getTabs(): array
    {
        $tabs = ['all' => Tab::make('전체')];

        $branches = Branch::where('user_id', auth()->id())
            ->orderBy('name')
            ->get();

        foreach ($branches as $branch) {
            $tabs[$branch->id] = Tab::make($branch->name)
                ->badge(fn () => $branch->rooms()->count());
        }

        return $tabs;
    }

    public ?string $activeTab = 'all';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

