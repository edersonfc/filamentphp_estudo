<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VendaResource\Pages;
use App\Models\Venda;
use App\Models\Produto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VendaResource extends Resource
{
    protected static ?string $model = Venda::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('cliente_id')
                    ->relationship('cliente', 'nome_cliente')
                    ->required(),

                Forms\Components\Repeater::make('produtos')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('produto_id')
                            ->label('Produto')
                            ->options(Produto::pluck('nome_produto', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, $state) {
                                $produto = Produto::find($state);
                                if ($produto) {
                                    $set('preco_unitario', $produto->preco);
                                }
                            }),

                        Forms\Components\TextInput::make('preco_unitario')
                            ->label('Preço Unitário')
                            ->disabled()
                            ->numeric(),

                        Forms\Components\TextInput::make('quantidade')
                            ->label('Quantidade')
                            ->required()
                            ->numeric()
                            ->reactive()
                            ->afterStateUpdated(function (callable $set, callable $get) {
                                $set('subtotal', $get('preco_unitario') * $get('quantidade'));
                            }),

                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->disabled()
                            ->numeric(),
                    ])
                    ->columns(4),

                Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->disabled()
                    ->numeric()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, callable $get) {
                        $total = collect($get('produtos'))->sum('subtotal');
                        $set('total', $total);
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cliente.nome_cliente')
                    ->label('Cliente')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->sortable()
                    ->money('BRL'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Data da Venda')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListVendas::route('/'),
            'create' => Pages\CreateVenda::route('/create'),
            'edit' => Pages\EditVenda::route('/{record}/edit'),
        ];
    }
}