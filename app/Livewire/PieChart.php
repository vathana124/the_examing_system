<?php

namespace App\Livewire;

use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Widget;

class PieChart extends ChartWidget
{
    protected static string $view = 'livewire.pie-chart';

    protected static bool $isLazy = false;
    protected int | string | array $columnSpan = 'full';

    public $data = [];
    public $startDate;
    public $endDate;


    protected function getData(): array
    {
        return [];
    }
    protected function getType(): string
    {
        return 'doughnut';
    }
}
