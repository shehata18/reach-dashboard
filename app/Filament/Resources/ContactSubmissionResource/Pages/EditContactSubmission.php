<?php

namespace App\Filament\Resources\ContactSubmissionResource\Pages;

use App\Filament\Resources\ContactSubmissionResource;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\EditRecord;

class EditContactSubmission extends EditRecord
{
    protected static string $resource = ContactSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema([
                    Select::make('status')
                        ->options([
                            'new' => 'New',
                            'read' => 'Read',
                            'replied' => 'Replied',
                            'archived' => 'Archived',
                        ])
                        ->required(),
                ])
                ->columns(1),
        ];
    }
}
