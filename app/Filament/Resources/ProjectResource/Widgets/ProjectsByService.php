<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ProjectsByService extends ChartWidget
{
    protected static ?int $sort = 3;
    
    protected static ?string $heading = 'Projects by Service';

    protected function getData(): array
    {
        $data = Project::select('services.title', DB::raw('count(*) as count'))
            ->join('services', 'projects.service_id', '=', 'services.id')
            ->groupBy('services.title')
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Projects',
                    'data' => $data->pluck('count')->toArray(),
                    'backgroundColor' => ['#36A2EB', '#FF6384', '#4BC0C0', '#FF9F40', '#9966FF'],
                ],
            ],
            'labels' => $data->pluck('title')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
} 