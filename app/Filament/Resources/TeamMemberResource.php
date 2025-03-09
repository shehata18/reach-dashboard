<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamMemberResource\Pages;
use App\Filament\Resources\TeamMemberResource\RelationManagers;
use App\Models\TeamMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $modelLabel = 'Team Member';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Card::make()
                ->schema([Forms\Components\Grid::make(2)->schema([Forms\Components\TextInput::make('name')->required()->maxLength(255), Forms\Components\TextInput::make('position')->required()->maxLength(255)]), Forms\Components\RichEditor::make('bio')->columnSpanFull(), Forms\Components\FileUpload::make('image')->image()->disk('public')->directory('team-members')->visibility('public')->columnSpanFull(), Forms\Components\Grid::make(3)->schema([Forms\Components\TextInput::make('email')->email()->maxLength(255), Forms\Components\TextInput::make('linkedin_url')->url()->maxLength(255), Forms\Components\TextInput::make('github_url')->url()->maxLength(255)]), Forms\Components\Grid::make(2)->schema([Forms\Components\Toggle::make('is_active')->default(true), Forms\Components\TextInput::make('sort_order')->numeric()->default(0)])])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([Tables\Columns\ImageColumn::make('image')->square()->size(40), Tables\Columns\TextColumn::make('name')->searchable()->sortable(), Tables\Columns\TextColumn::make('position')->searchable()->sortable(), Tables\Columns\TextColumn::make('email')->searchable()->toggleable(), Tables\Columns\IconColumn::make('is_active')->boolean()->sortable(), Tables\Columns\TextColumn::make('sort_order')->sortable(), Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true)])
            ->filters([
                Tables\Filters\Filter::make('is_active')
                    ->query(fn(Builder $query, array $data): Builder => $data['isActive'] ?? null ? $query->where('is_active', true) : $query)
                    ->form([Forms\Components\Checkbox::make('isActive')->label('Show Active Only')]),
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
            'index' => Pages\ListTeamMembers::route('/'),
            'create' => Pages\CreateTeamMember::route('/create'),
            'edit' => Pages\EditTeamMember::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [TeamMemberResource\Widgets\TeamMemberStats::class];
    }
}
