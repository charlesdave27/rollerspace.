<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentalPackageResource\Pages;
use App\Filament\Resources\RentalPackageResource\RelationManagers;
use App\Models\RentalPackage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class RentalPackageResource extends Resource
{
    protected static ?string $model = RentalPackage::class;
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Rental Package')
                    ->columns(12)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->columnSpan(7),
                        Forms\Components\TextInput::make('duration')
                            ->numeric()
                            ->step(1)
                            ->minValue(1)
                            ->columnSpan(7),
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('PHP')
                            ->columnSpan(7),
                        Forms\Components\TextInput::make('points_rewarded')
                            ->required()
                            ->numeric()
                            ->columnSpan(7),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('duration')
                    ->sortable()
                    ->placeholder('Unlimited'),
                Tables\Columns\TextColumn::make('price')
                    ->prefix('₱')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('points_rewarded')
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
                Tables\Actions\ViewAction::make(),
            ])
            ->recordUrl(function ($record) {
                return; // Disable default record URL
            })
            ->bulkActions([])
            ->paginated(false);
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
            'index' => Pages\ListRentalPackages::route('/'),
            'create' => Pages\CreateRentalPackage::route('/create'),
            'edit' => Pages\EditRentalPackage::route('/{record}/edit'),
        ];
    }
}
