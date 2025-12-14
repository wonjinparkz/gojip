<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScheduleTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
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
                '201',
                '스탠다드룸',
                '2025-01-01',
                '2025-12-31',
                'N',
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
     * 스타일 지정
     */
    public function styles(Worksheet $sheet)
    {
        // 예시 행에 설명 추가
        $sheet->setCellValue('A3', '※ 입력 가이드:');
        $sheet->setCellValue('A4', '- 필수 항목: 이름, 전화번호, 호실, 입주일');
        $sheet->setCellValue('A5', '- 호실: 숫자 또는 호실번호 (예: 201, 202)');
        $sheet->setCellValue('A6', '- 유형: 스탠다드룸, 디럭스룸 등');
        $sheet->setCellValue('A7', '- 날짜: YYYY-MM-DD 형식 (예: 2025-01-01)');
        $sheet->setCellValue('A8', '- 퇴실일미정/단기숙박: Y 또는 N');
        $sheet->setCellValue('A9', '- 퇴실일미정이 Y인 경우 퇴실일은 비워두세요');
        $sheet->setCellValue('A10', '- 단기숙박월세/보증금: 숫자만 입력 (예: 700000)');
        $sheet->setCellValue('A11', '- 중복 방지: 동일한 전화번호는 업로드되지 않습니다');

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
            '3:11' => [
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
            'C' => 10,  // 호실
            'D' => 15,  // 유형
            'E' => 12,  // 입주일
            'F' => 12,  // 퇴실일
            'G' => 12,  // 퇴실일미정
            'H' => 12,  // 단기숙박
            'I' => 15,  // 단기숙박월세
            'J' => 15,  // 단기숙박보증금
        ];
    }
}
