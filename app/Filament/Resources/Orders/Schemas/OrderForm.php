<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Costumer;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
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
                    ->prefix("Date: ")
                    ->columnSpanFull(),
                Group::make()
                    ->schema([

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
                                Placeholder::make('phone')
                                    ->content(fn(Get $get) => Costumer::find($get('costumer_id'))?->phone ?? '-'),
                                Placeholder::make('address')
                                    ->content(fn(Get $get) => Costumer::find($get('costumer_id'))?->address ?? '-'),

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
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
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

                                                $discount = $get('../../discount');
                                                $discount_amount = $total * $discount / 100;
                                                $set('../../discount_amount', $discount_amount);
                                                $set('../../total_payment', $total - $discount_amount);
                                            }),
                                        TextInput::make('price')->readOnly()->numeric()->dehydrated()->formatStateUsing(fn($state, Get $get)
                                        => $state ?? Product::find($get('product_id'))?->price ?? 0),
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

                                                $discount = $get('../../discount');
                                                $discount_amount = $total * $discount / 100;
                                                $set('../../discount_amount', $discount_amount);
                                                $set('../../total_payment', $total - $discount_amount);
                                            }),
                                        TextInput::make('subtotal')
                                            ->numeric()
                                            ->disabled()
                                            ->dehydrated(),
                                    ])->columns(3),
                            ])
                            ->columnSpanFull(),


                    ])->columnSpan(2),

                Section::make()
                    ->description('Payment Information')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'new' => 'New',
                                'processing' => 'Processing',
                                'completed' => 'Completed',
                                'cancelled' => 'Cancelled',
                            ])->default('new')->columnSpanFull(),
                        TextInput::make('total_price')
                            ->required()
                            ->numeric()
                            ->disabled()
                            ->dehydrated()
                            ->columnSpanFull(),
                        TextInput::make('discount')
                            ->columnSpan(1)
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $discount = floatval($state) ?? 0;
                                $total_price = $get('total_price') ?? 0;
                                $discount_amount = $total_price * $discount / 100;
                                $set('discount_amount', $discount_amount);
                                $set('total_payment', $total_price - $discount_amount);
                            }),
                        TextInput::make('discount_amount')
                            ->columnSpan(3)
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('total_payment')
                            ->columnSpanFull()
                            ->disabled()
                            ->dehydrated(),
                    ])->columnSpan(1)->columns(4),

            ])->columns(3);
    }
}
