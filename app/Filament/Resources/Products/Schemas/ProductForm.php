<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\SubCategory;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function generateSku($get, $set)
    {
        $brand = Brand::find($get('brand_id'));
        $category = Category::find($get('category_id'));
        $subcategory = SubCategory::find($get('subcategory_id'));

        if (!$category || !$subcategory || !$brand) {
            return;
        }

        //ambil 3 huruf pertama
        $catCode = strtoupper(substr($category->name, 0, 3));
        $subCode = strtoupper(substr($subcategory->name, 0, 3));
        $brandCode = strtoupper(substr($brand->name, 0, 3));

        $lastSku = Product::where('category_id', $category->id)
            ->where('subcategory_id', $subcategory->id)
            ->where('brand_id', $brand->id)
            ->orderBy('id', 'desc')
            ->value('sku');

        $nextNumber = 1;
        if ($lastSku) {
            $parts = explode('-', $lastSku);
            $lastNumber = intval(end($parts));
            $nextNumber = $lastNumber + 1;
        }

        $sku = sprintf('%s-%s-%s-%03d', $catCode, $subCode, $brandCode, $nextNumber);
        $set('sku', $sku);
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make([
                        TextInput::make('name')
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('base_price')
                            ->required()
                            ->numeric()
                            ->prefix("RP. "),
                        TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('RP. '),
                        TextInput::make('stock')
                            ->required()
                            ->numeric(),
                        TextInput::make('sku'),
                        TextInput::make('barcode'),
                        Group::make([
                            Toggle::make('is_active')
                                ->required(),
                            Toggle::make('in_stock')
                                ->required(),
                        ]),
                        RichEditor::make('description')
                            ->columnSpanFull(),



                    ])->columns(3)
                        ->description("Product Detail")
                ])->columnSpan(2),

                Section::make([
                    Select::make('brand_id')
                        ->relationship('brand', 'name', fn($query) => $query->where('is_active', true))
                        ->reactive()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            static::generateSku($get, $set);
                        }),
                    Select::make('category_id')
                        ->relationship('category', 'name', fn($query) => $query->where('is_active', true))
                        ->reactive()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            static::generateSku($get, $set);
                        }),
                    Select::make('subcategory_id')
                        ->label('Sub Category')
                        ->reactive()
                        ->options(function (Get $get) {
                            $CategoryId = $get('category_id');
                            if (!$CategoryId) {
                                return [];
                            }

                            return SubCategory::where('category_id', $CategoryId)->pluck('name', 'id');
                        })
                        ->disabled(fn(callable $get) => $get('category_id') === null)
                        ->dehydrated()
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            static::generateSku($get, $set);
                        }),
                    FileUpload::make('image')
                        ->image(),
                ])->columnSpan(1)
                    ->description("Assosiation")
            ])->columns(3);
    }
}
