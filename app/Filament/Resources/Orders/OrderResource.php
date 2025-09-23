<?php

namespace App\Filament\Resources\Orders;

use BackedEnum;
use App\Models\Order;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\Orders\Pages\EditOrder;
use App\Filament\Resources\Orders\Pages\ViewOrder;
use App\Filament\Resources\Orders\Pages\ListOrders;
use App\Filament\Resources\Orders\Pages\CreateOrder;
use App\Filament\Resources\Orders\Schemas\OrderForm;
use App\Filament\Resources\Orders\Tables\OrdersTable;
use App\Filament\Resources\Orders\Schemas\OrderInfolist;
use App\Filament\Resources\Orders\RelationManagers\OrderDetailRelationManager;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;
    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::ShoppingBag;

    protected static ?string $recordTitleAttribute = 'Order';


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'new')->count();
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['id', 'costumer.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'id' => $record->id,
            'Costumer Name' => $record->costumer->name ?? "N/A",

        ];
    }
    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::where('status', 'new')->count() > 0 ? 'info' : 'primary';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'The number of new orders';
    }

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrderInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            OrderDetailRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrders::route('/'),
            'create' => CreateOrder::route('/create'),
            'view' => ViewOrder::route('/{record}'),
            'edit' => EditOrder::route('/{record}/edit'),
        ];
    }
}
