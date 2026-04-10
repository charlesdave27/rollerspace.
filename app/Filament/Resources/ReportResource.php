<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Models\Report;
use App\Models\RentalManagement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Average;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use App\Exports\RevenueReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ReportResource extends Resource
{
    protected static ?string $model = Report::class;
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Revenue Reports';
    protected static ?string $modelLabel = 'Report';

    public static function canCreate(): bool
    {
        $user = Auth::user();

        return $user instanceof User && $user->isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                RentalManagement::query()
                    ->with(['loyaltyMember', 'rentalPackage', 'equipment', 'equipments', 'reward'])
                    ->where(function ($query) {
                        $query->whereNotNull('price_paid')
                            ->orWhereNotNull('reward_id');
                    })
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('loyaltyMember.name')
                    ->label('Customer')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        return $record->loyaltyMember ? 'Member' : 'Walk-in';
                    }),

                Tables\Columns\TextColumn::make('name')
                    ->label('Customer Name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('rentalPackage.name')
                    ->label('Package')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        return $record->rentalPackage->name ?? 'None';
                    }),

                // Tables\Columns\TextColumn::make('equipment_names')
                //     ->label('Equipments')
                //     ->sortable()
                //     ->searchable(),

                Tables\Columns\TextColumn::make('price_paid')
                    ->label('Revenue')
                    ->money('php')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return $record->price_paid ?? 0;
                    })
                    ->summarize([
                        Sum::make()->money('php')->label('Total Revenue'),
                        Average::make()->money('php')->label('Average'),
                        Count::make()->label('Total Rentals'),
                    ]),

                Tables\Columns\TextColumn::make('points')
                    ->label('Points Earned')
                    ->sortable()
                    ->getStateUsing(function ($record) {
                        return $record->points ?? 0;
                    })
                    ->summarize([
                        Sum::make()->label('Total Points'),
                    ]),

                Tables\Columns\TextColumn::make('reward.name')
                    ->label('Claimed Reward')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        return $record->reward->name ?? 'none';
                    }),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Duration')
                    ->sortable()
                    ->searchable()
                    ->getStateUsing(function ($record) {
                        return $record->rentalPackage->duration ?? $record->reward->duration ?? 'Unlimited';
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Rental Date')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From Date')
                            ->default(now()->startOfMonth()),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until Date')
                            ->default(now()->endOfMonth()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                SelectFilter::make('returned')
                    ->label('Return Status')
                    ->options([
                        '1' => 'Returned',
                        '0' => 'Not Returned',
                    ]),

                Filter::make('has_package')
                    ->label('Package Rentals Only')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('rental_package_id')),

                Filter::make('has_equipment')
                    ->label('Equipment Rentals Only')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('equipment_id')),

                Filter::make('loyalty_members')
                    ->label('Loyalty Members Only')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('loyalty_member_id')),

                Filter::make('walk_ins')
                    ->label('Walk-in Customers Only')
                    ->query(fn(Builder $query): Builder => $query->whereNull('loyalty_member_id')),

                Filter::make('redeemed_rewards')
                    ->label('Redeemed Rewards Only')
                    ->query(fn(Builder $query): Builder => $query->whereNotNull('reward_id')),

                SelectFilter::make('month')
                    ->label('Month')
                    ->options([
                        '1' => 'January',
                        '2' => 'February',
                        '3' => 'March',
                        '4' => 'April',
                        '5' => 'May',
                        '6' => 'June',
                        '7' => 'July',
                        '8' => 'August',
                        '9' => 'September',
                        '10' => 'October',
                        '11' => 'November',
                        '12' => 'December',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (filled($data['value'])) {
                            return $query->whereMonth('created_at', $data['value']);
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View Details')
                    ->modalHeading('Rental Details')
                    ->modalContent(function ($record) {
                        return view('filament.pages.rental-details', ['record' => $record]);
                    })
                    ->modalWidth('2xl'),
            ])
            ->recordUrl(function ($record) {
                return null; // Disable default record URL
            })
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('mark_returned')
                        ->label('Mark as Returned')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each(function ($record) {
                                $record->update(['returned' => true]);
                            });
                        })
                        ->requiresConfirmation(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_revenue')
                    ->label('Export Revenue Report')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('success')
                    ->form([
                        Forms\Components\Section::make('Export Filters')
                            ->schema([
                                Forms\Components\DatePicker::make('from')
                                    ->label('From Date')
                                    ->default(now()->startOfMonth()),
                                Forms\Components\DatePicker::make('until')
                                    ->label('Until Date')
                                    ->default(now()->endOfMonth()),
                                Forms\Components\Toggle::make('has_package')
                                    ->label('Package Rentals Only')
                                    ->default(false),
                                Forms\Components\Toggle::make('has_equipment')
                                    ->label('Equipment Rentals Only')
                                    ->default(false),
                                Forms\Components\Toggle::make('loyalty_members')
                                    ->label('Loyalty Members Only')
                                    ->default(false),
                                Forms\Components\Toggle::make('walk_ins')
                                    ->label('Walk-in Customers Only')
                                    ->default(false),
                                Forms\Components\Toggle::make('redeemed_rewards')
                                    ->label('Redeemed Rewards Only')
                                    ->default(false),
                                Forms\Components\Select::make('returned')
                                    ->label('Return Status')
                                    ->options([
                                        '' => 'All',
                                        '1' => 'Returned Only',
                                        '0' => 'Not Returned Only',
                                    ])
                                    ->default(''),
                                Forms\Components\Select::make('month')
                                    ->label('Filter by Month')
                                    ->options([
                                        '' => 'All Months',
                                        '1' => 'January',
                                        '2' => 'February',
                                        '3' => 'March',
                                        '4' => 'April',
                                        '5' => 'May',
                                        '6' => 'June',
                                        '7' => 'July',
                                        '8' => 'August',
                                        '9' => 'September',
                                        '10' => 'October',
                                        '11' => 'November',
                                        '12' => 'December',
                                    ])
                                    ->default(''),
                            ]),
                    ])
                    ->action(function (array $data) {
                        try {
                            // Clean up filters - remove empty values
                            $filters = array_filter($data, function ($value) {
                                return $value !== '' && $value !== null && $value !== false;
                            });

                            // Generate filename with timestamp
                            $timestamp = now()->format('Y-m-d_H-i-s');
                            $filename = "revenue_report_{$timestamp}.xlsx";

                            // Export the file
                            return Excel::download(new RevenueReportExport($filters), $filename);
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Export Failed')
                                ->body('There was an error generating the export: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->modalHeading('Export Revenue Report')
                    ->modalDescription('Configure your export filters and download the Excel file.')
                    ->modalSubmitActionLabel('Download Excel'),
            ])
            ->emptyStateHeading('No Revenue Data Found')
            ->emptyStateDescription('Start by creating rental records to see revenue reports.')
            ->emptyStateIcon('heroicon-o-currency-dollar');
    }

    public static function getRelations(): array
    {
        return [
            // Add relation managers if needed
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReports::route('/'),
            'create' => Pages\CreateReport::route('/create'),
            'edit' => Pages\EditReport::route('/{record}/edit'),
        ];
    }

    // public static function getNavigationBadge(): ?string
    // {
    //     // // Show total revenue for current month
    //     // $currentMonthRevenue = RentalManagement::whereMonth('created_at', now()->month)
    //     //     ->whereYear('created_at', now()->year)
    //     //     ->whereNotNull('price_paid')
    //     //     ->sum('price_paid');

    //     // return '₱' . number_format($currentMonthRevenue, 2);
    // }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }
}
