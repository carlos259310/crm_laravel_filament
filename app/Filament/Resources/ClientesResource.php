<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientesResource\Pages;
use App\Filament\Resources\ClientesResource\RelationManagers;
use App\Models\Clientes;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Http;


class ClientesResource extends Resource
{
    protected static ?string $model = Clientes::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'Cliente';
    protected static ?string $pluralModelLabel = 'Clientes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tipo_persona')
                    ->label('Tipo de Persona')
                    ->options([
                        'N' => 'Natural',
                        'J' => 'Jurídica',
                    ])
                    ->required()
                    ->reactive(),

                // Campos para persona natural
                Forms\Components\TextInput::make('nombre_1')
                    ->label('Primer Nombre')
                    ->required()
                    ->maxLength(30)
                    ->visible(fn($get) => $get('tipo_persona') === 'N'),

                Forms\Components\TextInput::make('nombre_2')
                    ->label('Segundo Nombre')
                    ->maxLength(30)
                    ->visible(fn($get) => $get('tipo_persona') === 'N'),

                Forms\Components\TextInput::make('apellido_1')
                    ->label('Primer Apellido')
                    ->required()
                    ->maxLength(30)
                    ->visible(fn($get) => $get('tipo_persona') === 'N'),

                Forms\Components\TextInput::make('apellido_2')
                    ->label('Segundo Apellido')
                    ->maxLength(30)
                    ->visible(fn($get) => $get('tipo_persona') === 'N'),

                // Campo para persona jurídica
                Forms\Components\TextInput::make('razon_social')
                    ->label('Razón Social')
                    ->required()
                    ->maxLength(120)
                    ->visible(fn($get) => $get('tipo_persona') === 'J'),

                // Campos comunes
                Forms\Components\Select::make('tipo_documento')
                    ->label('Tipo de Documento')
                    ->options([
                        'CC' => 'Cédula de Ciudadanía',
                        'NIT' => 'NIT',
                        'CE' => 'Cédula de Extranjería',
                    ])
                    ->required(),

                Forms\Components\TextInput::make('numero_documento')
                    ->label('Número de Documento')
                    ->required()
                    ->maxLength(20)
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->maxLength(100)
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('telefono')
                    ->label('Teléfono')
                    ->tel()
                    ->required()
                    ->maxLength(15),

                Forms\Components\TextInput::make('direccion')
                    ->label('Dirección')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('tipo_cliente')
                    ->label('Tipo de Cliente')
                    ->options([
                        'P' => 'Potencial',
                        'A' => 'Activo',
                        'I' => 'Inactivo',
                    ])
                    ->required(),

                //  Forms\Components\Select::make('ciudad')
                //     ->label('Ciudad')
                //   ->relationship('ciudad', 'ciudad')
                //  ->required()
                // ->maxLength(10),

                Forms\Components\Select::make('departamento')
                    ->label('Departamento')
                    ->relationship('departamento', 'departamento')
                    ->required()
                    ->maxLength(10),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipo_persona')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'N' => 'Natural',
                        'J' => 'Jurídica',
                    }),
                Tables\Columns\TextColumn::make('nombre_completo')
                    ->label('Nombre/Razón Social')
                    ->searchable()
                    ->formatStateUsing(function ($record) {
                        if ($record->tipo_persona === 'N') {
                            return "{$record->nombre_1} {$record->apellido_1}";
                        }
                        return $record->razon_social;
                    }),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono'),
                Tables\Columns\TextColumn::make('tipo_cliente')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'P' => 'Potencial',
                        'A' => 'Activo',
                        'I' => 'Inactivo',
                    }),
                //     Tables\Columns\TextColumn::make('ciudad.ciudad')
                //       ->label('Ciudad'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_persona')
                    ->options([
                        'N' => 'Natural',
                        'J' => 'Jurídica',
                    ]),
                Tables\Filters\SelectFilter::make('tipo_cliente')
                    ->options([
                        'P' => 'Potencial',
                        'A' => 'Activo',
                        'I' => 'Inactivo',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->using(function ($record) {
                        $response = Http::delete("/api/clientes/{$record->id_cliente}");
                        if ($response->successful()) {
                            return true;
                        }
                        throw new \Exception('Error al eliminar el cliente: ' . $response->json()['message']);
                    }),
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
            'index' => Pages\ListClientes::route('/'),
            'create' => Pages\CreateClientes::route('/create'),
            'edit' => Pages\EditClientes::route('/{record}/edit'),
        ];
    }
}
