<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoyaltyMemberResource\Pages;
use App\Filament\Resources\LoyaltyMemberResource\RelationManagers;
use App\Models\LoyaltyMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Enums\PaginationMode;
use Filament\Forms\Components\Section;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoyaltyMemberResource extends Resource
{
    protected static ?string $model = LoyaltyMember::class;
    protected static ?int $navigationSort = 5;

    protected static ?string $navigationIcon = 'heroicon-o-star';

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user instanceof User && ($user->isAdmin() || $user->isStaff());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Loyalty Member')
                    ->columns(12)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->columnSpan(7),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
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
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('qr_code_path')
                //     ->searchable(),
                Tables\Columns\TextColumn::make('loyalty_points')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable(),
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
            ->recordUrl(function ($record) {
                return;
            })
            ->bulkActions([])
            ->defaultPaginationPageOption(10);
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
            'index' => Pages\ListLoyaltyMembers::route('/'),
            'create' => Pages\CreateLoyaltyMember::route('/create'),
            'edit' => Pages\EditLoyaltyMember::route('/{record}/edit'),
        ];
    }
}
