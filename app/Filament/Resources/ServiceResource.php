<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Illuminate\Support\Facades\Storage;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 1;

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

                        Forms\Components\TextInput::make('slug')->required()->maxLength(255)->unique(Service::class, 'slug', ignoreRecord: true),
                    ]),

                    Forms\Components\RichEditor::make('description')->required()->columnSpanFull(),

                    Forms\Components\TagsInput::make('features')->separator(',')->columnSpanFull(),

                    Forms\Components\FileUpload::make('icon')->image()->disk('public')->directory('services/icons')->visibility('public')->storeFileNamesIn('icon_filename')->columnSpan(1),

                    Forms\Components\FileUpload::make('image')->image()->disk('public')->directory('services/images')->columnSpan(1)->visibility('public'),

                    Forms\Components\Grid::make(2)->schema([Forms\Components\Toggle::make('is_active')->default(true), Forms\Components\TextInput::make('sort_order')->numeric()->default(0)]),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('icon')
                    ->square()
                    ->size(40)
                    ->disk('public')
                    ->visibility('public')
                    ->url(fn($record) => $record->icon ? Storage::disk('public')->url($record->icon) : null)
                    ->extraImgAttributes(['class' => 'object-contain']),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('features')->listWithLineBreaks()->limitList(2)->separator(','),
                Tables\Columns\IconColumn::make('is_active')->boolean()->sortable(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([Tables\Filters\TernaryFilter::make('is_active')->label('Active Status')->placeholder('All Services')->trueLabel('Active Services')->falseLabel('Inactive Services')])
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
