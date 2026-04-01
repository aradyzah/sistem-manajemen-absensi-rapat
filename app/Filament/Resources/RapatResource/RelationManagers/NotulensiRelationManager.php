<?php

namespace App\Filament\Resources\RapatResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingService;

class NotulensiRelationManager extends RelationManager
{
    protected static string $relationship = 'notulensi';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Notulensi')
                    ->schema([
                        Forms\Components\TextInput::make('pimpinan_rapat')
                            ->label('Pimpinan Rapat')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('sekretaris')
                            ->label('Sekretaris (Notulis)')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),
                
                Forms\Components\Section::make('Hasil Rapat')
                    ->schema([
                        Forms\Components\RichEditor::make('isi_notulensi')
                            ->label('Isi Notulensi / Pembahasan')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('daftar_keputusan')
                            ->label('Daftar Keputusan')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('tindak_lanjut')
                            ->label('Tindak Lanjut')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('pimpinan_rapat')
            ->columns([
                Tables\Columns\TextColumn::make('pimpinan_rapat')->label('Pimpinan'),
                Tables\Columns\TextColumn::make('sekretaris')->label('Sekretaris'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Notulensi')
                    ->visible(fn (RelationManager $livewire) => $livewire->getOwnerRecord()->notulensi === null),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
