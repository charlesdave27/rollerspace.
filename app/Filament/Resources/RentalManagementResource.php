<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RentalManagementResource\Pages;
use App\Models\RentalManagement;
use App\Models\RentalPackage;
use App\Models\Equipment;
use App\Models\Reward;
use App\Models\LoyaltyMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RentalManagementResource extends Resource
{
    protected static ?string $model = RentalManagement::class;
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Rental';

    protected static ?string $navigationLabel = 'Active Rentals';
    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user instanceof User && ($user->isAdmin() || $user->isStaff());
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            // LoyaltyMember QR integration

            Grid::make(2)->schema([
                Section::make('Customer Info')
                    ->schema([
                        View::make('components.qr-user-display'),
                        View::make('components.qr-scan-button'),
                        TextInput::make('name')
                            ->required()
                            ->nullable()
                    ])
                    ->columns(2),
                Hidden::make('loyalty_member_id'),
                Section::make('Rental package & Rewards')
                    ->columns(2)
                    ->schema([
                        Select::make('rental_package_id')
                            ->label('Rental Options')
                            ->options(fn() => RentalPackage::all()->pluck('name', 'id'))
                            ->reactive()
                            ->visible(fn(callable $get) => !$get('reward_id'))
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $rental = RentalPackage::find($state);
                                    if ($rental) {
                                        $set('points', $rental->points_rewarded);
                                        $set('price_paid', $rental->price);
                                        $set('deadline', $rental->duration);
                                    }
                                }
                            }),

                        Select::make('reward_id')
                            ->label('Redeem Reward')
                            ->options(function (callable $get) {
                                $memberId = $get('loyalty_member_id');
                                if (!$memberId) return [];
                                $member = LoyaltyMember::find($memberId);
                                if (!$member) return [];
                                return Reward::where('required_points', '<=', $member->loyalty_points)
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $reward = Reward::find($state);
                                    $set('rental_package_id', null);
                                    $set('deadline', $reward->duration);
                                }
                            }),
                    ]),

                Section::make('Equipments')
                    ->columns(12)
                    ->schema([
                        Select::make('size')
                            ->label('Size')
                            ->options([
                                'Small' => 'Small',
                                'Medium' => 'Medium',
                                'Large' => 'Large'
                            ])
                            ->reactive()
                            ->dehydrated(false)
                            ->columnSpan(7),

                        // Single repeater for all equipment types
                        Repeater::make('equipments')
                            ->label('Equipment')
                            ->schema([
                                Select::make('type')
                                    ->label('Equipment Type')
                                    ->options([
                                        'Roller Skate' => 'Roller Skate',
                                        'Helmet' => 'Helmet',
                                        'Pad' => 'Pad'
                                    ])
                                    ->reactive(),

                                Select::make('equipment_id')
                                    ->label(fn(callable $get) => $get('type') ? $get('type') : 'Equipment')
                                    ->options(
                                        fn(callable $get) =>
                                        Equipment::where('is_available', true)
                                            ->where('status', Equipment::STATUS_AVAILABLE)
                                            ->where('type', $get('type'))
                                            ->when($get('../../size'), fn($query, $size) => $query->where('size', $size))
                                            ->get()
                                            ->mapWithKeys(fn($equipment) => [
                                                $equipment->id => "{$equipment->name} ({$equipment->size})"
                                            ])
                                    )
                                    ->visible(fn(callable $get) => $get('type'))
                                    ->searchable()
                                    ->reactive()
                            ])
                            ->dehydrated(false)
                            ->columnSpan(7),

                    ]),

                Section::make('Rental Info')
                    ->schema([
                        TextInput::make('points')
                            ->label('Points Rewarded')
                            ->visible(fn(callable $get) => $get('rental_package_id') && $get('loyalty_member_id'))
                            ->disabled()
                            ->dehydrated(true)
                            ->default(function (callable $get) {
                                $loyaltyMemberId = $get('loyalty_member_id');
                                $rentalPackageId = $get('rental_package_id');
                                if ($loyaltyMemberId && $rentalPackageId) {
                                    $package = \App\Models\RentalPackage::find($rentalPackageId);
                                    return $package ? $package->points_rewarded : 0;
                                }
                                return 0;
                            }),

                        TextInput::make('price_paid')
                            ->label('Price')
                            ->visible(fn(callable $get) => $get('rental_package_id'))
                            ->required()
                            ->disabled()
                            ->dehydrated(true),

                        TextInput::make('deadline')
                            ->numeric()
                            ->disabled()
                            ->minValue(1)
                            ->label('Rental Duration')
                            ->dehydrateStateUsing(fn($state) => $state ? now()->addHours((int) $state) : null)
                            ->afterStateHydrated(function (&$state, $record) {
                                if ($record && $record->deadline) {
                                    $hours = \Carbon\Carbon::parse($record->deadline)->diffInHours(now());
                                    $state = $hours > 0 ? $hours : 1;
                                }
                            })
                            ->dehydrated(fn($state) => $state !== null),
                    ]),
            ])

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(fn(Builder $query) => static::getModel()::query()->with(['equipments', 'equipment'])->where('returned', false))
            ->columns([
                TextColumn::make('name')->label('Customer Name'),
                TextColumn::make('rental_or_reward_name')->label('Rental Type'),
                TextColumn::make('equipment_names')
                    ->label('Equipments Rented')
                    ->toggleable()
                    ->placeholder('None'),

                ViewColumn::make('countdown')
                    ->label('Duration')
                    ->view('filament.components.countdown-column')
                    ->viewData(['record' => 'record'])
            ])
            ->filters([])
            ->actions([
                Action::make('print_receipt')
                    ->label('Print Receipt')
                    ->color('info')
                    ->icon('heroicon-o-printer')
                    ->url(fn($record) => route('receipt.pdf', $record->id))
                    ->openUrlInNewTab(),
                Action::make('returned')
                    ->label('Returned')
                    ->color('success')
                    ->icon('heroicon-o-check')
                    ->visible(fn($record) => !$record->returned)
                    ->action(function ($record) {
                        $record->update(['returned' => true]);
                        // Mark all related equipments as available and pivot returned
                        foreach ($record->equipments as $equipment) {
                            $equipment->is_available = true;
                            $equipment->save();
                            $record->equipments()->updateExistingPivot($equipment->id, ['returned' => true]);
                        }
                        // Backward compat: single equipment_id
                        if ($record->equipment_id) {
                            $equipment = Equipment::find($record->equipment_id);
                            if ($equipment) {
                                $equipment->is_available = true;
                                $equipment->save();
                            }
                        }
                    })
                    ->requiresConfirmation()
                    ->successNotificationTitle('Marked as returned!'),
            ])
            ->recordUrl(function ($record) {
                return; // Disable default record URL
            })
            ->bulkActions([])
            ->paginated(false);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRentalManagement::route('/'),
            'create' => Pages\CreateRentalManagement::route('/create'),
            'edit' => Pages\EditRentalManagement::route('/{record}/edit'),
        ];
    }
}
