<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobResource\Pages;
use App\Models\Job;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Collection;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\Textarea::make('description')->required(),
            Forms\Components\TextInput::make('location')->required(),
            Forms\Components\Select::make('type')
                ->options([
                    'full-time' => 'Full-time',
                    'part-time' => 'Part-time',
                    'contract' => 'Contract',
                ])
                ->required(),
            Forms\Components\Select::make('experience_level')
                ->options([
                    'entry' => 'Entry',
                    'mid' => 'Mid',
                    'senior' => 'Senior',
                ])
                ->required(),
            Forms\Components\TextInput::make('salary_min')->numeric()->nullable(),
            Forms\Components\TextInput::make('salary_max')->numeric()->nullable(),
            Forms\Components\TextInput::make('department')->nullable(),
            Forms\Components\Toggle::make('is_active')->default(true),
            Forms\Components\DatePicker::make('deadline')->nullable(),
            Forms\Components\TextInput::make('positions_available')->numeric()->default(1),
            Forms\Components\TagsInput::make('required_skills')->nullable(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([Tables\Columns\TextColumn::make('title')->sortable()->searchable(), Tables\Columns\TextColumn::make('location')->sortable()->searchable(), Tables\Columns\TextColumn::make('type')->sortable(), Tables\Columns\TextColumn::make('experience_level')->sortable(), Tables\Columns\BooleanColumn::make('is_active')->sortable(), Tables\Columns\TextColumn::make('deadline')->date()->sortable()])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->options([
                    'full-time' => 'Full-time',
                    'part-time' => 'Part-time',
                    'contract' => 'Contract',
                ]),
                Tables\Filters\SelectFilter::make('experience_level')->options([
                    'entry' => 'Entry',
                    'mid' => 'Mid',
                    'senior' => 'Senior',
                ]),
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\Action::make('markAsFilled')->label('Mark as Filled')->action(fn(Job $record) => $record->update(['positions_available' => 0]))->requiresConfirmation()->color('danger')])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make(), Tables\Actions\BulkAction::make('markAsFilled')->label('Mark as Filled')->action(fn(Collection $records) => $records->each->update(['positions_available' => 0]))->requiresConfirmation()->color('danger')])])
            ->emptyStateActions([Tables\Actions\CreateAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobs::route('/'),
            'create' => Pages\CreateJob::route('/create'),
            'edit' => Pages\EditJob::route('/{record}/edit'),
        ];
    }
}
