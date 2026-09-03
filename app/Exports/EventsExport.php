<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class EventsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Event::query();

        // Filter Pencarian
        if ($this->request->search) {
            $query->where(function($q) {
                $q->where('nama_kegiatan', 'like', "%{$this->request->search}%")
                  ->orWhere('penanggung_jawab', 'like', "%{$this->request->search}%")
                  ->orWhere('akademi', 'like', "%{$this->request->search}%");
            });
        }

        // Filter Bulan & Tahun
        if ($this->request->month) $query->whereMonth('tanggal_mulai', $this->request->month);
        if ($this->request->year) $query->whereYear('tanggal_mulai', $this->request->year);

        return $query->get();
    }

    public function headings(): array
    {
        return ['No', 'Akademi', 'Nama Kegiatan', 'Tanggal Mulai', 'Penanggung Jawab', 'Anggota/Panitia', 'Link Arsip', 'Link Lokasi DTS'];
    }

    public function map($event): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $event->akademi ?? '-',
            $event->nama_kegiatan,
            $event->tanggal_mulai,
            $event->penanggung_jawab ?? '-',
            $event->panitia ?? '-',
            $event->link_pencatatan ?? '-',
            $event->lokasi ?? '-'
        ];
    }
}
