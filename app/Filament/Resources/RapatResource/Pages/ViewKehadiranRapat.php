<?php

namespace App\Filament\Resources\RapatResource\Pages;

use App\Filament\Resources\RapatResource;
use App\Models\KehadiranRapat;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;

class ViewKehadiranRapat extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = RapatResource::class;
    protected static string $view = 'filament.resources.rapat-resource.pages.view-kehadiran-rapat';

    public $record;

    public function getTitle(): string
    {
        return 'Daftar Kehadiran Rapat';
    }


    public function getTableQuery(): Builder
    {
        return KehadiranRapat::query()->where('rapat_id', $this->record);
    }

    public function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('nama')
                ->label('Nama')
                ->searchable()
                ->sortable()
                ->weight('bold'),

            Tables\Columns\TextColumn::make('nip_nik')
                ->label('NIP/NIK')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('unit_kerja')
                ->label('Unit Kerja')
                ->searchable(),

            Tables\Columns\TextColumn::make('metode_kehadiran')
                ->label('Metode')
                ->badge()
                ->color(fn (?string $state): string => match ($state) {
                    'Online'  => 'info',
                    'Offline' => 'success',
                    default   => 'gray',
                }),

            Tables\Columns\IconColumn::make('is_lokasi_valid')
                ->label('Lokasi')
                ->boolean()
                ->trueIcon('heroicon-o-check-circle')
                ->falseIcon('heroicon-o-x-circle')
                ->trueColor('success')
                ->falseColor('danger')
                ->tooltip(fn ($record) => $record->catatan_validasi ?? '-'),

            Tables\Columns\ImageColumn::make('tanda_tangan')
                ->label('TTD')
                ->disk('public') // Pastikan disk sesuai konfigurasi
                ->width(100)
                ->height(50),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Waktu Absen')
                ->dateTime('H:i')
                ->sortable(),
        ];
    }

    /**
     * Define the form for Notulensi (Method B)
     */
    protected function getForms(): array
    {
        return [
            'notulensiForm' => $this->makeForm()
                ->schema([
                    \Filament\Forms\Components\Section::make('Notulensi Rapat')
                        ->description('Catat hasil pembahasan, keputusan, dan tindak lanjut rapat di sini.')
                        ->schema([
                            \Filament\Forms\Components\Grid::make(2)
                                ->schema([
                                    TextInput::make('pimpinan_rapat')
                                        ->label('Pimpinan Rapat')
                                        ->placeholder('Nama Pimpinan yang memimpin rapat')
                                        ->required(),
                                    TextInput::make('sekretaris')
                                        ->label('Sekretaris/Notulis')
                                        ->placeholder('Nama orang yang mencatat rapat')
                                        ->required(),
                                ]),
                            \Filament\Forms\Components\RichEditor::make('isi_notulensi')
                                ->label('Pembahasan Rapat')
                                ->placeholder('Tuliskan detail jalannya rapat di sini...')
                                ->required()
                                ->columnSpanFull(),
                            \Filament\Forms\Components\Grid::make(2)
                                ->schema([
                                    \Filament\Forms\Components\RichEditor::make('daftar_keputusan')
                                        ->label('Daftar Keputusan')
                                        ->placeholder('Tuliskan poin-poin keputusan yang disepakati')
                                        ->columnSpan(1),
                                    \Filament\Forms\Components\RichEditor::make('tindak_lanjut')
                                        ->label('Tindak Lanjut')
                                        ->placeholder('Tuliskan rencana aksi setelah rapat ini')
                                        ->columnSpan(1),
                                ]),
                        ])
                        ->footerActions([
                            \Filament\Forms\Components\Actions\Action::make('saveNotulensi')
                                ->label('Simpan Notulensi')
                                ->submit('notulensiForm')
                                ->color('primary')
                                ->icon('heroicon-o-check-circle'),
                        ]),
                ])
                ->statePath('notulensiData'),
        ];
    }

    public $notulensiData = [];

    public function mount($record): void
    {
        $this->record = $record;
        
        // Load existing notulensi data if available
        $rapat = \App\Models\Rapat::find($this->record);
        if ($rapat && $rapat->notulensi) {
            $this->notulensiForm->fill($rapat->notulensi->toArray());
        } else {
            // Default fill if empty
            $this->notulensiForm->fill([
                'sekretaris' => auth()->user()->name,
            ]);
        }
    }

    public function saveNotulensi(): void
    {
        $data = $this->notulensiForm->getState();
        
        \App\Models\Notulensi::updateOrCreate(
            ['rapat_id' => $this->record],
            $data
        );

        \Filament\Notifications\Notification::make()
            ->title('Notulensi Berhasil Disimpan')
            ->success()
            ->send();
    }


    public function getTableHeaderActions(): array
    {
        return [
            Tables\Actions\Action::make('Export PDF')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('rapats.kehadiran.export', $this->record))
                ->openUrlInNewTab(),
            Tables\Actions\CreateAction::make()
                ->label('+ Peserta Rapat')
                ->disableCreateAnother()
                ->modalHeading('Tambah Kehadiran')
                ->modalSubmitActionLabel('Simpan')
                ->form([
                    \Filament\Forms\Components\Select::make('status')
                        ->label('Jenis Peserta')
                        ->options([
                            'pegawai' => 'Pegawai',
                            'eksternal' => 'Eksternal',
                        ])
                        ->default('pegawai')
                        ->required()
                        ->reactive(),

                    TextInput::make('nama')->required(),
                    TextInput::make('nip_nik')->label('NIP/NIK')
                        ->required(fn ($get) => $get('status') === 'pegawai'),
                    TextInput::make('unit_kerja')->required(),
                    TextInput::make('jabatan_tugas')->label('Jabatan/Tugas')->required(),

                    TextInput::make('instansi')
                        ->required(fn ($get) => $get('status') === 'eksternal')
                        ->visible(fn ($get) => $get('status') === 'eksternal'),
                    TextInput::make('no_telepon')
                        ->label('No. Telepon')
                        ->visible(fn ($get) => $get('status') === 'eksternal'),
                    TextInput::make('email')
                        ->email()
                        ->visible(fn ($get) => $get('status') === 'eksternal'),

                    TextInput::make('tanda_tangan')->required(),
                ])
                ->mutateFormDataUsing(fn ($data) => array_merge($data, ['rapat_id' => $this->record]))
                ->after(function () {
                    $this->dispatch('notify', [
                        'title' => 'Kehadiran berhasil ditambahkan!',
                        'type' => 'success',
                    ]);
                }),
        ];
    }

    public function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make()
                ->form([
                    TextInput::make('status')
                    ->label('Jenis Peserta')
                    ->disabled() // Nonaktifkan agar tidak bisa diubah
                    ->default(fn ($get) => ucfirst($get('status'))),

                    TextInput::make('nama')->required(),
                    TextInput::make('nip_nik')->label('NIP/NIK')
                    ->required(fn ($get) => $get('status') === 'pegawai'),
                    TextInput::make('unit_kerja')->required(),
                    TextInput::make('jabatan_tugas')->required(),

                    TextInput::make('instansi')
                        ->required(fn ($get) => $get('status') === 'eksternal')
                        ->visible(fn ($get) => $get('status') === 'eksternal'),
                    TextInput::make('no_telepon')
                        ->visible(fn ($get) => $get('status') === 'eksternal'),
                    TextInput::make('email')
                        ->email()
                        ->visible(fn ($get) => $get('status') === 'eksternal'),

                    TextInput::make('tanda_tangan')
                        ->required(),
                ]),
            Tables\Actions\DeleteAction::make(),
        ];
    }
}