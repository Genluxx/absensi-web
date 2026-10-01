@extends('layouts.app')

@section('title', 'Input Presensi - SAP.HRIS')
@section('page-title', 'Input Presensi Tim')

@section('content')
    <div class="glass-panel rounded-2xl p-4 sm:p-6">
        <div class="flex justify-between items-start mb-6">
            <div>
                <h2 class="font-bold text-lg text-white">Form Input Presensi Tim Operasional</h2>
                <p class="text-sm text-slate-400">Verifikasi hadir, cuti, izin, atau sakit untuk tim pada {{ now()->translatedFormat('d F Y') }}</p>
                <p class="realtime-display mt-3 text-xl sm:text-2xl font-extrabold tracking-wide text-[#a97924]">Jam realtime <span class="mx-1 text-[#b7a28a]">·</span> <span id="realtimeClock">--:--:--</span></p>
            </div>
            @if ($isAdmin)
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                    class="glow-button flex items-center gap-1.5 bg-blue-500/10 text-blue-300 border border-blue-400/30 text-sm font-bold px-4 py-2 rounded-lg">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Karyawan
            </button>
            @endif
        </div>

        @if ($isAdmin)
        <form method="GET" action="{{ route('presensi.input') }}" class="mb-4 rounded-xl border border-slate-700 bg-slate-950/50 p-4">
            <label for="mandor_id" class="block text-xs font-bold uppercase tracking-wide text-slate-400 mb-2">Pilih tim untuk dicatat</label>
            <div class="flex flex-col sm:flex-row gap-3">
                <select id="mandor_id" name="mandor_id" onchange="this.form.submit()" class="flex-1 border border-slate-700 bg-slate-900 text-white rounded-lg px-3 py-2 text-sm">
                    <option value="">Pilih mandor / area...</option>
                    @foreach ($mandorList as $mandor)
                        <option value="{{ $mandor->id }}" {{ (string) $mandorId === (string) $mandor->id ? 'selected' : '' }}>{{ $mandor->name }}{{ $mandor->area ? ' - '.$mandor->area : '' }}</option>
                    @endforeach
                </select>
                <span class="text-xs text-slate-500 self-center">{{ $karyawan->count() }} anggota ditemukan</span>
            </div>
        </form>
        @endif

        <div id="gpsStatus" role="status" aria-live="polite" class="flex flex-wrap items-center gap-2 rounded-lg p-3 mb-4 bg-amber-500/10 border border-amber-500/30 text-[#6b4d19] text-sm font-semibold">
            <svg id="gpsStatusIcon" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            <span id="gpsStatusText" class="flex-1">Mencari lokasi…</span>
            <button type="button" id="btnPerbaruiGps" class="ml-auto rounded-md border border-current/20 px-3 py-1.5 text-xs font-bold transition hover:bg-black/5 focus:outline-none focus:ring-2 focus:ring-cyan-400" aria-label="Perbarui lokasi GPS">
                Perbarui lokasi
            </button>
            <p id="gpsHelp" class="hidden basis-full pl-6 text-xs font-medium leading-relaxed"></p>
        </div>

        <section id="gpsMapCard" class="hidden mb-5 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-label="Peta lokasi perangkat">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-4 py-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Lokasi perangkat</h3>
                    <p id="gpsMapCoordinates" class="mt-0.5 text-xs tabular-nums text-slate-500"></p>
                </div>
                <a id="gpsMapOpen" href="#" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-md border border-slate-200 px-3 py-2 text-xs font-bold text-cyan-800 transition hover:bg-cyan-50 focus:outline-none focus:ring-2 focus:ring-cyan-500">
                    Buka di Google Maps
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5H19.5V10.5M19 5L10.5 13.5M18 13.5V19.5H4.5V6H10.5" />
                    </svg>
                </a>
            </div>
            <div id="gpsMapViewport" class="relative h-56 w-full touch-none overflow-hidden bg-[#e9eee7] sm:h-64" role="img" aria-label="Peta jalan dengan penanda lokasi GPS">
                <div id="gpsMapTiles" class="absolute inset-0" aria-hidden="true"></div>
                <div id="gpsMapMarker" class="pointer-events-none absolute z-10 -translate-x-1/2 -translate-y-full drop-shadow-md" aria-hidden="true">
                    <svg class="h-10 w-8" viewBox="0 0 32 42" fill="none">
                        <path d="M16 1C7.72 1 1 7.7 1 15.96 1 27.06 16 41 16 41s15-13.94 15-25.04C31 7.7 24.28 1 16 1Z" fill="#1976D2" stroke="white" stroke-width="2" />
                        <circle cx="16" cy="16" r="5" fill="white" />
                    </svg>
                </div>
                <div class="absolute right-3 top-3 z-20 grid overflow-hidden rounded-md border border-slate-300 bg-white shadow-md">
                    <button type="button" id="gpsMapZoomIn" class="grid h-9 w-9 place-items-center border-b border-slate-200 text-xl font-semibold leading-none text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" aria-label="Perbesar peta">+</button>
                    <button type="button" id="gpsMapZoomOut" class="grid h-9 w-9 place-items-center text-xl font-semibold leading-none text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500" aria-label="Perkecil peta">−</button>
                </div>
                <span class="absolute bottom-2 left-2 z-20 rounded bg-white/90 px-1.5 py-0.5 text-[10px] text-slate-600 shadow-sm">Lokasi perangkat</span>
            </div>
            <p class="flex flex-wrap items-center justify-between gap-1 px-4 py-2 text-[11px] text-slate-500">
                <span>Peta © <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener noreferrer" class="underline decoration-slate-400 underline-offset-2">OpenStreetMap contributors</a></span>
                <span>Geser peta untuk melihat sekitar</span>
            </p>
        </section>

        <form method="POST" action="{{ route('presensi.simpan') }}" enctype="multipart/form-data" id="formPresensi">
            @csrf
            <div class="overflow-x-auto rounded-xl border border-slate-800">
            <table class="w-full min-w-[920px] text-sm">
                <thead>
                    <tr class="text-left text-xs text-slate-400 border-b border-slate-700">
                        <th class="pb-2">Karyawan Tim</th>
                        <th class="pb-2">Lokasi/Divisi</th>
                        <th class="pb-2">Status Presensi</th>
                        <th class="pb-2">Foto</th>
                        <th class="pb-2">Keterangan / Hasil Panen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($karyawan as $k)
                    <tr class="border-b border-slate-800">
                        <td class="py-3">
                            <p class="font-bold text-white">{{ $k->nama }}</p>
                            <p class="text-xs text-slate-400">{{ $k->nik }} • {{ $k->jabatan }}</p>
                        </td>
                        <td class="py-3 text-slate-300">
                            <span class="bg-slate-800 text-slate-200 text-xs px-2 py-1 rounded">{{ $k->tipe === 'kebun' ? 'Kebun' : 'Pabrik' }}</span>
                            <span class="ml-2">{{ $k->lokasi }}</span>
                        </td>
                        <td class="py-3">
                            <select name="presensi[{{ $k->id }}][status]" class="border border-slate-700 bg-slate-950 text-white rounded-lg px-2 py-1 text-sm">
                                @php($statusTersimpan = $presensiHariIni->get($k->id)?->status)
                                <option value="">- Pilih -</option>
                                <option value="Hadir" {{ $statusTersimpan === 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                <option value="Telat" {{ $statusTersimpan === 'Telat' ? 'selected' : '' }}>Telat</option>
                                <option value="Cuti" {{ $statusTersimpan === 'Cuti' ? 'selected' : '' }}>Cuti</option>
                                <option value="Izin" {{ $statusTersimpan === 'Izin' ? 'selected' : '' }}>Izin</option>
                                <option value="Sakit" {{ $statusTersimpan === 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="Alpa" {{ $statusTersimpan === 'Alpa' ? 'selected' : '' }}>Alpa</option>
                            </select>
                        </td>
                        <td class="py-3">
                            <input type="file" name="presensi[{{ $k->id }}][foto]" accept="image/*" capture="environment"
                                   class="text-xs w-32 text-slate-200 file:mr-3 file:rounded file:border-0 file:bg-blue-600 file:text-white file:px-2 file:py-1">
                        </td>
                        <td class="py-3">
                            <input type="text" name="presensi[{{ $k->id }}][keterangan]"
                                   class="border border-slate-700 bg-slate-950 text-white rounded-lg px-2 py-1 text-sm w-full placeholder:text-slate-500"
                                   value="{{ $presensiHariIni->get($k->id)?->keterangan }}"
                                   placeholder="Alasan izin atau catatan...">
                            <input type="hidden" name="presensi[{{ $k->id }}][latitude]" class="js-lat">
                            <input type="hidden" name="presensi[{{ $k->id }}][longitude]" class="js-lng">
                            <input type="hidden" name="presensi[{{ $k->id }}][lokasi_gps]" class="js-location">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" id="btnSimpan" class="glow-button flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white font-bold px-6 py-2 rounded-xl disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Simpan Presensi Tim
                </button>
            </div>
        </form>
    </div>

    @if ($isAdmin)
    <!-- Modal Tambah Karyawan -->
    <div id="modalTambah" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center p-6">
        <div class="bg-slate-900 border border-slate-700 rounded-2xl p-6 max-w-md w-full">
            <h3 class="font-bold text-lg mb-4 text-white">Tambah Karyawan Baru</h3>
            <form method="POST" action="{{ route('karyawan.store') }}">
                @csrf
                <label class="block text-xs font-bold mb-1 text-slate-300">NIK</label>
                <input type="text" name="nik" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-3" required>
                <label class="block text-xs font-bold mb-1 text-slate-300">Nama Lengkap</label>
                <input type="text" name="nama" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-3" required>
                <label class="block text-xs font-bold mb-1 text-slate-300">Jabatan (opsional)</label>
                <input type="text" name="jabatan" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-4">
                @if ($isAdmin)
                    <label class="block text-xs font-bold mb-1 text-slate-300">Tim mandor</label>
                    <select name="mandor_id" class="w-full border border-slate-700 bg-slate-950 text-white rounded-lg px-3 py-2 mb-4" required>
                        <option value="">Pilih tim...</option>
                        @foreach ($mandorList as $mandor)
                            <option value="{{ $mandor->id }}" {{ (string) $mandorId === (string) $mandor->id ? 'selected' : '' }}>{{ $mandor->name }}{{ $mandor->area ? ' - '.$mandor->area : '' }}</option>
                        @endforeach
                    </select>
                @endif
                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')"
                            class="flex-1 border border-slate-600 rounded-xl py-2 font-bold text-slate-200">Batal</button>
                    <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-500 text-white rounded-xl py-2 font-bold">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <script>
        const btnSimpan = document.getElementById('btnSimpan');
        const realtimeClock = document.getElementById('realtimeClock');
        const gpsStatus = document.getElementById('gpsStatus');
        const gpsStatusText = document.getElementById('gpsStatusText');
        const gpsStatusIcon = document.getElementById('gpsStatusIcon');
        const btnPerbaruiGps = document.getElementById('btnPerbaruiGps');
        const gpsHelp = document.getElementById('gpsHelp');
        const gpsMapCard = document.getElementById('gpsMapCard');
        const gpsMapViewport = document.getElementById('gpsMapViewport');
        const gpsMapTiles = document.getElementById('gpsMapTiles');
        const gpsMapZoomIn = document.getElementById('gpsMapZoomIn');
        const gpsMapZoomOut = document.getElementById('gpsMapZoomOut');
        const gpsMapCoordinates = document.getElementById('gpsMapCoordinates');
        const gpsMapOpen = document.getElementById('gpsMapOpen');
        let gpsMapPosition = null;
        let gpsMapZoom = 16;
        let gpsMapResizeObserver;
        let gpsMapOffsetX = 0;
        let gpsMapOffsetY = 0;
        let gpsMapDrag = null;
        const gpsMapMarker = document.getElementById('gpsMapMarker');

        const updateClock = () => {
            realtimeClock.textContent = new Intl.DateTimeFormat('id-ID', {
                hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false,
            }).format(new Date());
        };
        updateClock();
        setInterval(updateClock, 1000);

        const setGpsState = (message, state, help = '') => {
            const colors = state === 'ready'
                ? 'bg-emerald-500/10 border-emerald-500/30 text-emerald-700'
                : state === 'loading'
                    ? 'bg-cyan-500/10 border-cyan-500/30 text-cyan-800'
                    : 'bg-amber-500/10 border-amber-500/30 text-amber-800';
            gpsStatus.className = `flex items-center gap-2 text-sm rounded-lg p-3 mb-4 border ${colors}`;
            gpsStatusText.textContent = message;
            gpsStatusIcon.classList.toggle('hidden', state !== 'loading');
            gpsStatusIcon.classList.toggle('animate-spin', state === 'loading');
            btnPerbaruiGps.disabled = state === 'loading';
            btnPerbaruiGps.classList.toggle('opacity-50', state === 'loading');
            gpsHelp.textContent = help;
            gpsHelp.classList.toggle('hidden', !help);
        };

        const applyGpsPosition = (position) => {
                const { latitude, longitude } = position.coords;
                document.querySelectorAll('.js-lat').forEach((input) => input.value = latitude);
                document.querySelectorAll('.js-lng').forEach((input) => input.value = longitude);
                document.querySelectorAll('.js-location').forEach((input) => input.value = `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`);
                const accuracy = Math.round(position.coords.accuracy || 0);
            const mapUrl = `https://www.google.com/maps/search/?api=1&query=${latitude},${longitude}`;
            gpsMapPosition = { latitude, longitude };
            gpsMapCoordinates.textContent = `${latitude.toFixed(6)}, ${longitude.toFixed(6)} · akurasi ±${accuracy} m`;
            gpsMapOpen.href = mapUrl;
            gpsMapCard.classList.remove('hidden');
            renderGpsMap();
                setGpsState(`Lokasi aktif (akurasi ±${accuracy} m): ${latitude.toFixed(5)}, ${longitude.toFixed(5)}`, 'ready');
        };

        const renderGpsMap = () => {
            if (!gpsMapPosition || !gpsMapViewport.clientWidth) return;
            const tileSize = 256;
            const tileCount = 2 ** gpsMapZoom;
            const latitudeRadians = gpsMapPosition.latitude * Math.PI / 180;
            const centerTileX = ((gpsMapPosition.longitude + 180) / 360) * tileCount;
            const centerTileY = ((1 - Math.asinh(Math.tan(latitudeRadians)) / Math.PI) / 2) * tileCount;
            const centerPixelX = centerTileX * tileSize;
            const centerPixelY = centerTileY * tileSize;
            const leftPixel = centerPixelX - gpsMapViewport.clientWidth / 2 - gpsMapOffsetX;
            const topPixel = centerPixelY - gpsMapViewport.clientHeight / 2 - gpsMapOffsetY;
            const firstTileX = Math.floor(leftPixel / tileSize);
            const firstTileY = Math.floor(topPixel / tileSize);
            const lastTileX = Math.floor((leftPixel + gpsMapViewport.clientWidth) / tileSize);
            const lastTileY = Math.floor((topPixel + gpsMapViewport.clientHeight) / tileSize);
            const fragment = document.createDocumentFragment();

            for (let tileY = firstTileY; tileY <= lastTileY; tileY += 1) {
                if (tileY < 0 || tileY >= tileCount) continue;
                for (let tileX = firstTileX; tileX <= lastTileX; tileX += 1) {
                    const wrappedTileX = ((tileX % tileCount) + tileCount) % tileCount;
                    const tile = document.createElement('img');
                    tile.alt = '';
                    tile.draggable = false;
                    tile.decoding = 'async';
                    tile.referrerPolicy = 'strict-origin-when-cross-origin';
                    tile.className = 'absolute max-w-none select-none';
                    tile.width = tileSize;
                    tile.height = tileSize;
                    tile.style.left = `${tileX * tileSize - leftPixel}px`;
                    tile.style.top = `${tileY * tileSize - topPixel}px`;
                    tile.src = `https://tile.openstreetmap.org/${gpsMapZoom}/${wrappedTileX}/${tileY}.png`;
                    fragment.append(tile);
                }
            }

            gpsMapTiles.replaceChildren(fragment);
            gpsMapMarker.style.left = `${gpsMapViewport.clientWidth / 2 + gpsMapOffsetX}px`;
            gpsMapMarker.style.top = `${gpsMapViewport.clientHeight / 2 + gpsMapOffsetY}px`;
        };

        gpsMapViewport.addEventListener('pointerdown', (event) => {
            if (event.target.closest('button')) return;
            gpsMapDrag = { x: event.clientX, y: event.clientY, offsetX: gpsMapOffsetX, offsetY: gpsMapOffsetY };
            gpsMapViewport.setPointerCapture(event.pointerId);
            gpsMapViewport.classList.add('cursor-grabbing');
        });
        gpsMapViewport.addEventListener('pointermove', (event) => {
            if (!gpsMapDrag) return;
            gpsMapOffsetX = gpsMapDrag.offsetX + event.clientX - gpsMapDrag.x;
            gpsMapOffsetY = gpsMapDrag.offsetY + event.clientY - gpsMapDrag.y;
            renderGpsMap();
        });
        const finishGpsMapDrag = () => {
            gpsMapDrag = null;
            gpsMapViewport.classList.remove('cursor-grabbing');
        };
        gpsMapViewport.addEventListener('pointerup', finishGpsMapDrag);
        gpsMapViewport.addEventListener('pointercancel', finishGpsMapDrag);

        gpsMapZoomIn.addEventListener('click', () => {
            gpsMapZoom = Math.min(19, gpsMapZoom + 1);
            renderGpsMap();
        });
        gpsMapZoomOut.addEventListener('click', () => {
            gpsMapZoom = Math.max(13, gpsMapZoom - 1);
            renderGpsMap();
        });
        gpsMapResizeObserver = new ResizeObserver(renderGpsMap);
        gpsMapResizeObserver.observe(gpsMapViewport);

        const requestGpsPosition = (options) => new Promise((resolve, reject) => {
            navigator.geolocation.getCurrentPosition(resolve, reject, options);
        });

        const locate = async (refresh = false) => {
            if (!navigator.geolocation) {
                setGpsState('Browser ini tidak mendukung akses lokasi. Presensi tetap bisa disimpan tanpa lokasi.', 'optional', 'Buka halaman ini di Chrome atau Edge versi terbaru.');
                return;
            }

            if (!window.isSecureContext) {
                setGpsState('Browser memblokir lokasi pada alamat ini. Presensi tetap bisa disimpan tanpa lokasi.', 'optional', 'Gunakan localhost di laptop ini atau akses melalui HTTPS. Alamat IP jaringan lokal dengan HTTP tidak diizinkan browser untuk GPS.');
                return;
            }

            setGpsState('Mencari lokasi…', 'loading');
            try {
                const position = await requestGpsPosition(refresh
                    ? { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
                    : { enableHighAccuracy: false, timeout: 3500, maximumAge: 120000 });
                applyGpsPosition(position);

                if (!refresh && position.coords.accuracy > 100) {
                    requestGpsPosition({ enableHighAccuracy: true, timeout: 9000, maximumAge: 0 })
                        .then(applyGpsPosition)
                        .catch(() => {});
                }
            } catch (error) {
                if (!refresh && error.code !== 1) {
                    setGpsState('Sinyal lokasi lambat. Mencoba GPS dengan akurasi lebih tinggi…', 'loading');
                    try {
                        const position = await requestGpsPosition({ enableHighAccuracy: true, timeout: 9000, maximumAge: 0 });
                        applyGpsPosition(position);
                        return;
                    } catch (retryError) {
                        error = retryError;
                    }
                }

                const message = error.code === 1
                    ? 'Izin lokasi ditolak. Izinkan lokasi di pengaturan browser, lalu coba lagi.'
                    : error.code === 2
                        ? 'Izin browser sudah diberikan, tetapi Windows belum memberikan data lokasi.'
                        : 'Pencarian lokasi terlalu lama. Periksa GPS lalu coba lagi.';
                const help = error.code === 1
                    ? 'Klik ikon pengaturan situs di sebelah alamat web, ubah Lokasi menjadi Izinkan, lalu tekan Perbarui lokasi.'
                    : 'Windows: buka Settings > Privacy & security > Location, aktifkan Location services dan Let desktop apps access your location. Aktifkan Wi-Fi agar laptop dapat menentukan posisi, lalu tekan Perbarui lokasi.';
                setGpsState(`${message} Presensi tetap bisa disimpan tanpa lokasi.`, 'optional', help);
            }
        };

        btnPerbaruiGps.addEventListener('click', () => locate(true));
        locate();

        if (btnSimpan) btnSimpan.disabled = false;
    </script>
@endsection