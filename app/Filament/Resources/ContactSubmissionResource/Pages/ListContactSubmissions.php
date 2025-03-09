<?php

namespace App\Filament\Resources\ContactSubmissionResource\Pages;

use App\Filament\Resources\ContactSubmissionResource;
use Filament\Resources\Pages\ListRecords;

class ListContactSubmissions extends ListRecords
{
    protected static string $resource = ContactSubmissionResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            ContactSubmissionResource\Widgets\ContactSubmissionStats::class,
            ContactSubmissionResource\Widgets\ContactSubmissionChart::class,
            ContactSubmissionResource\Widgets\ContactSubmissionStatusChart::class,
        ];
    }
}
