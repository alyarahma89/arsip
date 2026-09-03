<?php

namespace App\Exports;

use App\Models\Assignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class AssignmentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Assignment::query();

        // Biar data yang di-export sesuai dengan filter pencarian di halaman Admin
        if ($this->request->filter == 'completed') $query->where('status_laporan', true);
        if ($this->request->filter == 'pending') $query->where('status_laporan', false);
        if ($this->request->filter == 'has_proof') $query->where('link_bukti', '!=', '-')->where('link_bukti', '!=', '');

        if ($this->request->search) {
            $query->where(function($q) {
                $q->where('nama_pegawai', 'like', "%{$this->request->search}%")
                  ->orWhere('kegiatan', 'like', "%{$this->request->search}%");
            });
        }
        if ($this->request->bulan) $query->whereMonth('tanggal', $this->request->bulan);
        if ($this->request->tahun) $query->whereYear('tanggal', $this->request->tahun);

        return $query->get();
    }

    public function headings(): array
    {
        return ['No', 'Nama Pegawai', 'Tanggal', 'Kegiatan', 'Status Laporan', 'Link Bukti'];
    }

    public function map($assignment): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $assignment->nama_pegawai,
            $assignment->tanggal,
            $assignment->kegiatan,
            $assignment->status_laporan ? 'Selesai' : 'Pending',
            $assignment->link_bukti
        ];
    }
}
