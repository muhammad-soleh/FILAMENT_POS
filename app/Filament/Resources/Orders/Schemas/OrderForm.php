<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Costumer;
use App\Models\Product;
use Filament\Actions\Action;
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
                                    ->label('Name')
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
                                        TextInput::make('price')
                                            ->readOnly()
                                            ->numeric()
                                            ->formatStateUsing(fn($state, Get $get)
                                            => $state ?? Product::find($get('product_id'))?->price ?? 0)
                                            ->prefix('Rp.'),
                                        TextInput::make('qty')
                                            ->numeric()
                                            ->minValue(1)
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
                                            ->readOnly()
                                            ->default(0)
                                            ->prefix('Rp.'),
                                    ])->columns(4)
                                    ->hiddenLabel()
                                    ->addAction(fn(Action $action) => $action
                                        ->label('Add Product')
                                        ->color('primary')
                                        ->icon('heroicon-o-plus')),
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
                            ->readOnly()
                            ->columnSpanFull()
                            ->prefix('Rp.')
                            ->default(0),
                        TextInput::make('discount')
                            ->columnSpan(2)
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $discount = floatval($state) ?? 0;
                                $total_price = $get('total_price') ?? 0;
                                $discount_amount = $total_price * $discount / 100;
                                $set('discount_amount', $discount_amount);
                                $set('total_payment', $total_price - $discount_amount);
                            })
                            ->suffix('%')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(100),
                        TextInput::make('discount_amount')
                            ->columnSpan(2)
                            ->readOnly()
                            ->prefix('Rp.')
                            ->default(0),
                        TextInput::make('total_payment')
                            ->columnSpanFull()
                            ->readOnly()
                            ->prefix('Rp.')
                            ->default(0),
                        Select::make('payment_method')
                            ->options(
                                [
                                    'cash' => 'Cash',
                                    'debit' => 'Debit',
                                    'credit' => 'Credit',
                                    'qris' => 'Qris'
                                ]
                            )->default('cash')
                            ->columnSpan(2),
                        Select::make('payment_status')
                            ->options([
                                'unpaid' => 'Unpaid',
                                'paid' => 'Paid',
                            ])->default('unpaid')
                            ->columnSpan(2),
                    ])->columnSpan(1)->columns(4),

            ])->columns(3);
    }
}
