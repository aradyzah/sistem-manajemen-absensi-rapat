<x-filament-panels::page>
    <div class="mb-4">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
            Agenda Rapat: {{ \App\Models\Rapat::find($record)->agenda_rapat }}
        </h2>

        @php
            $rapat = \App\Models\Rapat::find($record);
            \Carbon\Carbon::setLocale('id'); // Mengatur bahasa ke Indonesia
            $tanggal = $rapat && $rapat->tanggal_rapat ? \Carbon\Carbon::parse($rapat->tanggal_rapat)->translatedFormat('l, d F Y') : 'Tanggal tidak tersedia';
            $waktu = $rapat && $rapat->waktu_mulai ? \Carbon\Carbon::parse($rapat->waktu_mulai)->format('H:i') : 'Waktu tidak tersedia';
        @endphp

        <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-gray-600 dark:text-gray-400">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-500 dark:text-gray-400"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                        clip-rule="evenodd" />
                </svg>
                {{ $tanggal }}
            </div>
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-500 dark:text-gray-400"
                    viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.414-1.414L11 9.586V6z"
                        clip-rule="evenodd" />
                </svg>
                Pukul {{ $waktu }} WITA
            </div>
        </div>
    </div>

    {{ $this->table }}

    <div class="mt-8">
        <form wire:submit="saveNotulensi">
            {{ $this->notulensiForm }}
        </form>
    </div>
</x-filament-panels::page>