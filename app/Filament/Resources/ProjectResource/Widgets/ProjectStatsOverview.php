<?php

namespace App\Filament\Resources\ProjectResource\Widgets;

use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Projects', Project::count())->description('All projects in the system')->color('primary'),

            Stat::make('Featured Projects', Project::where('is_featured', true)->count())
                ->description('Projects marked as featured')
                ->color('success'),

            Stat::make('Active Projects', Project::where('is_active', true)->count())
                ->description('Currently active projects')
                ->color('info'),
        ];
    }
}
