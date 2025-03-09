<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, $state, Forms\Set $set) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        Forms\Components\TextInput::make('slug')->required()->maxLength(255)->unique(Project::class, 'slug', ignoreRecord: true),
                    ]),

                    Forms\Components\RichEditor::make('description')->required()->columnSpanFull(),

                    Forms\Components\Grid::make(2)->schema([Forms\Components\TextInput::make('client_name')->maxLength(255), Forms\Components\Select::make('service_id')->relationship('service', 'title')->required()->searchable()->preload()->native(false)->optionsLimit(100)]),

                    Forms\Components\Grid::make(2)->schema([Forms\Components\DatePicker::make('completion_date'), Forms\Components\TextInput::make('website_url')->url()->maxLength(255)]),

                    Forms\Components\TagsInput::make('technologies')->separator(',')->columnSpanFull(),

                    Forms\Components\FileUpload::make('image')->image()->disk('public')->directory('projects/images')->visibility('public')->columnSpan(1),

                    Forms\Components\FileUpload::make('gallery')->multiple()->image()->disk('public')->directory('projects/gallery')->visibility('public')->columnSpan(1),

                    Forms\Components\Grid::make(3)->schema([Forms\Components\Toggle::make('is_featured')->default(false), Forms\Components\Toggle::make('is_active')->default(true), Forms\Components\TextInput::make('sort_order')->numeric()->default(0)]),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([Tables\Columns\ImageColumn::make('image')->square()->size(40)->disk('public'), Tables\Columns\TextColumn::make('title')->searchable()->sortable(), Tables\Columns\TextColumn::make('service.title')->searchable()->sortable(), Tables\Columns\TextColumn::make('client_name')->searchable(), Tables\Columns\IconColumn::make('is_featured')->boolean()->sortable(), Tables\Columns\IconColumn::make('is_active')->boolean()->sortable(), Tables\Columns\TextColumn::make('sort_order')->sortable()])
            ->filters([
                Tables\Filters\SelectFilter::make('service')->relationship('service', 'title'),

                Tables\Filters\Filter::make('is_featured')
                    ->query(fn(Builder $query, array $data): Builder => $data['isActive'] ?? null ? $query->where('is_featured', true) : $query)
                    ->form([Forms\Components\Checkbox::make('isActive')->label('Show Featured Only')]),

                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('sort_order', 'asc')
            ->reorderable('sort_order');
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
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            ProjectResource\Widgets\ProjectStatsOverview::class,
            ProjectResource\Widgets\ProjectsByService::class,
        ];
    }
}
