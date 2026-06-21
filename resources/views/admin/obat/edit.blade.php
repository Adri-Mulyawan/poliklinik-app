<x-layouts.app title="Edit Obat">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('obat.index') }}" class="flex items-center justify-center w-9 h-9 rounded-lg 
                  bg-slate-100 hover:bg-slate-200 
                  text-slate-600 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>

        <h2 class="text-2xl font-bold text-slate-800">
            Edit Obat
        </h2>
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-8">

            <form action="{{ route('obat.update', $obat->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Grid Baris 1 --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                    {{-- Nama Obat --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            Nama Obat <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="nama_obat" value="{{ old('nama_obat', $obat->nama_obat) }}"
                            placeholder="Masukkan nama obat..." class="w-full px-4 py-2 border-2 rounded-lg p-2
                                      focus:border-primary focus:outline-none
                                      @error('nama_obat') border-red-500 @enderror" required>
                        @error('nama_obat')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kemasan --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            Kemasan
                        </label>
                        <input type="text" name="kemasan" value="{{ old('kemasan', $obat->kemasan) }}"
                            placeholder="Contoh: Strip, Botol, Tube..." class="w-full px-4 py-2 border-2 rounded-lg p-2
                                      focus:border-primary focus:outline-none
                                      @error('kemasan') border-red-500 @enderror">
                        @error('kemasan')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Grid Baris 2: Harga & Stok --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                    {{-- Harga --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            Harga <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center border-2 rounded-lg px-4 py-2
                                    focus-within:border-primary @error('harga') border-red-500 @enderror">
                            <span class="text-slate-500 text-sm font-semibold mr-2">Rp</span>
                            <input type="number" name="harga" value="{{ old('harga', $obat->harga) }}"
                                placeholder="0" min="0" step="1"
                                class="w-full focus:outline-none" required>
                        </div>
                        @error('harga')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Stok --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">
                            Stok <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center border-2 rounded-lg px-4 py-2
                                    focus-within:border-primary @error('stok') border-red-500 @enderror">
                            <span class="text-slate-500 text-sm font-semibold mr-2">
                                <i class="fas fa-boxes-stacked"></i>
                            </span>
                            <input type="number" name="stok" value="{{ old('stok', $obat->stok) }}"
                                placeholder="0" min="0" step="1"
                                class="w-full focus:outline-none" required>
                        </div>
                        @error('stok')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror

                        {{-- Indikator Status Stok Saat Ini --}}
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-xs text-slate-500">Stok saat ini:</span>
                            @if ($obat->stok == 0)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-red-100 text-red-600">
                                    <i class="fas fa-circle-xmark"></i> Habis
                                </span>
                            @elseif ($obat->stok <= 5)
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-amber-100 text-amber-600">
                                    <i class="fas fa-triangle-exclamation"></i> Menipis ({{ $obat->stok }})
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-600">
                                    <i class="fas fa-circle-check"></i> Aman ({{ $obat->stok }})
                                </span>
                            @endif
                        </div>
                    </div>

                </div>

                {{-- Buttons --}}
                <div class="flex gap-3">
                    <button type="submit"
                        class="px-6 py-2.5 rounded-lg bg-primary hover:bg-primary/90 
                               text-white font-semibold text-sm transition">
                        <i class="fas fa-save mr-1"></i> Update
                    </button>

                    <a href="{{ route('obat.index') }}"
                        class="px-6 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 
                               text-slate-600 font-semibold text-sm transition">
                        Batal
                    </a>
                </div>

            </form>

        </div>
    </div>

</x-layouts.app>