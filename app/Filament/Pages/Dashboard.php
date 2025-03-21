<?php

namespace App\Filament\Pages;

use App\Livewire\Line1Chart;
use App\Livewire\LineChart;
use App\Livewire\PieChart;
use App\Livewire\StatsOverview;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{

    public static function getNavigationIcon(): string | Htmlable | null
    {
        return asset('icons/flaticon/dashboard.png');
    }

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }


    public static function canAccess(): bool
    {
        $user = auth()->user();

        if($user->isSuperAdmin()){
            return false;
        }
        return true;
    }

}
