<?php

namespace App\Filament\Resources\Wallets\Tables;

use App\Services\WalletService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WalletsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.student_number')->label('Student No.')->searchable(),
                TextColumn::make('student.full_name')->label('Student'),
                TextColumn::make('balance')->money('NGN')->sortable(),
                TextColumn::make('currency'),
                TextColumn::make('status')->badge(),
            ])
            ->recordActions([
                Action::make('fund')
                    ->label('Fund Wallet')
                    ->icon('heroicon-o-banknotes')
                    ->form([
                        TextInput::make('amount')->numeric()->required()->minValue(1)->prefix('₦'),
                        TextInput::make('description')->maxLength(255),
                    ])
                    ->action(function ($record, array $data, WalletService $walletService) {
                        $walletService->fund(
                            $record,
                            (float) $data['amount'],
                            auth()->user(),
                            $data['description'] ?? null,
                        );
                    })
                    ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'finance'])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
