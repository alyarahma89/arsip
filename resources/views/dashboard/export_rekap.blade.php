<table>
    <thead>
        {{-- JUDUL LAPORAN --}}
        <tr><td colspan="6"><strong>REKAPITULASI KINERJA PEGAWAI - BBPSDMP</strong></td></tr>
        <tr><td colspan="6">Nama Pegawai: {{ $user->name }}</td></tr>
        <tr><td colspan="6">Tanggal Download: {{ date('d F Y') }}</td></tr>
        <tr></tr> {{-- Baris Kosong --}}

        {{-- TABEL 1: KEGIATAN DTS --}}
        <tr><td colspan="6"><strong>A. DATA KEGIATAN DTS</strong></td></tr>
        <tr>
            <th style="background-color: #0E2C6C; color: white; width: 50px; text-align: center;">No</th>
            <th style="background-color: #0E2C6C; color: white; width: 250px;">Nama Kegiatan</th>
            <th style="background-color: #0E2C6C; color: white; width: 100px;">Akademi</th>
            <th style="background-color: #0E2C6C; color: white; width: 150px;">Tanggal</th>
            <th style="background-color: #0E2C6C; color: white; width: 100px;">Peran</th>
            <th style="background-color: #0E2C6C; color: white; width: 150px;">Status Admin</th>
        </tr>
    </thead>
    <tbody>
        @foreach($events as $index => $event)
        <tr>
            <td style="text-align: center;">{{ $index + 1 }}</td>
            <td>{{ $event->nama_kegiatan }}</td>
            <td>{{ $event->akademi }}</td>
            <td>{{ \Carbon\Carbon::parse($event->tanggal_mulai)->isoFormat('D MMMM Y') }}</td>
            {{-- Cek apakah dia PJ atau Panitia --}}
            <td>{{ str_contains($event->penanggung_jawab, $user->name) ? 'PJ' : 'Panitia' }}</td>
            <td>{{ $event->adm_lapgas == 'SUDAH' ? 'Lengkap' : 'Belum' }}</td>
        </tr>
        @endforeach
    </tbody>

    <tr></tr><tr></tr> {{-- Jarak Antar Tabel --}}

    <thead>
        {{-- TABEL 2: LAPORAN TUGAS --}}
        <tr><td colspan="6"><strong>B. LAPORAN TUGAS (LAPGAS)</strong></td></tr>
        <tr>
            <th style="background-color: #00AEEF; color: white; text-align: center;">No</th>
            <th style="background-color: #00AEEF; color: white;">Tanggal Tugas</th>
            <th style="background-color: #00AEEF; color: white;">Kegiatan</th>
            <th style="background-color: #00AEEF; color: white;">Status Laporan</th>
            <th style="background-color: #00AEEF; color: white;">Link Bukti</th>
        </tr>
    </thead>
    <tbody>
        @foreach($assignments as $index => $assign)
        <tr>
            <td style="text-align: center;">{{ $index + 1 }}</td>
            <td>{{ \Carbon\Carbon::parse($assign->tanggal)->isoFormat('D MMMM Y') }}</td>
            <td>{{ $assign->kegiatan }}</td>
            <td>{{ $assign->status_laporan ? 'SELESAI' : 'PENDING' }}</td>
            <td>{{ $assign->link_bukti }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
