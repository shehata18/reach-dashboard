<?php

namespace App\Filament\Resources\ContactSubmissionResource\Widgets;

use App\Models\ContactSubmission;
use Filament\Widgets\ChartWidget;

class ContactSubmissionStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Messages by Status';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $statusCounts = ContactSubmission::query()->selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Messages by Status',
                    'data' => array_values($statusCounts),
                    'backgroundColor' => [
                        '#ef4444', // red for new
                        '#f59e0b', // yellow for read
                        '#10b981', // green for replied
                        '#6b7280', // gray for archived
                    ],
                ],
            ],
            'labels' => array_map('ucfirst', array_keys($statusCounts)),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
