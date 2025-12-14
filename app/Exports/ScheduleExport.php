<?php

namespace App\Exports;

use App\Models\Tenant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScheduleExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting, WithStyles
{
    protected $branchId;

    public function __construct($branchId = null)
    {
        $this->branchId = $branchId;
    }

    /**
     * 현재 지점의 입주자 데이터 조회
     */
    public function collection()
    {
        $query = Tenant::with(['branch', 'room'])
            ->where('user_id', auth()->id());

        if ($this->branchId) {
            $query->where('branch_id', $this->branchId);
        }

        return $query->orderBy('room_number')->orderBy('move_in_date')->get();
    }

    /**
     * 엑셀 헤더
     */
    public function headings(): array
    {
        return [
            '이름',
            '전화번호',
            '호실',
            '유형',
            '입주일',
            '퇴실일',
            '퇴실일미정',
            '단기숙박',
            '단기숙박월세',
            '단기숙박보증금',
        ];
    }

    /**
     * 데이터 매핑
     */
    public function map($tenant): array
    {
        return [
            $tenant->name,
            $tenant->phone,
            $tenant->room_number,
            $tenant->room_type,
            $tenant->move_in_date?->format('Y-m-d'),
            $tenant->move_out_date?->format('Y-m-d'),
            $tenant->indefinite_move_out ? 'Y' : 'N',
            $tenant->is_short_term ? 'Y' : 'N',
            $tenant->short_term_monthly_rent ?? '',
            $tenant->short_term_deposit ?? '',
        ];
    }

    /**
     * 컬럼 포맷 지정
     */
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT, // 전화번호를 텍스트로 처리
            'E' => NumberFormat::FORMAT_DATE_YYYYMMDD2, // 입주일
            'F' => NumberFormat::FORMAT_DATE_YYYYMMDD2, // 퇴실일
            'I' => NumberFormat::FORMAT_NUMBER, // 단기숙박월세
            'J' => NumberFormat::FORMAT_NUMBER, // 단기숙박보증금
        ];
    }

    /**
     * 스타일 지정
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // 헤더 스타일
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F3F4F6']
                ],
            ],
        ];
    }
}
