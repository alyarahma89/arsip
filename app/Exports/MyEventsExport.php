<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class MyEventsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = Event::query();

        // Kalau Min lagi nge-search di web, hasil exportnya juga ikutan tersaring
        if ($this->request->search) {
            $query->where('nama_kegiatan', 'like', "%{$this->request->search}%");
        }

        return $query->get();
    }

    public function headings(): array
    {
        return ['No', 'Akademi', 'Nama Kegiatan', 'Tanggal Mulai', 'Lokasi Folder'];
    }

    public function map($event): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $event->akademi,
            $event->nama_kegiatan,
            $event->tanggal_mulai,
            $event->lokasi ?? '-'
        ];
    }
}
