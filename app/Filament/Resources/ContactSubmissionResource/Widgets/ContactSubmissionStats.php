<?php

namespace App\Filament\Resources\ContactSubmissionResource\Widgets;

use App\Models\ContactSubmission;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ContactSubmissionStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Messages', ContactSubmission::count())->description('All time messages')->color('primary'),

            Stat::make('Unread Messages', ContactSubmission::where('status', 'new')->count())
                ->description('Requires attention')
                ->color('danger'),

            Stat::make('This Week', ContactSubmission::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count())
                ->description('Messages this week')
                ->color('info'),
        ];
    }
}
