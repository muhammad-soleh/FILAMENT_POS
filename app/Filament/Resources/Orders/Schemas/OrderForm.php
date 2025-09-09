<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Costumer;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class OrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DateTimePicker::make('date')
                    ->default(now())
                    ->required()
                    ->disabled()
                    ->hiddenLabel()
                    ->dehydrated()
                    ->prefix("Date: "),
                Section::make()
                    ->description('Costumer Information')
                    ->schema([
                        Select::make('costumer_id')
                            ->relationship('costumer', 'name')
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set) {
                                $costumer = Costumer::find($state);
                                $set('phone', $costumer->phone ?? null);
                                $set('address', $costumer->address ?? null);
                            })
                            ->required(),
                        TextInput::make('phone')
                            ->disabled(),
                        TextInput::make('address')
                            ->disabled(),

                    ])
                    ->columns(3)
                    ->columnSpanFull(),

                Section::make()
                    ->description('Order Detail')
                    ->schema([
                        Repeater::make('orderdetail')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->relationship(
                                        'product',
                                        'name'
                                    )
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $product = Product::find($state);
                                        $price = $product->price ?? 0;
                                        $set('price', $price);
                                        $qty = $get('qty') ?? 1;
                                        $set('qty', $qty);
                                        $subtotal = $price * $qty;
                                        $set('subtotal', $subtotal);

                                        $items = $get('../../orderdetail' ?? []);
                                        $total = collect($items)->sum(fn($item) => $item['subtotal'] ?? 0);
                                        $set('../../total_price', $total);
                                    }),
                                TextInput::make('price'),
                                TextInput::make('qty')
                                    ->numeric()
                                    ->default(1)
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        $price = $get('price') ?? 0;
                                        $set('subtotal', $price * $state);
                                        $items = $get('../../orderdetail' ?? []);
                                        $total = collect($items)->sum(fn($item) => $item['subtotal'] ?? 0);
                                        $set('../../total_price', $total);
                                    }),
                                TextInput::make('subtotal')
                                    ->numeric(),
                            ])->columns(3),
                    ])
                    ->columnSpanFull(),

                TextInput::make('total_price')
                    ->required()
                    ->numeric(),

            ]);
    }
}
