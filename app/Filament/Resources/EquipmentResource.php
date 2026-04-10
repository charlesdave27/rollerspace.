<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EquipmentResource\Pages;
use App\Filament\Resources\EquipmentResource\RelationManagers;
use App\Models\Equipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class EquipmentResource extends Resource
{
    protected static ?string $model = Equipment::class;
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Skate & Gears';

    protected static ?string $navigationLabel = 'Skates & Gears';

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Equipments')
                    ->columns(12)
                    ->columnSpanFull()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->columnSpan(7),
                        Forms\Components\Select::make('type')
                            ->options([
                                'Roller Skate' => 'Roller Skate',
                                'Helmet' => 'Helmet',
                                'Pad' => 'Pad',
                            ])
                            ->required()
                            ->columnSpan(7),
                        Forms\Components\Select::make('size')
                            ->options([
                                'Small' => 'Small',
                                'Medium' => 'Medium',
                                'Large' => 'Large',
                            ])
                            ->required()
                            ->columnSpan(7),
                        Forms\Components\Toggle::make('is_available')
                            ->default(true)
                            ->required()
                            ->columnSpan(7),
                        Forms\Components\Select::make('status')
                            ->options(Equipment::statusOptions())
                            ->default(Equipment::STATUS_AVAILABLE)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (in_array($state, [Equipment::STATUS_MAINTENANCE, Equipment::STATUS_DAMAGED, Equipment::STATUS_RETIRED])) {
                                    $set('is_available', false);
                                }
                            })
                            ->columnSpan(7),
                        Forms\Components\Textarea::make('maintenance_notes')
                            ->rows(3)
                            ->placeholder('Notes about repairs, parts, etc.')
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('last_maintenance_at')
                            ->nullable()
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
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('size')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_available')
                    ->label('Rentable')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'maintenance' => 'warning',
                        'damaged' => 'danger',
                        'retired' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('last_maintenance_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('maintenance_notes')
                    ->limit(30)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\SelectFilter::make('status')
                    ->options(Equipment::statusOptions()),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'Roller Skate' => 'Roller Skate',
                        'Helmet' => 'Helmet',
                        'Pad' => 'Pad',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->recordUrl(function ($record) {
                return null; // Disable default record URL
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
            'index' => Pages\ListEquipment::route('/'),
            'create' => Pages\CreateEquipment::route('/create'),
            'edit' => Pages\EditEquipment::route('/{record}/edit'),
        ];
    }
}
