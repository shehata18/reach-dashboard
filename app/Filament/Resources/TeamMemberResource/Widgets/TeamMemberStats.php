<?php

namespace App\Filament\Resources\TeamMemberResource\Widgets;

use App\Models\TeamMember;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TeamMemberStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Team Members', TeamMember::count())->description('All team members')->color('primary'),

            Stat::make('Active Members', TeamMember::where('is_active', true)->count())
                ->description('Currently active members')
                ->color('success'),

            Stat::make('Inactive Members', TeamMember::where('is_active', false)->count())
                ->description('Currently inactive members')
                ->color('danger'),
        ];
    }
}
