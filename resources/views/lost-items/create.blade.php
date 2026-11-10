@extends('layouts.app')

@section('content')

<div class="space-y-6 max-w-7xl mx-auto" x-data="{
    step: {{ $errors->has('identity_no') || $errors->has('phone') ? 4 : ($errors->has('photo_url') || $errors->has('photo_files') || $errors->has('photo_files.0') || $errors->has('photo_files.1') || $errors->has('photo_files.2') ? 3 : ($errors->has('last_seen_location') || $errors->has('last_seen_date') || $errors->has('last_seen_time') ? 2 : 1)) }},
    title: @js(old('title', 'Dompet Kulit Cokelat Braun Buffel')),
    category: 'Elektronik',
    location: @js(old('last_seen_location', 'Perpustakaan Pusat')),
    color: @js(old('primary_color', 'Cokelat Tua')),
    identityNo: @js(old('identity_no', Auth::user()->identity_no ?? '')),
    identityNoError: '',
    validateIdentityNo() {
        if (!this.identityNo) {
            this.identityNoError = '';
            return;
        }
        if (!/^[0-9]+$/.test(this.identityNo)) {
            this.identityNoError = 'NIM / NIP hanya boleh berisi angka, tidak boleh mengandung huruf atau simbol.';
        } else {
            this.identityNoError = '';
        }
    },
    filterIdentityInput(e) {
        // Only allow numbers and navigation keys
        const allowedKeys = ['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Home', 'End'];
        if (allowedKeys.includes(e.key) || e.ctrlKey || e.metaKey) {
            return;
        }
        if (!/^[0-9]$/.test(e.key)) {
            e.preventDefault();
        }
    },
    sanitizeIdentityInput() {
        if (this.identityNo) {
            this.identityNo = this.identityNo.replace(/[^0-9]/g, '');
        }
        this.validateIdentityNo();
    },
    phone: @js(old('phone', Auth::user()->phone ?? '')),
    phoneError: '',
    validatePhone() {
        if (!this.phone) {
            this.phoneError = '';
            return;
        }
        if (this.phone.length > 13) {
            this.phoneError = 'Nomor telepon maksimal 13 karakter (saat ini: ' + this.phone.length + ' karakter).';
        } else if (!/^(\+628[1-9][0-9]{5,9}|628[1-9][0-9]{6,10}|08[1-9][0-9]{6,10})$/.test(this.phone)) {
            this.phoneError = 'Format nomor telepon tidak valid. Gunakan format seperti 081234567890 (maksimal 13 digit).';
        } else {
            this.phoneError = '';
        }
    },
    filterPhoneInput(e) {
        // Prevent typing beyond 13 characters
        if (this.phone && this.phone.length >= 13 && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab'].includes(e.key) && !e.ctrlKey && !e.metaKey) {
            e.preventDefault();
        }
    },
    photoUrl: @js(old('photo_url', '')),
    defaultPhoto: 'https://images.unsplash.com/photo-1627123424574-724758594e93?w=500&auto=format&fit=crop',
    selectedFiles: [],
    photoError: '',
    dragging: false,
    goToStep(stepNumber) {
        this.step = stepNumber;
    },
    get photoLimit() {
        return this.photoUrl.trim() ? 2 : 3;
    },
    handleFiles(fileList) {
        const files = [...this.selectedFiles.map((photo) => photo.file), ...Array.from(fileList)];
        const validFiles = [];
        let error = '';

        for (const file of files) {
            if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                error = 'Format foto harus JPG, PNG, atau WEBP.';
                continue;
            }

            if (file.size > 5 * 1024 * 1024) {
                error = 'Ukuran setiap foto maksimal 5 MB.';
                continue;
            }

            if (validFiles.length >= this.photoLimit) {
                error = 'Maksimal 3 foto, termasuk foto dari URL.';
                continue;
            }

            validFiles.push(file);
        }

        this.selectedFiles.forEach((photo) => URL.revokeObjectURL(photo.preview));
        this.selectedFiles = validFiles.map((file) => ({
            file,
            name: file.name,
            preview: URL.createObjectURL(file),
        }));

        const transfer = new DataTransfer();
        this.selectedFiles.forEach((photo) => transfer.items.add(photo.file));
        this.$refs.photoInput.files = transfer.files;
        this.photoError = error;
    },
    removePhoto(index) {
        const files = this.selectedFiles.filter((photo, photoIndex) => photoIndex !== index).map((photo) => photo.file);
        this.selectedFiles.forEach((photo) => URL.revokeObjectURL(photo.preview));
        this.selectedFiles = files.map((file) => ({
            file,
            name: file.name,
            preview: URL.createObjectURL(file),
        }));

        const transfer = new DataTransfer();
        this.selectedFiles.forEach((photo) => transfer.items.add(photo.file));
        this.$refs.photoInput.files = transfer.files;
        this.photoError = '';
    },
    validatePhotoCount() {
        this.photoError = this.selectedFiles.length > this.photoLimit
            ? 'Maksimal 3 foto, termasuk foto dari URL. Hapus foto unggahan atau kosongkan URL.'
            : '';
    },
    nextStep() {
        const currentSection = this.$refs['step' + this.step];
        const invalidField = currentSection.querySelector(':invalid');

        if (invalidField) {
            invalidField.reportValidity();
            return;
        }

        if (this.step === 3 && this.photoError) {
            return;
        }

        this.step = Math.min(this.step + 1, 4);
    },
    submitReport(event) {
        this.sanitizeIdentityInput();
        this.validateIdentityNo();
        this.validatePhone();
        if ((this.phone && this.phone.length > 13) || Boolean(this.identityNoError) || Boolean(this.phoneError)) {
            this.step = 4;
            return;
        }

        const invalidField = event.target.querySelector(':invalid');

        if (!invalidField) {
            event.target.submit();
            return;
        }

        this.step = Number(invalidField.closest('[data-step]').dataset.step);
        this.$nextTick(() => invalidField.reportValidity());
    }
}">

    <!-- Header & Back Button -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('dashboard.redirect') }}" class="text-xs font-bold text-slate-500 hover:text-blue-600 flex items-center gap-1 mb-2">
                &larr; Kembali ke Dashboard
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-3">
                <i data-lucide="search" class="w-8 h-8 text-blue-600"></i>
                Laporkan Barang Hilang
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Isi detail barang yang Anda cari secara komprehensif. Pencocokan otomatis AI KEMBALI akan langsung memeriksa repositori penemuan.
            </p>
        </div>
    </div>

    <nav aria-label="Tahapan laporan barang hilang" class="overflow-x-auto rounded-2xl border border-slate-200/80 bg-white p-4 text-xs font-bold shadow-sm">
        <ol class="flex min-w-[680px] items-center justify-between">
            @foreach(['Informasi Barang', 'Lokasi & Waktu', 'Foto & Ciri Khusus', 'Kontak & Privasi'] as $index => $label)
                @php($stepNumber = $index + 1)
                <li class="flex flex-1 items-center last:flex-none">
                    <button type="button"
                            @click="goToStep({{ $stepNumber }})"
                            :aria-current="step === {{ $stepNumber }} ? 'step' : null"
                            class="flex items-center gap-2 whitespace-nowrap transition-colors"
                            :class="step === {{ $stepNumber }} ? 'text-blue-600' : (step > {{ $stepNumber }} ? 'text-emerald-600' : 'text-slate-400')">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-extrabold"
                              :class="step === {{ $stepNumber }} ? 'bg-blue-600 text-white' : (step > {{ $stepNumber }} ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500')">
                            {{ $stepNumber }}
                        </span>
                        <span>{{ $label }}</span>
                    </button>
                    @if($stepNumber < 4)
                        <span class="mx-4 h-px flex-1 bg-slate-200" aria-hidden="true"></span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>

    <!-- Main Form Grid (2 Columns: Left Form, Right AI Preview Card - Matching PDF Page 2) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left Form Column -->
        <div class="lg:col-span-8 bg-white p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
            
            <form method="POST" action="{{ route('lost-items.store') }}" enctype="multipart/form-data" class="space-y-6" novalidate @submit.prevent="submitReport($event)">
                @csrf

                <!-- Section 1: Informasi Barang -->
                <section x-show="step === 1" x-cloak x-ref="step1" data-step="1" class="space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-base border-b border-slate-100 pb-2 flex items-center gap-2">
                        <i data-lucide="info" class="w-5 h-5 text-blue-600"></i>
                        1. Informasi Barang (Item Details)
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                                Kategori Barang <span class="text-rose-500">*</span>
                            </label>
                            <select name="category_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none">
                                <option value="">Pilih Kategori...</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                                Warna Utama Barang <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" 
                                   name="primary_color" 
                                   x-model="color"
                                   value="{{ old('primary_color', 'Cokelat Tua') }}"
                                   required 
                                   placeholder="Contoh: Hitam, Cokelat, Biru Navy" 
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none">
                        @error('primary_color') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                            Nama / Judul Barang <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="title" 
                               x-model="title"
                               value="{{ old('title', 'Dompet Kulit Cokelat Braun Buffel') }}"
                               required 
                               placeholder="Contoh: Dompet Kulit Cokelat Braun Buffel berisi KTP & ATM" 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none">
                        @error('title') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                            Merek / Brand / Pelindung (Opsional)
                        </label>
                        <input type="text" 
                               name="brand" 
                               value="{{ old('brand') }}"
                               placeholder="Contoh: Apple, Braun Buffel, Eiger, Casio" 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                            Deskripsi Detail & Ciri-Ciri Khusus <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="description"
                                  rows="4" 
                                  required 
                                  placeholder="Jelaskan spesifik: stiker di casing belakang, goresan halus di sudut kiri, isi kartu di dalam dompet, gantungan kunci anime..." 
                                  class="w-full p-4 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none">{{ old('description') }}</textarea>
                        @error('description') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </section>

                <!-- Section 2: Lokasi & Waktu Terakhir Dilihat -->
                <section x-show="step === 2" x-cloak x-ref="step2" data-step="2" class="space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-base pb-2 flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-5 h-5 text-blue-600"></i>
                        2. Lokasi & Waktu Terakhir Dilihat
                    </h3>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                            Lokasi Kehilangan / Gedung Kampus <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" 
                               name="last_seen_location" 
                               x-model="location"
                               value="{{ old('last_seen_location', 'Perpustakaan Pusat') }}"
                               required 
                               placeholder="Contoh: Perpustakaan Pusat Lantai 2, Gedung A Rektorat, Parkiran Gedung C" 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none">
                        @error('last_seen_location') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                                Tanggal Kehilangan <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" 
                                   name="last_seen_date" 
                                   value="{{ old('last_seen_date', date('Y-m-d')) }}"
                                   required 
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none">
                            @error('last_seen_date') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                                Perkiraan Jam / Waktu
                            </label>
                            <input type="time" 
                                   name="last_seen_time" 
                                   value="{{ old('last_seen_time', '10:30') }}"
                                   class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none">
                            @error('last_seen_time') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <!-- Section 3: Foto & URL Bukti -->
                <section x-show="step === 3" x-cloak x-ref="step3" data-step="3" class="space-y-4">
                    <h3 class="font-extrabold text-slate-900 text-base pb-2 flex items-center gap-2">
                        <i data-lucide="camera" class="w-5 h-5 text-blue-600"></i>
                        3. Unggah Foto Bukti & Referensi Visual
                    </h3>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                            URL Foto Referensi / Sampel Barang (Opsional)
                        </label>
                        <input type="url" 
                               name="photo_url"
                               x-model="photoUrl"
                               @input="validatePhotoCount()"
                               placeholder="https://images.unsplash.com/..." 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none">
                        @error('photo_url') <p class="mt-1 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <input x-ref="photoInput"
                               type="file"
                               name="photo_files[]"
                               accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                               multiple
                               class="sr-only"
                               @change="handleFiles($event.target.files)">
                        <button type="button"
                                @click="$refs.photoInput.click()"
                                @dragover.prevent="dragging = true"
                                @dragleave.prevent="dragging = false"
                                @drop.prevent="dragging = false; handleFiles($event.dataTransfer.files)"
                                :class="dragging ? 'border-blue-500 bg-blue-50' : 'border-slate-300 bg-slate-50 hover:bg-blue-50/50'"
                                class="w-full rounded-2xl border-2 border-dashed p-6 text-center transition-colors">
                            <i data-lucide="upload-cloud" class="mx-auto mb-2 h-10 w-10 text-blue-500"></i>
                            <span class="block text-xs font-bold text-slate-700">Tarik & Lepas Foto di sini, atau klik untuk memilih file</span>
                            <span class="mt-1 block text-[11px] text-slate-400">Format JPG, PNG, WEBP; maksimal 5 MB per foto dan total 3 foto</span>
                        </button>
                        @error('photo_files') <p class="mt-2 text-xs font-semibold text-rose-600">{{ $message }}</p> @enderror
                        @if($errors->has('photo_files.0') || $errors->has('photo_files.1') || $errors->has('photo_files.2'))
                            <p class="mt-2 text-xs font-semibold text-rose-600">{{ $errors->first('photo_files.*') }}</p>
                        @endif
                        <p x-cloak x-show="photoError" x-text="photoError" class="mt-2 text-xs font-semibold text-rose-600"></p>
                    </div>

                    <div x-cloak x-show="selectedFiles.length" class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                        <template x-for="(photo, index) in selectedFiles" :key="photo.preview">
                            <div class="relative overflow-hidden rounded-xl border border-slate-200 bg-white">
                                <img :src="photo.preview" :alt="photo.name" class="h-28 w-full object-cover">
                                <div class="flex items-center justify-between gap-2 p-2">
                                    <span x-text="photo.name" class="truncate text-xs text-slate-600"></span>
                                    <button type="button" @click="removePhoto(index)" :aria-label="'Hapus ' + photo.name" class="shrink-0 rounded-lg p-1 text-rose-600 hover:bg-rose-50">
                                        <i data-lucide="x" class="h-4 w-4"></i>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </section>

                <!-- Section 4: Kontak & Privasi -->
                <section x-show="step === 4" x-cloak x-ref="step4" data-step="4" class="space-y-6">
                    <div class="border-b border-slate-100 pb-3">
                        <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2">
                            <i data-lucide="shield-check" class="w-5 h-5 text-blue-600"></i>
                            4. Kontak & Privasi Pelapor
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">
                            Lengkapi identitas kampus dan nomor telepon aktif Anda untuk keperluan verifikasi kepemilikan dan penyerahan barang.
                        </p>
                    </div>

                    <!-- Informasi Kontak Pelapor Form -->
                    <div class="bg-slate-50/80 rounded-2xl border border-slate-200/90 p-5 space-y-4">
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-200 text-xs font-bold uppercase text-slate-600 tracking-wider">
                            <i data-lucide="user-check" class="w-4 h-4 text-blue-600"></i>
                            Informasi Kontak Pelapor
                        </div>

                        <!-- A. Nama Lengkap & C. Email (Read-Only Akun Login) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                                    Nama Lengkap
                                </label>
                                <div class="px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 flex items-center justify-between shadow-2xs">
                                    <span>{{ Auth::user()->name }}</span>
                                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 border border-slate-200">Akun Login</span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">
                                    Nama terhubung dengan akun. Untuk mengubah, buka <a href="{{ route('profile.show') }}" target="_blank" class="text-blue-600 font-semibold hover:underline">pengaturan profil</a>.
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">
                                    Email Kampus
                                </label>
                                <div class="px-4 py-3 bg-white border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 flex items-center justify-between shadow-2xs">
                                    <span class="truncate">{{ Auth::user()->email }}</span>
                                    <i data-lucide="lock" class="w-4 h-4 text-slate-400 shrink-0"></i>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1">Email resmi terverifikasi dari akun Anda.</p>
                            </div>
                        </div>

                        <!-- B. NIM/NIP & D. Nomor Telepon (Editable Inputs) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                            <div>
                                <label for="identity_no" class="block text-xs font-bold text-slate-700 uppercase mb-1">
                                    NIM / NIP (Hanya Angka) <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <i data-lucide="id-card" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                    <input type="text"
                                           inputmode="numeric"
                                           pattern="[0-9]*"
                                           id="identity_no"
                                           name="identity_no"
                                           x-model="identityNo"
                                           @keydown="filterIdentityInput($event)"
                                           @input="sanitizeIdentityInput()"
                                           required
                                           placeholder="Masukkan NIM atau NIP (hanya angka)"
                                           class="w-full pl-10 pr-4 py-3 bg-white border rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                                           :class="identityNoError || {{ $errors->has('identity_no') ? 'true' : 'false' }} ? 'border-rose-400 bg-rose-50/30 text-rose-900 focus:border-rose-500 focus:ring-rose-200' : 'border-slate-200'">
                                </div>
                                <p x-cloak x-show="identityNoError" x-text="identityNoError" class="mt-1.5 text-xs font-semibold text-rose-600 flex items-center gap-1"></p>
                                @error('identity_no')
                                    <p class="mt-1.5 text-xs font-semibold text-rose-600 flex items-center gap-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <p x-show="!identityNoError && !{{ $errors->has('identity_no') ? 'true' : 'false' }}" class="text-[11px] text-slate-400 mt-1">
                                    Hanya angka (tanpa huruf, spasi, atau simbol).
                                </p>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase">
                                        Nomor Telepon / WhatsApp <span class="text-rose-500">*</span>
                                    </label>
                                    <span class="text-[11px] font-mono font-bold"
                                          :class="phone && phone.length > 13 ? 'text-rose-600' : (phone && phone.length === 13 ? 'text-amber-600' : 'text-slate-400')">
                                        <span x-text="phone ? phone.length : 0"></span>/13
                                    </span>
                                </div>
                                <div class="relative">
                                    <i data-lucide="phone" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                                    <input type="tel"
                                           id="phone"
                                           name="phone"
                                           x-model="phone"
                                           maxlength="13"
                                           @input="validatePhone()"
                                           @keydown="filterPhoneInput($event)"
                                           required
                                           placeholder="08xxxxxxxxxx"
                                           class="w-full pl-10 pr-10 py-3 bg-white border rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 outline-none transition-all"
                                           :class="(phone && phone.length > 13) || phoneError || {{ $errors->has('phone') ? 'true' : 'false' }} ? 'border-rose-400 bg-rose-50/30 text-rose-900 focus:border-rose-500 focus:ring-rose-200' : 'border-slate-200'">
                                    <template x-if="phone && phone.length > 13">
                                        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-rose-500 font-bold" title="Melebihi batas maksimal 13 karakter">
                                            <i data-lucide="alert-octagon" class="w-4 h-4"></i>
                                        </div>
                                    </template>
                                </div>
                                <p x-cloak x-show="phoneError" x-text="phoneError" class="mt-1.5 text-xs font-semibold text-rose-600 flex items-center gap-1"></p>
                                @error('phone')
                                    <p class="mt-1.5 text-xs font-semibold text-rose-600 flex items-center gap-1">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <p x-show="!phoneError && !{{ $errors->has('phone') ? 'true' : 'false' }}" class="text-[11px] text-slate-400 mt-1">
                                    Contoh format: 081234567890 (maksimal 13 karakter).
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Privacy Guarantee Box -->
                    <div class="rounded-2xl border border-blue-200 bg-blue-50/70 p-4 text-xs leading-relaxed text-blue-900 flex items-start gap-3">
                        <i data-lucide="shield" class="w-5 h-5 text-blue-600 shrink-0 mt-0.5"></i>
                        <div>
                            <span class="font-extrabold block text-blue-950 mb-0.5">Privasi Terjamin</span>
                            Informasi kontak digunakan oleh petugas kampus untuk keperluan verifikasi dan koordinasi pengembalian barang. Informasi ini tidak ditampilkan kepada publik.
                        </div>
                    </div>
                </section>

                <!-- Step Navigation and Submit -->
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-6">
                    <a href="{{ route('dashboard.redirect') }}" class="px-5 py-3 rounded-xl border border-slate-300 text-slate-600 font-bold text-sm hover:bg-slate-50 transition-all">
                        Batal
                    </a>
                    <div class="ml-auto flex items-center gap-3">
                        <button type="button"
                                x-show="step > 1"
                                @click="step--"
                                class="rounded-xl border border-slate-300 px-5 py-3 text-sm font-bold text-slate-600 transition-all hover:bg-slate-50">
                            Kembali
                        </button>
                        <button type="button"
                                x-show="step < 4"
                                @click="nextStep()"
                                class="flex items-center gap-2 rounded-xl bg-blue-600 px-7 py-3 text-sm font-extrabold text-white shadow-lg shadow-blue-600/30 transition-all hover:bg-blue-700">
                            Lanjut
                            <i data-lucide="arrow-right" class="h-4 w-4"></i>
                        </button>
                        <button type="submit"
                                x-show="step === 4"
                                :disabled="(phone && (phone.length > 13 || Boolean(phoneError))) || Boolean(identityNoError)"
                                :class="(phone && (phone.length > 13 || Boolean(phoneError))) || Boolean(identityNoError) ? 'opacity-50 cursor-not-allowed bg-slate-400' : 'bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-600/30'"
                                class="flex items-center gap-2 rounded-xl px-7 py-3 text-sm font-extrabold text-white transition-all">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        Kirim Laporan Kehilangan
                        </button>
                    </div>
                </div>

            </form>
        </div>

        <!-- Right Sidebar Floating Cards (Matching PDF Page 2) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- Live Preview Card -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-3">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400 block">RINGKASAN LAPORAN PREVIEW</span>
                <div class="rounded-2xl bg-slate-100 p-4 border border-slate-200">
                    <img :src="selectedFiles.length ? selectedFiles[0].preview : (photoUrl || defaultPhoto)"
                         alt="Preview" class="w-full h-36 object-cover rounded-xl mb-3">
                    <h4 class="font-extrabold text-slate-900 text-sm" x-text="title"></h4>
                    <p class="text-xs text-slate-500 mt-1"><i data-lucide="map-pin" class="w-3.5 h-3.5 inline"></i> <span x-text="location"></span></p>
                    <p class="text-xs text-slate-500"><i data-lucide="user" class="w-3.5 h-3.5 inline"></i> {{ Auth::user()->name }} (<span x-text="identityNo || '{{ Auth::user()->identity_no ?? '-' }}'"></span>)</p>
                </div>
            </div>

            <!-- Auto-Matcher AI Box (Matching PDF Page 2) -->
            <div class="bg-gradient-to-br from-indigo-900 to-blue-900 p-6 rounded-3xl text-white shadow-md space-y-3">
                <div class="flex items-center gap-2 text-amber-400 font-extrabold text-xs uppercase tracking-wider">
                    <i data-lucide="sparkles" class="w-4 h-4"></i>
                    Auto-Matcher AI KEMBALI
                </div>
                <p class="text-xs text-blue-100 leading-relaxed">
                    Sistem akan menganalisis data menggunakan Vector Embedding & NLP untuk mencocokkan barang yang dilaporkan dengan barang temuan di seluruh kampus.
                </p>
                <div class="p-3 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-between text-xs">
                    <span class="font-bold">Akurasi Kemiripan:</span>
                    <span class="font-extrabold text-emerald-400">Tinggi (88%)</span>
                </div>
            </div>

            <!-- Emergency Satpam Box (Matching PDF Page 2) -->
            <div class="bg-rose-50 border border-rose-200 p-6 rounded-3xl space-y-2">
                <div class="flex items-center gap-2 text-rose-700 font-bold text-sm">
                    <i data-lucide="phone-call" class="w-5 h-5"></i>
                    Barang Kritis & Darurat?
                </div>
                <p class="text-xs text-rose-600 leading-relaxed">
                    Bila barang hilang mencakup kunci motor/mobil utama atau dokumen berharga, segera hubungi Satpam Kampus.
                </p>
                <p class="text-xs font-mono font-bold text-rose-800 pt-1">
                    Hotline Satpam: (0283) 351-010 / Pos Rektorat
                </p>
            </div>

        </div>

    </div>

</div>

@endsection
