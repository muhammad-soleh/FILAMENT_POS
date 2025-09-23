<?php

namespace App\Filament\Resources;

use UnitEnum;
use Filament\Forms;
use Filament\Tables;
use App\Models\Costumer;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CostumerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CostumerResource\RelationManagers;
use App\Filament\Resources\CostumerResource\Pages\EditCostumer;
use App\Filament\Resources\CostumerResource\Pages\ViewCostumer;
use App\Filament\Resources\CostumerResource\Pages\ListCostumers;
use App\Filament\Resources\CostumerResource\Pages\CreateCostumer;

class CostumerResource extends Resource
{
    protected static ?string $model = Costumer::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';
    protected static string | \BackedEnum | null $activeNavigationIcon = 'heroicon-s-user-group';
    protected static ?int $navigationSort = 5;
    protected static UnitEnum|string|null $navigationGroup = "User Management";

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'phone', 'address'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'name' => $record->name,
            'phone' => $record->phone,
            'address' => $record->address,
        ];
    }


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'The number of costumers';
    }
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->tel()
                    ->required()
                    ->maxLength(255),
                Textarea::make('address')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('address')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
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
            'index' => ListCostumers::route('/'),
            'create' => CreateCostumer::route('/create'),
            'view' => ViewCostumer::route('/{record}'),
            'edit' => EditCostumer::route('/{record}/edit'),
        ];
    }
}
