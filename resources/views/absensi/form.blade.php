@extends('layouts.app')

@section('content')
        <div class="max-w-4xl mx-auto transform transition-all duration-300 hover:scale-[1.005]">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Header Section -->
                <div style="background-color: #002147;" class="px-8 py-6">
                    <div class="mb-6">
                        <a href="/" class="inline-flex items-center text-blue-200 hover:text-white transition-all group">
                            <x-heroicon-o-arrow-left class="h-5 w-5 mr-2 transform group-hover:-translate-x-1 transition-transform" />
                            <span class="text-sm font-medium text-white/90">Kembali ke Beranda</span>
                        </a>
                    </div>

                    <div class="space-y-3">
                        <h1 class="text-2xl font-bold text-white leading-tight">
                            {{ $rapat->agenda_rapat }}
                        </h1>

                        <div class="flex items-center text-sm text-white">
                            <x-heroicon-o-calendar class="h-5 w-5 mr-2" />
                            {{ \Carbon\Carbon::parse($rapat->tanggal_rapat)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                        </div>
                        
                        <div class="text-sm text-white space-y-3">
                            @if ($rapat->jenis_rapat === 'online')
                                <div class="flex items-center">
                                    <x-heroicon-o-link class="h-5 w-5 mr-2" />
                                    <a href="{{ $rapat->link_meeting }}" class="underline text-blue-200" target="_blank">Link Meeting</a>
                                </div>
                            @elseif ($rapat->jenis_rapat === 'offline')
                                <div class="flex items-center">
                                    <x-heroicon-o-map-pin class="h-5 w-5 mr-2" />
                                    {{ $rapat->lokasi_rapat }}
                                </div>
                            @elseif ($rapat->jenis_rapat === 'hybrid')
                                <div class="flex items-center">
                                    <x-heroicon-o-map-pin class="h-5 w-5 mr-2" />
                                    {{ $rapat->lokasi_rapat }}
                                </div>
                                <div class="flex items-center">
                                    <x-heroicon-o-link class="h-5 w-5 mr-2" />
                                    <a href="{{ $rapat->link_meeting }}" class="underline text-blue-200" target="_blank">Link Meeting</a>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center text-sm text-white">
                            <x-heroicon-o-clock class="h-5 w-5 mr-2" />
                            {{ \Carbon\Carbon::parse($rapat->waktu_mulai)->format('H:i') }} -
                            {{ $rapat->waktu_selesai ? \Carbon\Carbon::parse($rapat->waktu_selesai)->format('H:i') : 'selesai' }} WITA
                        </div>
                    </div>
                </div>

                <!-- Success Message -->
                @if (session('success'))
                <div x-data="{ show: true }" 
                     x-show="show" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-4"
                     x-init="setTimeout(() => show = false, 5000)"
                     class="bg-green-50 border-l-4 border-green-500 p-4 mx-6 mt-6 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Form Section -->
                <form action="{{ url('/absensi/' . $rapat->link_absensi) }}" method="POST" class="px-8 py-0 pb-8 space-y-6">
                    @csrf

                    <!-- Personal Information Grid -->
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Jenis Peserta -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">Jenis Peserta</label>
                            <div class="flex items-center space-x-6 mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="status" value="pegawai" class="form-radio text-blue-600" checked>
                                    <span class="ml-2 text-gray-700">Pegawai</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="status" value="eksternal" class="form-radio text-blue-600">
                                    <span class="ml-2 text-gray-700">Eksternal</span>
                                </label>
                            </div>
                        </div>

                        <!-- Name Field -->
                        <div class="space-y-1">
                            <label for="nama" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <div class="relative rounded-md">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <input type="text" id="nama" name="nama"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                    placeholder="Masukkan nama lengkap" required>
                            </div>
                        </div>

                        <!-- NIP/NIK Field -->
                        <div class="space-y-1">
                            <label for="nip_nik" id="label-nip-nik" class="block text-sm font-medium text-gray-700">NIP/NIK</label>
                            <div class="relative rounded-md">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </div>
                                <input type="text" id="nip_nik" name="nip_nik"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                    placeholder="Masukkan NIP/NIK">
                            </div>
                        </div>

                        <!-- Work Unit Field -->
                        <div class="space-y-1">
                            <label for="unit_kerja" class="block text-sm font-medium text-gray-700">Unit Kerja</label>
                            <div class="relative rounded-md">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1" />
                                    </svg>
                                </div>
                                <input type="text" id="unit_kerja" name="unit_kerja"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                    placeholder="Masukkan unit kerja" required>
                            </div>
                        </div>

                        <!-- Position Field -->
                        <div class="space-y-1">
                            <label for="jabatan_tugas" class="block text-sm font-medium text-gray-700">Jabatan/Tugas</label>
                            <div class="relative rounded-md">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <input type="text" id="jabatan_tugas" name="jabatan_tugas"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                    placeholder="Masukkan jabatan atau tugas" required>
                            </div>
                        </div>

                        <!-- === FITUR 1: Hybrid Toggle Section === -->
                        @if($rapat->jenis_rapat === 'hybrid')
                        <div id="hybrid-toggle-section" class="space-y-4 p-5 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl border border-blue-100 shadow-sm animate__animated animate__fadeIn">
                            <div class="flex items-center space-x-2">
                                <div class="p-1.5 bg-blue-600 rounded-lg">
                                    <x-heroicon-o-adjustments-horizontal class="h-4 w-4 text-white" />
                                </div>
                                <label class="block text-sm font-bold text-blue-900">Metode Kehadiran <span class="text-red-500">*</span></label>
                            </div>
                            
                            <p class="text-xs text-blue-700 leading-relaxed">Rapat ini bersifat <span class="font-bold">Hybrid</span>. Silakan pilih bagaimana Anda mengikuti rapat ini:</p>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Opsi Offline -->
                                <label class="relative flex flex-col p-4 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 transition-all group shadow-sm" id="label-offline">
                                    <input type="radio" name="metode_kehadiran" value="Offline" class="sr-only peer" required>
                                    
                                    <!-- Background & Border Highlight (Stay when checked) -->
                                    <div class="absolute inset-0 rounded-xl border-2 border-transparent peer-checked:border-blue-600 peer-checked:bg-blue-50/50 pointer-events-none transition-all"></div>
                                    
                                    <!-- Check Icon (Stay when checked) -->
                                    <div class="absolute top-3 right-3 hidden peer-checked:block text-blue-600 animate__animated animate__zoomIn z-10">
                                        <x-heroicon-o-check-circle class="h-6 w-6" />
                                    </div>
                                    
                                    <div class="relative z-10 flex items-center space-x-3 mb-2">
                                        <div class="p-2 bg-green-50 rounded-lg group-hover:bg-green-100 transition-colors">
                                            <x-heroicon-o-map-pin class="h-5 w-5 text-green-600" />
                                        </div>
                                        <span class="font-bold text-gray-900">Offline</span>
                                    </div>
                                    <span class="relative z-10 text-xs text-gray-500">Hadir langsung di lokasi rapat (Luring)</span>
                                </label>

                                <!-- Opsi Online -->
                                <label class="relative flex flex-col p-4 bg-white border-2 border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 transition-all group shadow-sm" id="label-online">
                                    <input type="radio" name="metode_kehadiran" value="Online" class="sr-only peer" required>
                                    
                                    <!-- Background & Border Highlight (Stay when checked) -->
                                    <div class="absolute inset-0 rounded-xl border-2 border-transparent peer-checked:border-blue-600 peer-checked:bg-blue-50/50 pointer-events-none transition-all"></div>
                                    
                                    <!-- Check Icon (Stay when checked) -->
                                    <div class="absolute top-3 right-3 hidden peer-checked:block text-blue-600 animate__animated animate__zoomIn z-10">
                                        <x-heroicon-o-check-circle class="h-6 w-6" />
                                    </div>
                                    
                                    <div class="relative z-10 flex items-center space-x-3 mb-2">
                                        <div class="p-2 bg-blue-50 rounded-lg group-hover:bg-blue-100 transition-colors">
                                            <x-heroicon-o-video-camera class="h-5 w-5 text-blue-600" />
                                        </div>
                                        <span class="font-bold text-gray-900">Online</span>
                                    </div>
                                    <span class="relative z-10 text-xs text-gray-500">Hadir melalui link meeting (Daring)</span>
                                </label>
                            </div>

                            <!-- Feedback Validasi Lokasi -->
                            <div id="location-feedback" class="hidden mt-3 p-4 rounded-xl text-sm flex items-start space-x-3 animate__animated animate__fadeIn border">
                                <div id="feedback-icon-container" class="flex-shrink-0 mt-0.5"></div>
                                <div class="flex-grow">
                                    <p id="feedback-message" class="font-semibold"></p>
                                    <p id="feedback-detail" class="text-xs mt-1 opacity-80"></p>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Hidden Field untuk Data Lokasi -->
                        <input type="hidden" name="location_data" id="location_data">

                        <!-- Field untuk Eksternal -->
                        <div id="eksternal-fields" class="space-y-6 hidden">
                            <!-- Instansi -->
                            <div class="space-y-1">
                                <label for="instansi" class="block text-sm font-medium text-gray-700">Instansi</label>
                                <div class="relative rounded-md">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Zm0 3h.008v.008h-.008v-.008Z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="instansi" name="instansi"
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 transition duration-150 ease-in-out"
                                        placeholder="Masukkan nama instansi">
                                </div>
                            </div>

                            <!-- No. Telepon -->
                            <div class="space-y-1">
                                <label for="no_telepon" class="block text-sm font-medium text-gray-700">No. Telepon <span class="text-red-400 font-">(opsional)</span></label>
                                <div class="relative rounded-md">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                        </svg>
                                    </div>
                                    <input type="text" id="no_telepon" name="no_telepon"
                                        class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md transition duration-150 ease-in-out"
                                        placeholder="Masukkan no telepon">
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="space-y-1">
                                <label for="email" class="block text-sm font-medium text-gray-700">Email <span class="text-red-400">(opsional)</span></label>
                                <div class="relative rounded-md">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                        </svg>
                                    </div>
                                    <input type="email" id="email" name="email"
                                        class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md transition duration-150 ease-in-out"
                                        placeholder="Masukkan email">
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Signature Section -->
                    <div class="mt-8">
                        <label for="signature-pad" class="block text-sm font-medium text-gray-700 mb-2">Tanda Tangan</label>
                        <div class="border-2  border-gray-300 rounded-xl p-0 bg-gray-50 hover:border-blue-400 transition duration-200">
                            <div class="relative h-60">
                                <canvas id="signature-pad" class="w-full h-full rounded-lg bg-white shadow-inner absolute top-0 left-0"></canvas>
                            </div>
                        </div>
                        <input type="hidden" name="tanda_tangan" id="tanda_tangan">
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row sm:justify-between gap-4 mt-10">
                        <button type="button" onclick="clearSignature()" 
                            class="inline-flex justify-center items-center px-5 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:-translate-y-0.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Clear Tanda Tangan
                        </button>
                        <button type="submit" 
                            class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const defaultStatus = document.querySelector('input[name="status"]:checked')?.value || 'pegawai';
    toggleEksternalFields(defaultStatus, false); // jangan kosongkan saat pertama
    updateFieldRequirements(defaultStatus);

    const canvas = document.getElementById('signature-pad');
    const signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgb(255, 255, 255)',
        penColor: 'rgb(16, 24, 39)',
        minWidth: 1,
        maxWidth: 3,
        velocityFilterWeight: 0.7
    });

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear();
    }

    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    // === Handle Form Submit with SweetAlert2 ===
    const form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // 1. Validasi Tanda Tangan
        if (signaturePad.isEmpty()) {
            Swal.fire({
                icon: 'error',
                title: 'Tanda Tangan Kosong',
                text: 'Harap berikan tanda tangan Anda sebelum mengirim',
                confirmButtonColor: '#3B82F6'
            });
            return;
        }

        // 2. Persiapkan Data
        document.getElementById('tanda_tangan').value = signaturePad.toDataURL('image/png');
        const formData = new FormData(form);

        // 3. Tampilkan Loading
        Swal.fire({
            title: 'Memproses Absensi...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // 4. Kirim Data via AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(async response => {
            const data = await response.json();
            
            if (response.ok) {
                // Berhasil
                Swal.fire({
                    icon: 'success',
                    title: 'Absensi Berhasil!',
                    text: data.message || 'Data kehadiran Anda telah tercatat di sistem.',
                    confirmButtonColor: '#059669',
                    confirmButtonText: 'Selesai'
                }).then(() => {
                    // Redirect ke beranda
                    window.location.href = '/'; 
                });
            } else {
                // Gagal (Error Validasi)
                let errorMessages = '';
                if (data.errors) {
                    errorMessages = Object.values(data.errors).flat().join('<br>');
                } else {
                    errorMessages = data.message || 'Terjadi kesalahan saat memproses data.';
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Absensi Gagal',
                    html: `<div class="text-left text-sm">${errorMessages}</div>`,
                    confirmButtonColor: '#DC2626'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan Sistem',
                text: 'Gagal terhubung ke server. Silakan coba lagi nanti.',
                confirmButtonColor: '#DC2626'
            });
        });
    });

    window.clearSignature = function () {
        signaturePad.clear();
    }

    // Toggle eksternal field dan reset jika perlu
    function toggleEksternalFields(status, shouldClear = true) {
        const eksternalFields = document.getElementById('eksternal-fields');
        const nipLabel = document.getElementById('label-nip-nik');

        // Update label opsional
        if (status === 'eksternal') {
            eksternalFields.classList.remove('hidden');
            if (nipLabel && !nipLabel.innerHTML.includes('opsional')) {
                nipLabel.innerHTML += ' <span class="text-red-400">(opsional)</span>';
            }
        } else {
            eksternalFields.classList.add('hidden');
            if (nipLabel) {
                nipLabel.innerHTML = 'NIP/NIK'; // reset label jika pegawai
            }

            // Kosongkan nilai eksternal saat kembali ke pegawai
            ['instansi', 'no_telepon', 'email'].forEach(id => {
                document.getElementById(id).value = '';
            });
        }

        if (shouldClear) {
            ['nama', 'nip_nik', 'unit_kerja', 'jabatan_tugas', 'instansi', 'no_telepon', 'email'].forEach(id => {
                const field = document.getElementById(id);
                if (field) field.value = '';
            });
        }

        updateFieldRequirements(status);
    }

    // Ganti jenis peserta → reset field
    document.querySelectorAll('input[name="status"]').forEach(radio => {
        radio.addEventListener('change', function () {
            toggleEksternalFields(this.value, true); // kosongkan jika user ganti
            updateFieldRequirements(this.value); 
        });
    });

    function updateFieldRequirements(status) {
    const nipField = document.getElementById('nip_nik');
    const instansiField = document.getElementById('instansi');
    const optionalEksternalFields = ['nip_nik', 'no_telepon', 'email'];

    // NIP wajib jika pegawai, opsional jika eksternal
    if (nipField) {
        if (status === 'pegawai') {
            nipField.setAttribute('required', 'required');
        } else {
            nipField.removeAttribute('required');
        }
    }

    // Instansi wajib hanya untuk eksternal
    if (instansiField) {
        if (status === 'eksternal') {
            instansiField.setAttribute('required', 'required');
        } else {
            instansiField.removeAttribute('required');
        }
    }

    // Optional untuk eksternal
    optionalEksternalFields.forEach(id => {
        const field = document.getElementById(id);
        if (field && status === 'eksternal') {
            field.removeAttribute('required');
        }
    });
}

    // Autofill berdasarkan NIP/NIK
    document.getElementById('nip_nik').addEventListener('change', function () {
    const nip = this.value;
    const uuid = "{{ $rapat->link_absensi }}";
    const status = document.querySelector('input[name="status"]:checked')?.value;

    if (status !== 'pegawai') return; // Hanya autofill jika pegawai

    fetch(`/absensi/${uuid}/cek-nip?nip_nik=${nip}`)
        .then(res => res.json())
        .then(data => {
            if (!data) return;

            document.getElementById('nama').value ||= data.nama || '';
            document.getElementById('unit_kerja').value ||= data.unit_kerja || '';
            document.getElementById('jabatan_tugas').value ||= data.jabatan_tugas || '';
        });
    });

    // === FITUR 1: Hybrid Toggle & Lokasi Validation Logic ===
    const uuid = "{{ $rapat->link_absensi }}";
    const hybridSection = document.getElementById('hybrid-toggle-section');
    const locationFeedback = document.getElementById('location-feedback');
    const feedbackMessage = document.getElementById('feedback-message');
    const feedbackDetail = document.getElementById('feedback-detail');
    const feedbackIconContainer = document.getElementById('feedback-icon-container');
    const locationDataInput = document.getElementById('location_data');

    // 1. Cek jenis rapat saat load
    fetch(`/api/rapat/${uuid}/jenis`)
        .then(res => res.json())
        .then(response => {
            if (response.success && response.data.show_hybrid_toggle) {
                hybridSection.classList.remove('hidden');
                // Tambahkan required attribute secara dinamis
                document.querySelectorAll('input[name="metode_kehadiran"]').forEach(input => {
                    input.setAttribute('required', 'required');
                });
            }
        });

    // 2. Handle perubahan metode kehadiran
    document.querySelectorAll('input[name="metode_kehadiran"]').forEach(input => {
        input.addEventListener('change', function() {
            const metode = this.value;
            
            if (metode === 'Offline') {
                validateLocationRealtime();
            } else {
                // Sembunyikan feedback jika pilih Online
                locationFeedback.classList.add('hidden');
                locationDataInput.value = '';
            }
        });
    });

    // 3. Fungsi Validasi Lokasi Real-time
    function validateLocationRealtime() {
        // Tampilkan loading state
        showFeedback('Memvalidasi lokasi...', 'Sedang mengecek koneksi jaringan dan koordinat GPS...', 'loading');

        // Coba ambil GPS (opsional)
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const latLng = `${position.coords.latitude},${position.coords.longitude}`;
                    locationDataInput.value = latLng;
                    sendValidationRequest('Offline', latLng);
                },
                (error) => {
                    console.warn("Geolocation error:", error.message);
                    // Jika GPS gagal, tetap kirim request (fallback ke IP di backend)
                    sendValidationRequest('Offline', null);
                },
                { timeout: 5000 }
            );
        } else {
            sendValidationRequest('Offline', null);
        }
    }

    function sendValidationRequest(metode, locationData) {
        fetch(`/api/rapat/${uuid}/validasi-lokasi`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                metode_kehadiran: metode,
                location_data: locationData
            })
        })
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                if (response.is_lokasi_valid) {
                    showFeedback('Lokasi Valid', response.catatan, 'success');
                } else {
                    showFeedback('Lokasi Tidak Terverifikasi', response.catatan, 'error');
                }
            }
        })
        .catch(err => {
            console.error("Validation error:", err);
            showFeedback('Gagal Validasi', 'Terjadi kesalahan saat menghubungi server.', 'error');
        });
    }

    function showFeedback(title, message, type) {
        locationFeedback.classList.remove('hidden', 'bg-green-50', 'bg-red-50', 'bg-blue-50', 'border-green-200', 'border-red-200', 'border-blue-200');
        
        let icon = '';
        let bgColor = '';
        let borderColor = '';
        let textColor = '';

        if (type === 'success') {
            bgColor = 'bg-green-50';
            borderColor = 'border-green-200';
            textColor = 'text-green-800';
            icon = '<svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        } else if (type === 'error') {
            bgColor = 'bg-red-50';
            borderColor = 'border-red-200';
            textColor = 'text-red-800';
            icon = '<svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>';
        } else {
            bgColor = 'bg-blue-50';
            borderColor = 'border-blue-200';
            textColor = 'text-blue-800';
            icon = '<svg class="h-6 w-6 text-blue-600 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>';
        }

        locationFeedback.classList.add(bgColor, borderColor);
        feedbackMessage.className = `font-bold ${textColor}`;
        feedbackMessage.textContent = title;
        feedbackDetail.textContent = message;
        feedbackIconContainer.innerHTML = icon;
    }
});
</script>
@endpush
@endsection
