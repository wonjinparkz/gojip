<?php

namespace App\Imports;

use App\Models\Tenant;
use App\Models\Room;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Facades\Log;

class ScheduleImport implements ToModel, WithStartRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;

    protected $branchId;
    protected $userId;
    protected $skippedRows = []; // 중복으로 건너뛴 행

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
        // 행 구조: [0: 이름, 1: 전화번호, 2: 호실, 3: 유형, 4: 입주일, 5: 퇴실일, 6: 퇴실일미정, 7: 단기숙박, 8: 단기숙박월세, 9: 단기숙박보증금]

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
                    'room' => $row[2] ?? '',
                    'reason' => '동일한 전화번호로 이미 등록된 데이터가 존재합니다',
                ];

                Log::info("중복 데이터 건너뜀: {$name} ({$phone})");
                return null; // 중복이면 생성하지 않음
            }
        }

        // 호실 정보 조회 (숫자를 문자열로 변환)
        $roomNumber = !empty($row[2]) ? (string)$row[2] : null;
        $room = null;
        if (!empty($roomNumber)) {
            $room = Room::where('branch_id', $this->branchId)
                ->where('room_number', $roomNumber)
                ->first();
        }

        // 퇴실일미정 여부
        $indefiniteMoveOut = $this->parseBoolean($row[6] ?? 'N');

        // 날짜 정보
        $moveInDate = $this->parseDate($row[4] ?? null);
        $moveOutDate = $indefiniteMoveOut ? null : $this->parseDate($row[5] ?? null);

        // 호실과 날짜 겹침 체크
        if (!empty($roomNumber) && !empty($moveInDate)) {
            $query = Tenant::where('user_id', $this->userId)
                ->where('room_number', $roomNumber);

            // 날짜 겹침 체크: 기존 일정과 겹치는지 확인
            if ($moveOutDate) {
                // 퇴실일이 있는 경우
                $query->where(function($q) use ($moveInDate, $moveOutDate) {
                    $q->where(function($subQ) use ($moveInDate, $moveOutDate) {
                        // 기존 일정의 입주일이 새 일정 기간 내에 있거나
                        $subQ->whereBetween('move_in_date', [$moveInDate, $moveOutDate]);
                    })->orWhere(function($subQ) use ($moveInDate, $moveOutDate) {
                        // 기존 일정의 퇴실일이 새 일정 기간 내에 있거나
                        $subQ->whereBetween('move_out_date', [$moveInDate, $moveOutDate]);
                    })->orWhere(function($subQ) use ($moveInDate, $moveOutDate) {
                        // 새 일정이 기존 일정 기간 내에 완전히 포함되는 경우
                        $subQ->where('move_in_date', '<=', $moveInDate)
                             ->where('move_out_date', '>=', $moveOutDate);
                    });
                });
            } else {
                // 퇴실일 미정인 경우 - 입주일 이후의 모든 일정과 겹침
                $query->where(function($q) use ($moveInDate) {
                    $q->where('move_in_date', '<=', $moveInDate)
                      ->where(function($subQ) use ($moveInDate) {
                          $subQ->whereNull('move_out_date')
                               ->orWhere('move_out_date', '>=', $moveInDate);
                      });
                });
            }

            $conflictingSchedule = $query->first();

            if ($conflictingSchedule) {
                // 날짜 겹침 데이터 저장
                $this->skippedRows[] = [
                    'name' => $name,
                    'phone' => $phone ?? '',
                    'room' => $roomNumber,
                    'date' => $moveInDate . ' ~ ' . ($moveOutDate ?? '미정'),
                    'reason' => '같은 호실에 겹쳐진 일정이 존재합니다',
                ];

                Log::info("날짜 겹침 데이터 건너뜀: {$name} - {$roomNumber}호 ({$moveInDate} ~ " . ($moveOutDate ?? '미정') . ")");
                return null; // 겹치면 생성하지 않음
            }
        }

        // 유형은 엑셀에서 입력받거나, 호실에서 가져옴
        $roomType = !empty($row[3]) ? (string)$row[3] : $room?->room_type;

        return new Tenant([
            'user_id' => $this->userId,
            'branch_id' => $this->branchId,
            'room_id' => $room?->id,
            'name' => $name,
            'phone' => $phone,
            'room_number' => $roomNumber,
            'room_type' => $roomType,
            'monthly_rent' => $room?->monthly_rent,
            'move_in_date' => $moveInDate,
            'move_out_date' => $moveOutDate,
            'indefinite_move_out' => $indefiniteMoveOut,
            'is_short_term' => $this->parseBoolean($row[7] ?? 'N'),
            'short_term_monthly_rent' => $this->parseNumber($row[8] ?? null),
            'short_term_deposit' => $this->parseNumber($row[9] ?? null),
            'payment_status' => 'pending',
            'status' => 'active',
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
            '2' => 'nullable', // 호실
            '3' => 'nullable', // 유형
            '4' => 'nullable', // 입주일
            '5' => 'nullable', // 퇴실일
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
     * 날짜 파싱
     */
    protected function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            // 엑셀 날짜 형식이 숫자인 경우 처리
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::warning("날짜 파싱 실패: {$value}");
            return null;
        }
    }

    /**
     * Boolean 파싱 (Y/N)
     */
    protected function parseBoolean($value)
    {
        return strtoupper(trim($value ?? '')) === 'Y';
    }
}
