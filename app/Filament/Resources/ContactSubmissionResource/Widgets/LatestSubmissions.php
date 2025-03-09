<?php

namespace App\Filament\Resources\ContactSubmissionResource\Widgets;

use App\Models\ContactSubmission;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestSubmissions extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(ContactSubmission::query()->where('status', 'new')->latest()->limit(5))
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('subject')->limit(50)->searchable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
                Tables\Columns\BadgeColumn::make('status')->colors([
                    'danger' => 'new',
                    'warning' => 'read',
                    'success' => 'replied',
                    'secondary' => 'archived',
                ]),
            ])
            ->actions([Tables\Actions\ViewAction::make()]);
    }
}
