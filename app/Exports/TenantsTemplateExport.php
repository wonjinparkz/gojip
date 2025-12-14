<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TenantsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    /**
     * 빈 데이터 (예시 행만 추가)
     */
    public function array(): array
    {
        return [
            [
                '홍길동',
                '010-1234-5678',
                '남',
                'N',
                '',
                'N',
                '',
                '',
            ],
        ];
    }

    /**
     * 엑셀 헤더
     */
    public function headings(): array
    {
        return [
            '이름',
            '전화번호',
            '성별',
            '블랙리스트',
            '블랙리스트메모',
            '단기숙박',
            '단기숙박월세',
            '단기숙박보증금',
        ];
    }

    /**
     * 스타일 지정
     */
    public function styles(Worksheet $sheet)
    {
        // 예시 행에 설명 추가
        $sheet->setCellValue('A3', '※ 입력 가이드:');
        $sheet->setCellValue('A4', '- 필수 항목: 이름, 전화번호');
        $sheet->setCellValue('A5', '- 성별: 남, 여');
        $sheet->setCellValue('A6', '- 블랙리스트/단기숙박: Y 또는 N');
        $sheet->setCellValue('A7', '- 단기숙박월세/보증금: 숫자만 입력 (예: 700000)');
        $sheet->setCellValue('A8', '- 중복 방지: 동일한 전화번호는 업로드되지 않습니다');

        return [
            // 헤더 스타일
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2DD4BF']
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
            // 예시 행 스타일
            2 => [
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F3F4F6']
                ],
            ],
            // 설명 스타일
            '3:8' => [
                'font' => ['size' => 9, 'color' => ['rgb' => '6B7280']],
            ],
        ];
    }

    /**
     * 컬럼 너비 지정
     */
    public function columnWidths(): array
    {
        return [
            'A' => 12,  // 이름
            'B' => 15,  // 전화번호
            'C' => 8,   // 성별
            'D' => 12,  // 블랙리스트
            'E' => 20,  // 블랙리스트메모
            'F' => 12,  // 단기숙박
            'G' => 15,  // 단기숙박월세
            'H' => 15,  // 단기숙박보증금
        ];
    }
}
