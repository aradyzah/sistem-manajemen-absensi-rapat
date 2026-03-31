<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KehadiranRapatResource\Pages;
use App\Models\KehadiranRapat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;

class KehadiranRapatResource extends Resource
{
    protected static ?string $model = KehadiranRapat::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Manajemen Rapat';
    protected static ?string $navigationLabel = 'Kehadiran Rapat';
    protected static ?int $navigationSort = 3;
    protected static ?string $pluralModelLabel = 'Kehadiran Rapat';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Define your form fields here
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->searchable()->sortable(),
                TextColumn::make('nip_nik')->label('NIP/NIK')->searchable()->sortable(),
                TextColumn::make('unit_kerja')->searchable()->sortable(),
                TextColumn::make('jabatan_tugas')->searchable()->sortable(),
                TextColumn::make('metode_kehadiran')
                    ->label('Metode')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'Online'  => 'info',
                        'Offline' => 'success',
                        default   => 'gray',
                    })
                    ->sortable(),

                IconColumn::make('is_lokasi_valid')
                    ->label('Lokasi Valid')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->tooltip(fn ($record) => $record->catatan_validasi ?? '-'),

                TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('catatan_validasi')
                    ->label('Catatan Validasi')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('metode_kehadiran')
                    ->label('Metode Kehadiran')
                    ->options([
                        'Online'  => 'Online (Daring)',
                        'Offline' => 'Offline (Luring)',
                    ]),

                SelectFilter::make('is_lokasi_valid')
                    ->label('Status Validasi Lokasi')
                    ->options([
                        '1' => 'Valid',
                        '0' => 'Tidak Valid',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListKehadiranRapats::route('/'),
            'create' => Pages\CreateKehadiranRapat::route('/create'),
            'edit' => Pages\EditKehadiranRapat::route('/{record}/edit'),
        ];
    }
}
