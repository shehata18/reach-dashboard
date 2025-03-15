<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JobApplicationResource\Pages;
use App\Filament\Resources\JobApplicationResource\RelationManagers;
use App\Models\JobApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Widgets;
use Filament\Notifications\Notification;

class JobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form->schema([Forms\Components\Select::make('job_id')->label('Job')->relationship('job', 'title')->required(), Forms\Components\TextInput::make('full_name')->required()->maxLength(255), Forms\Components\TextInput::make('email')->required()->email(), Forms\Components\TextInput::make('phone')->nullable(), Forms\Components\Textarea::make('cover_letter')->nullable(), Forms\Components\FileUpload::make('resume_path')->label('Resume')->disk('public')->directory('resumes')->nullable()]);
    }

    public static function viewForm(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('job_id')->label('Job')->relationship('job', 'title')->required()->disabled(),
            Forms\Components\TextInput::make('full_name')->required()->maxLength(255)->disabled(),
            Forms\Components\TextInput::make('email')->required()->email()->disabled(),
            Forms\Components\TextInput::make('phone')->nullable()->disabled(),
            Forms\Components\Textarea::make('cover_letter')->nullable()->disabled(),
            Forms\Components\FileUpload::make('resume_path')->label('Resume')->disk('public')->directory('resumes')->nullable()->disabled(),
            Forms\Components\Select::make('status')
                ->options([
                    'pending' => 'Pending',
                    'reviewed' => 'Reviewed',
                    'accepted' => 'Accepted',
                    'rejected' => 'Rejected',
                ])
                ->default('pending'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->sortable()->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'accepted',
                        'danger' => 'rejected',
                        'info' => 'reviewed',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->date()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'reviewed' => 'Reviewed',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                    ])
                    ->indicator('Status')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->optionsLimit(4)
                    ->columnSpanFull(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('changeStatus')
                    ->label('Change Status')
                    ->icon('heroicon-m-pencil-square')
                    ->modalHeading('Change Application Status')
                    ->modalWidth('sm')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'reviewed' => 'Reviewed',
                                'accepted' => 'Accepted',
                                'rejected' => 'Rejected',
                            ])
                            ->required()
                            ->default(fn($record) => $record->status)
                            ->selectablePlaceholder(false)
                            ->extraAttributes([
                                'style' => 'color: #f97316;',
                            ]),
                    ])

                    ->action(function (JobApplication $record, array $data): void {
                        $record->update(['status' => $data['status']]);
                        Notification::make()->title('Status updated successfully')->success()->send();
                    }),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getRelations(): array
    {
        return [
                //
            ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobApplications::route('/'),
            'view' => Pages\ViewJobApplication::route('/{record}'),
        ];
    }
}
