<?php

namespace App\Imports;

use App\Models\Tenant;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Log;

class TenantsImport implements ToModel, WithStartRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    protected $branchId;
    protected $userId;
    protected $skippedRows = []; // 중복으로 건너뛴 행
    protected $expectedHeaders = [
        '이름',
        '전화번호',
        '성별',
        '블랙리스트',
        '블랙리스트메모',
        '단기숙박',
        '단기숙박월세',
        '단기숙박보증금',
    ];

    public function __construct($branchId, $userId)
    {
        $this->branchId = $branchId;
        $this->userId = $userId;
    }

    /**
     * 중복으로 건너뛴 행 가져오기
     */
    public function getSkippedRows(): array
    {
        return $this->skippedRows;
    }

    /**
     * 시작 행 번호 (2행부터 시작, 1행은 헤더)
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * 엑셀 데이터를 모델로 변환
     */
    public function model(array $row)
    {
        // 행 구조: [0: 이름, 1: 전화번호, 2: 성별, 3: 블랙리스트, 4: 블랙리스트메모, 5: 단기숙박, 6: 단기숙박월세, 7: 단기숙박보증금]

        // 빈 행 건너뛰기
        if (empty($row[0])) {
            return null;
        }

        $name = (string)$row[0];
        $phone = !empty($row[1]) ? (string)$row[1] : null;

        // 전화번호가 있는 경우 중복 체크
        if (!empty($phone)) {
            $existingTenant = Tenant::where('user_id', $this->userId)
                ->where('phone', $phone)
                ->first();

            if ($existingTenant) {
                // 중복된 데이터 저장
                $this->skippedRows[] = [
                    'name' => $name,
                    'phone' => $phone,
                    'reason' => '동일한 전화번호로 이미 등록된 데이터가 존재합니다',
                ];

                Log::info("중복 데이터 건너뜀: {$name} ({$phone})");
                return null; // 중복이면 생성하지 않음
            }
        }

        return new Tenant([
            'user_id' => $this->userId,
            'branch_id' => $this->branchId,
            'name' => $name,
            'phone' => $phone,
            'gender' => !empty($row[2]) ? (string)$row[2] : null,
            'is_blacklisted' => $this->parseBoolean($row[3] ?? 'N'),
            'blacklist_memo' => !empty($row[4]) ? (string)$row[4] : null,
            'is_short_term' => $this->parseBoolean($row[5] ?? 'N'),
            'short_term_monthly_rent' => $this->parseNumber($row[6] ?? null),
            'short_term_deposit' => $this->parseNumber($row[7] ?? null),
        ]);
    }

    /**
     * 유효성 검사 규칙
     */
    public function rules(): array
    {
        return [
            '0' => 'required', // 이름
            '1' => 'nullable', // 전화번호
        ];
    }

    /**
     * 유효성 검사 커스텀 메시지
     */
    public function customValidationMessages()
    {
        return [
            '0.required' => '이름은 필수 항목입니다.',
        ];
    }

    /**
     * 숫자 파싱 (콤마 제거)
     */
    protected function parseNumber($value)
    {
        if (empty($value)) {
            return null;
        }
        return (int) str_replace(',', '', $value);
    }

    /**
     * Boolean 파싱 (Y/N)
     */
    protected function parseBoolean($value)
    {
        return strtoupper(trim($value ?? '')) === 'Y';
    }
}
