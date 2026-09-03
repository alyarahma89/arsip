<?php

namespace App\Exports;

use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class MyAssignmentsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        // KUNCI KEAMANAN: Hanya ambil data yang namanya sama dengan user yang login!
        $query = Assignment::where('nama_pegawai', Auth::user()->name);

        // Fitur pencarian
        if ($this->request->search) {
            $query->where('kegiatan', 'like', "%{$this->request->search}%");
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['No', 'Kegiatan', 'Tanggal', 'Status', 'Link Bukti'];
    }

    public function map($assign): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $assign->kegiatan,
            $assign->tanggal,
            $assign->status_laporan ? 'Selesai' : 'Pending',
            $assign->link_bukti ?? '-'
        ];
    }
}
