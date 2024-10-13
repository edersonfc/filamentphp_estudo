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

class VendaResource extends Resource
{
    protected static ?string $model = Venda::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Menu suspenso para selecionar o cliente
                Forms\Components\Select::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nome_cliente') // Relacionamento com o modelo Cliente
                    ->required(),

                // Menu suspenso para selecionar o produto
                Forms\Components\Select::make('produto_id')
                    ->label('Produto')
                    ->relationship('produto', 'nome_produto') // Relacionamento com o modelo Produto
                    ->required()
                    ->reactive() // Torna o campo reativo para disparar eventos quando o produto é selecionado
                    ->afterStateUpdated(function (callable $set, $state) {
                        // Obter o preço do produto selecionado
                        $produto = Produto::find($state);
                        if ($produto) {
                            $set('preco_unitario', $produto->preco); // Preencher o campo de preço unitário
                        }
                    }),

                // Campo para exibir o preço unitário do produto selecionado
                Forms\Components\TextInput::make('preco_unitario')
                // Forms\Components\TextInput::make('preco')
                    ->label('Preço Unitário')
                    ->disabled() // Campo apenas leitura
                    ->numeric(),

                // Campo para quantidade
                Forms\Components\TextInput::make('quantidade')
                    ->label('Quantidade')
                    ->required()
                    ->numeric()
                    ->reactive() // Recalcula o subtotal quando a quantidade muda
                    ->afterStateUpdated(function (callable $set, callable $get) {
                        // Atualiza o subtotal (preço unitário * quantidade)
                        $set('subtotal', $get('preco_unitario') * $get('quantidade'));
                        $set('total', $get('subtotal')); // Atualiza o total com o subtotal
                    }),

                // Campo para exibir o subtotal calculado
                Forms\Components\TextInput::make('subtotal')
                    ->label('Subtotal')
                    ->disabled() // Somente leitura
                    ->numeric(),

                // Campo para exibir o total
                Forms\Components\TextInput::make('total')
                    ->label('Total')
                    ->disabled() // Somente leitura
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('cliente.nome_cliente')
                    ->label('Cliente')
                    ->sortable(),

                Tables\Columns\TextColumn::make('produto.nome_produto')
                    ->label('Produto')
                    ->sortable(),

                Tables\Columns\TextColumn::make('produto.preco')
                    ->label('Preco')
                    ->sortable(),

                Tables\Columns\TextColumn::make('quantidade')
                    ->label('Quantidade')
                    ->sortable()
                    ->numeric(),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->sortable()
                    ->numeric(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
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
