<?php

namespace App\Filament\Resources\ContactSubmissionResource\Widgets;

use App\Models\ContactSubmission;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ContactSubmissionChart extends ChartWidget
{
    protected static ?string $heading = 'Messages Timeline';
    
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = $this->getTimelineData();

        return [
            'datasets' => [
                [
                    'label' => 'Messages',
                    'data' => $data['counts'],
                    'backgroundColor' => '#7c3aed',
                    'borderColor' => '#7c3aed',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getTimelineData(): array
    {
        $days = collect(range(30, 0, -1))->map(function ($daysAgo) {
            $date = Carbon::now()->subDays($daysAgo);
            
            return [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('M j'),
                'count' => ContactSubmission::whereDate('created_at', $date)->count(),
            ];
        });

        return [
            'labels' => $days->pluck('label')->toArray(),
            'counts' => $days->pluck('count')->toArray(),
        ];
    }
} 