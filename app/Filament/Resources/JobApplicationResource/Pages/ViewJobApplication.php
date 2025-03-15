<?php

namespace App\Filament\Resources\JobApplicationResource\Pages;

use App\Filament\Resources\JobApplicationResource;
use Filament\Resources\Pages\Page;
use Filament\Notifications\Notification;

class ViewJobApplication extends Page
{
    protected static string $resource = JobApplicationResource::class;

    protected static string $view = 'filament.resources.job-applications.pages.view-job-application';

    public $record;

    public function mount($record): void
    {
        $this->record = static::getResource()::resolveRecordRouteBinding($record);

        if (!$this->record) {
            abort(404);
        }
    }

    public function updateStatus(string $status): void
    {
        $this->record->update(['status' => $status]);

        Notification::make()->title('Application status updated successfully')->success()->send();
    }
}
