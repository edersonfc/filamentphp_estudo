<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdutoResource\Pages;
use App\Filament\Resources\ProdutoResource\RelationManagers;
use App\Models\Produto;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Fornecedor;
use App\Models\Categoria;

class ProdutoResource extends Resource
{
    protected static ?string $model = Produto::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nome_produto')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Hidden::make('categoria_id'),

                Forms\Components\Select::make('categoria')
                    ->label('Categoria')
                    ->required()
                    ->options(Categoria::distinct()->pluck('categoria', 'categoria')->toArray())
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        $set('subcategoria', null);
                        $categoria = Categoria::where('categoria', $state)->first();
                        if ($categoria) {
                            $set('categoria_id', $categoria->id);
                        }
                    })
                    ->placeholder('Selecione a Categoria'),
                
                Forms\Components\Select::make('subcategoria')
                    ->label('Subcategoria')
                    ->required()
                    ->options(function (callable $get) {
                        $categoriaSelecionada = $get('categoria');
                        if ($categoriaSelecionada) {
                            return Categoria::where('categoria', $categoriaSelecionada)
                                ->pluck('subcategoria', 'subcategoria')->toArray();
                        }
                        return [];
                    })
                    ->searchable()
                    ->placeholder('Selecione a Subcategoria'),

                Forms\Components\Hidden::make('fornecedor_id'),

                Forms\Components\Select::make('nome_fornecedor')
                    ->label('Fornecedor')
                    ->required()
                    ->options(Fornecedor::pluck('nome_fornecedor', 'nome_fornecedor')->toArray())
                    ->searchable()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        $fornecedor = Fornecedor::where('nome_fornecedor', $state)->first();
                        if ($fornecedor) {
                            $set('fornecedor_id', $fornecedor->id);
                        }
                    })
                    ->placeholder('Selecione um fornecedor'),

                Forms\Components\TextInput::make('quantidade')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('preco')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nome_produto')
                    ->searchable(),
                Tables\Columns\TextColumn::make('categoria')
                    ->sortable(),
                Tables\Columns\TextColumn::make('subcategoria')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fornecedor.nome_fornecedor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantidade')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('preco')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListProdutos::route('/'),
            'create' => Pages\CreateProduto::route('/create'),
            'edit' => Pages\EditProduto::route('/{record}/edit'),
        ];
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $categoria = Categoria::where('categoria', $data['categoria'])
                              ->where('subcategoria', $data['subcategoria'])
                              ->first();
        
        $fornecedor = Fornecedor::where('nome_fornecedor', $data['nome_fornecedor'])->first();

        $data['categoria_id'] = $categoria ? $categoria->id : null;
        $data['fornecedor_id'] = $fornecedor ? $fornecedor->id : null;

        return $data;
    }

    public static function mutateFormDataBeforeUpdate(array $data): array
    {
        return static::mutateFormDataBeforeCreate($data);
    }
}