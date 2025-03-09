<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 4;

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

                        Forms\Components\TextInput::make('slug')->required()->maxLength(255)->unique(Post::class, 'slug', ignoreRecord: true),
                    ]),

                    Forms\Components\RichEditor::make('content')->required()->columnSpanFull(),

                    Forms\Components\FileUpload::make('featured_image')->image()->disk('public')->directory('blog/featured-images')->visibility('public')->columnSpanFull(),

                    Forms\Components\Grid::make(2)->schema([Forms\Components\DateTimePicker::make('published_at')->label('Publish Date')->nullable(), Forms\Components\Toggle::make('is_published')->label('Published')->default(false)]),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([Tables\Columns\ImageColumn::make('featured_image')->square()->size(40), Tables\Columns\TextColumn::make('title')->searchable()->sortable(), Tables\Columns\IconColumn::make('is_published')->boolean()->sortable(), Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable(), Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true)])
            ->filters([
                Tables\Filters\Filter::make('published')
                    ->query(fn(Builder $query, array $data): Builder => $data['isPublished'] ?? null ? $query->where('is_published', true) : $query)
                    ->form([Forms\Components\Checkbox::make('isPublished')->label('Show Published Only')]),

                Tables\Filters\Filter::make('published_at')
                    ->form([Forms\Components\DatePicker::make('published_from'), Forms\Components\DatePicker::make('published_until')])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['published_from'], fn(Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date))->when($data['published_until'], fn(Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date));
                    }),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
