<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DashboardMemo extends Widget
{
    protected string $view = 'filament.widgets.dashboard-memo';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = -1; // Display at the top

    public static function canView(): bool
    {
        return true;
    }
}
