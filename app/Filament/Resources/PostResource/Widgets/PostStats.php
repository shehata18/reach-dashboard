<?php

namespace App\Filament\Resources\PostResource\Widgets;

use App\Models\Post;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PostStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Posts', Post::count())->description('All blog posts')->color('primary'),

            Stat::make('Published Posts', Post::where('is_published', true)->count())
                ->description('Visible on website')
                ->color('success'),

            Stat::make('Draft Posts', Post::where('is_published', false)->count())
                ->description('Not yet published')
                ->color('warning'),
        ];
    }
}
