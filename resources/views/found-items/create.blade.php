@extends('layouts.app')

@section('content')

<div class="space-y-6" x-data="{
    step: {{ $errors->has('photo_url') || $errors->has('photo_files') || $errors->has('photo_files.0') || $errors->has('photo_files.1') || $errors->has('photo_files.2') ? 4 : ($errors->has('holding_option') || $errors->has('holding_location_name') ? 3 : ($errors->has('found_location') || $errors->has('found_date') || $errors->has('found_time') ? 2 : 1)) }},
    photoUrl: @js(old('photo_url', '')),
    selectedFiles: [],
    photoError: '',
    dragging: false,
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
    goToStep(stepNumber) {
        this.step = stepNumber;
    },
    nextStep() {
        const currentSection = this.$refs['step' + this.step];
        const invalidField = currentSection.querySelector(':invalid');

        if (invalidField) {
            invalidField.reportValidity();
            return;
        }

        if (this.step === 4 && this.photoError) {
            return;
        }

        this.step = Math.min(this.step + 1, 4);
    },
    submitReport(event) {
        if (this.photoError) {
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

    <!-- Page Header -->
    <div class="flex items-center gap-3 pb-2">
        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
            <i data-lucide="plus-circle" class="w-5 h-5"></i>
        </div>
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Laporkan Barang Ditemukan</h1>
            <p class="text-xs text-slate-500 mt-0.5">Lengkapi formulir berikut untuk melaporkan barang yang Anda temukan.</p>
        </div>
    </div>

    <nav aria-label="Tahapan laporan barang ditemukan" class="overflow-x-auto rounded-2xl border border-slate-200/80 bg-white p-4 text-xs font-bold shadow-sm">
        <ol class="flex min-w-[600px] items-center justify-between">
            @foreach(['Informasi Barang', 'Lokasi & Waktu', 'Penyerahan', 'Foto Barang'] as $index => $label)
                @php($stepNumber = $index + 1)
                <li class="flex flex-1 items-center last:flex-none">
                    <button type="button"
                            @click="goToStep({{ $stepNumber }})"
                            :aria-current="step === {{ $stepNumber }} ? 'step' : null"
                            class="flex items-center gap-2 whitespace-nowrap transition-colors"
                            :class="step === {{ $stepNumber }} ? 'text-emerald-600' : (step > {{ $stepNumber }} ? 'text-emerald-600' : 'text-slate-400')">
                        <span class="flex h-7 w-7 items-center justify-center rounded-full text-xs font-extrabold"
                              :class="step === {{ $stepNumber }} ? 'bg-emerald-600 text-white' : (step > {{ $stepNumber }} ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500')">
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

    <!-- Form Card -->
    <form method="POST" enctype="multipart/form-data" action="{{ route('found-items.store') }}" novalidate @submit.prevent="submitReport($event)" class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        @csrf

        <!-- Section 1: Informasi Barang -->
        <section x-show="step === 1" x-cloak x-ref="step1" data-step="1" class="space-y-5">
            <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2 pb-2 border-b border-slate-100">
                <i data-lucide="package" class="w-5 h-5 text-emerald-500"></i>
                Informasi Barang
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kategori Barang <span class="text-rose-500">*</span>
                    </label>
                    <select name="category_id" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 focus:outline-none transition-all">
                        <option value="">— Pilih Kategori —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Judul -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama / Judul Barang <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="cth: Earphone Wireless TWS Putih"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 focus:outline-none transition-all">
                    @error('title') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Brand -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Merek / Brand <span class="text-slate-400">(opsional)</span>
                    </label>
                    <input type="text" name="brand" value="{{ old('brand') }}" placeholder="cth: Anker Soundcore"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 focus:outline-none transition-all">
                </div>

                <!-- Warna -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Warna Utama <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="primary_color" value="{{ old('primary_color') }}" required placeholder="cth: Putih / White"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 focus:outline-none transition-all">
                    @error('primary_color') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Deskripsi & Kondisi Fisik <span class="text-rose-500">*</span>
                </label>
                <textarea name="description" rows="4" required placeholder="Jelaskan kondisi fisik barang, ciri khusus, kerusakan, dll..."
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 focus:outline-none transition-all resize-none">{{ old('description') }}</textarea>
                @error('description') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
            </div>
        </section>

        <!-- Section 2: Lokasi & Waktu -->
        <section x-show="step === 2" x-cloak x-ref="step2" data-step="2" class="space-y-5">
            <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2 pb-2 border-b border-slate-100">
                <i data-lucide="map-pin" class="w-5 h-5 text-emerald-500"></i>
                Lokasi & Waktu Penemuan
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Lokasi Ditemukan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="found_location" value="{{ old('found_location') }}" required placeholder="cth: Perpustakaan Pusat Lantai 2"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 focus:outline-none transition-all">
                    @error('found_location') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Tanggal Ditemukan <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="found_date" value="{{ old('found_date', date('Y-m-d')) }}" required
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 focus:outline-none transition-all">
                    @error('found_date') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Waktu (opsional)
                    </label>
                    <input type="time" name="found_time" value="{{ old('found_time') }}"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 focus:outline-none transition-all">
                </div>
            </div>
        </section>

        <!-- Section 3: Penyerahan -->
        <section x-show="step === 3" x-cloak x-ref="step3" data-step="3" class="space-y-5">
            <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2 pb-2 border-b border-slate-100">
                <i data-lucide="hand" class="w-5 h-5 text-emerald-500"></i>
                Opsi Penyerahan Barang
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Opsi Penyerahan <span class="text-rose-500">*</span>
                    </label>
                    <select name="holding_option" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 focus:outline-none transition-all">
                        <option value="">— Pilih Opsi —</option>
                        <option value="satpam" {{ old('holding_option') == 'satpam' ? 'selected' : '' }}>Serahkan ke Pos Satpam</option>
                        <option value="saya_simpan" {{ old('holding_option') == 'saya_simpan' ? 'selected' : '' }}>Saya Simpan Sendiri</option>
                        <option value="ruang_dosen" {{ old('holding_option') == 'ruang_dosen' ? 'selected' : '' }}>Titipkan di Ruang Dosen</option>
                    </select>
                    @error('holding_option') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Lokasi Penyimpanan <span class="text-slate-400">(opsional)</span>
                    </label>
                    <input type="text" name="holding_location_name" value="{{ old('holding_location_name') }}" placeholder="cth: Pos Satpam Perpustakaan"
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 focus:outline-none transition-all">
                </div>
            </div>
        </section>

        <!-- Section 4: Foto -->
        <section x-show="step === 4" x-cloak x-ref="step4" data-step="4" class="space-y-5">
            <h3 class="font-extrabold text-slate-900 text-base flex items-center gap-2 pb-2 border-b border-slate-100">
                <i data-lucide="image" class="w-5 h-5 text-emerald-500"></i>
                Foto Barang
            </h3>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    URL Foto <span class="text-slate-400">(opsional)</span>
                </label>
                <input type="url" name="photo_url" value="{{ old('photo_url') }}" placeholder="https://example.com/photo.jpg"
                       x-model="photoUrl" @input="validatePhotoCount()"
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 focus:outline-none transition-all">
                @error('photo_url') <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p> @enderror
                <p class="text-[11px] text-slate-400 mt-1">Anda juga dapat memilih foto dari perangkat. Maksimal 3 foto, termasuk foto dari URL.</p>
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
                        :class="dragging ? 'border-emerald-500 bg-emerald-50' : 'border-slate-300 bg-slate-50 hover:bg-emerald-50/50'"
                        class="w-full rounded-2xl border-2 border-dashed p-6 text-center transition-colors">
                    <i data-lucide="upload-cloud" class="mx-auto mb-2 h-10 w-10 text-emerald-500"></i>
                    <span class="block text-xs font-bold text-slate-700">Tarik & Lepas foto di sini, atau klik untuk memilih file</span>
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
                        <img :src="photo.preview" :alt="photo.name" class="h-32 w-full object-cover">
                        <button type="button"
                                @click="removePhoto(index)"
                                :aria-label="'Hapus foto ' + photo.name"
                                class="absolute right-2 top-2 rounded-full bg-slate-900/75 p-1.5 text-white hover:bg-rose-600">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                        <p class="truncate px-2 py-1.5 text-[11px] text-slate-600" x-text="photo.name"></p>
                    </div>
                </template>
            </div>
        </section>

        <!-- Step Navigation and Submit -->
        <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-6">
            <a href="{{ route('found-items.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm transition-all">
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
                        class="flex items-center gap-2 rounded-xl bg-emerald-600 px-7 py-3 text-sm font-extrabold text-white shadow-lg shadow-emerald-600/20 transition-all hover:bg-emerald-700">
                    Lanjut
                    <i data-lucide="arrow-right" class="h-4 w-4"></i>
                </button>
                <button type="submit"
                        x-show="step === 4"
                        class="flex items-center gap-2 rounded-xl bg-emerald-600 px-7 py-3 text-sm font-extrabold text-white shadow-lg shadow-emerald-600/20 transition-all hover:bg-emerald-700">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Publikasikan Laporan
                </button>
            </div>
        </div>
    </form>

</div>

@endsection
