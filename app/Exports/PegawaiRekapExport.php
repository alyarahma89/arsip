<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PegawaiRekapExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $events;
    protected $assignments;
    protected $user;

    // Ini buat nangkep data yang dikirim dari Controller
    public function __construct($events, $assignments, $user)
    {
        $this->events = $events;
        $this->assignments = $assignments;
        $this->user = $user;
    }

    // Ini buat nentuin tampilan Excel-nya ambil dari View mana
    public function view(): View
    {
        return view('dashboard.export_rekap', [
            'events' => $this->events,
            'assignments' => $this->assignments,
            'user' => $this->user
        ]);
    }

    // Ini buat styling Excel (Bold Header)
    public function styles(Worksheet $sheet)
    {
        return [
            // Bold Baris 1 & 2 (Judul)
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],

            // Bold Header Tabel (Baris ke-5) + Kasih Warna Biru Muda
            'A5:G5' => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E0E7FF']]
            ],
        ];
    }
}
