<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactSubmissionResource\Pages;
use App\Filament\Resources\ContactSubmissionResource\RelationManagers;
use App\Models\ContactSubmission;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ContactSubmissionResource extends Resource
{
    protected static ?string $model = ContactSubmission::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationGroup = 'Communications';

    protected static ?string $modelLabel = 'Contact Message';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([Forms\Components\TextInput::make('name')->required()->disabled(), Forms\Components\TextInput::make('email')->email()->required()->disabled(), Forms\Components\TextInput::make('phone')->tel()->disabled(), Forms\Components\TextInput::make('company')->disabled()]),

                    Forms\Components\TextInput::make('subject')->required()->disabled()->columnSpanFull(),

                    Forms\Components\Textarea::make('message')->required()->disabled()->columnSpanFull()->rows(6),

                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'new' => 'New',
                                'read' => 'Read',
                                'replied' => 'Replied',
                                'archived' => 'Archived',
                            ])
                            ->required(),

                        Forms\Components\DateTimePicker::make('read_at')->disabled(),
                    ]),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->url(fn($record) => route('filament.admin.resources.contact-submissions.view', $record))->openUrlInNewTab(false),

                Tables\Columns\TextColumn::make('email')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('subject')->searchable()->limit(30),

                Tables\Columns\BadgeColumn::make('status')->colors([
                    'danger' => 'new',
                    'warning' => 'read',
                    'success' => 'replied',
                    'secondary' => 'archived',
                ]),

                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),

                Tables\Columns\TextColumn::make('read_at')->dateTime()->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'new' => 'New',
                    'read' => 'Read',
                    'replied' => 'Replied',
                    'archived' => 'Archived',
                ]),

                Tables\Filters\Filter::make('unread')->query(fn(Builder $query) => $query->whereNull('read_at'))->toggle(),

                Tables\Filters\Filter::make('created_at')
                    ->form([Forms\Components\DatePicker::make('created_from'), Forms\Components\DatePicker::make('created_until')])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['created_from'], fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date))->when($data['created_until'], fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('updateStatus')
                    ->label('Update Status')
                    ->icon('heroicon-o-pencil-square')
                    ->form([
                        Forms\Components\Select::make('status')
                            ->options([
                                'new' => 'New',
                                'read' => 'Read',
                                'replied' => 'Replied',
                                'archived' => 'Archived',
                            ])
                            ->required()
                            ->default(function ($record) {
                                return $record->status;
                            }),
                    ])
                    ->action(function (ContactSubmission $record, array $data): void {
                        $record->update(['status' => $data['status']]);
                    }),
                Tables\Actions\DeleteAction::make(),
            ]);
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
            'index' => Pages\ListContactSubmissions::route('/'),
            'view' => Pages\ViewContactSubmission::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Contact Information')
                ->schema([Infolists\Components\Grid::make(2)->schema([Infolists\Components\TextEntry::make('name')->label('Full Name')->icon('heroicon-o-user'), Infolists\Components\TextEntry::make('email')->icon('heroicon-o-envelope')->copyable()->copyMessage('Email copied')->copyMessageDuration(1500), Infolists\Components\TextEntry::make('phone')->icon('heroicon-o-phone')->copyable(), Infolists\Components\TextEntry::make('company')->icon('heroicon-o-building-office')])])
                ->collapsible(),

            Infolists\Components\Section::make('Message Details')->schema([Infolists\Components\TextEntry::make('subject')->label('Subject Line')->weight('bold'), Infolists\Components\TextEntry::make('message')->markdown()->prose()]),

            Infolists\Components\Section::make('Status Information')->schema([
                Infolists\Components\Grid::make(3)->schema([
                    Infolists\Components\TextEntry::make('status')->badge()->color(
                        fn(string $state): string => match ($state) {
                            'new' => 'danger',
                            'read' => 'warning',
                            'replied' => 'success',
                            'archived' => 'gray',
                            default => 'primary',
                        },
                    ),

                    Infolists\Components\TextEntry::make('created_at')->label('Received')->dateTime()->icon('heroicon-o-clock'),

                    Infolists\Components\TextEntry::make('read_at')->label('Read')->dateTime()->icon('heroicon-o-eye'),
                ]),
            ]),
        ]);
    }

    public static function getWidgets(): array
    {
        return [ContactSubmissionResource\Widgets\ContactSubmissionStats::class, ContactSubmissionResource\Widgets\ContactSubmissionChart::class, ContactSubmissionResource\Widgets\ContactSubmissionStatusChart::class];
    }
}
