<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


class TransferStokExport implements FromView, WithEvents
{
    protected $tbl;
    protected $totalrow;

    public function __construct($tbl, $totalrow)
    {
        $this->tbl = $tbl;
        $this->totalrow = $totalrow;
    }

    public function view(): View
    {
        return view('dashboard_kandang.history.transfer_stok_export', [
            'penjualan' => $this->tbl,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function (AfterSheet $event) {
                $sheet = $event->sheet; 
                $cellRange = 'A1:F1';
                $cellRangeLoop = 'A1:F' . $this->totalrow;
                // $sheet->setAutoFilter($cellRange);
                
                $sheet->getStyle($cellRangeLoop)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'font' => [
                        'name'  =>  'Calibri',
                        'size'  =>  11,
                        'bold' => false
                    ]
                ]);
                $sheet->getStyle($cellRange)->getFont()->setBold(true);
            },
        ];
    }
}
