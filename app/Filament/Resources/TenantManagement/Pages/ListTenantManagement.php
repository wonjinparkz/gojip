<?php

namespace App\Filament\Resources\TenantManagement\Pages;

use App\Filament\Resources\TenantManagement\TenantManagementResource;
use App\Models\Branch;
use App\Models\Tenant;
use App\Exports\TenantsExport;
use App\Exports\TenantsTemplateExport;
use App\Imports\TenantsImport;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Maatwebsite\Excel\Facades\Excel;

class ListTenantManagement extends ListRecords
{
    protected static string $resource = TenantManagementResource::class;

    protected string $view = 'filament.resources.tenant-management.pages.list-tenant-management';

    public function getHeading(): string
    {
        $branchId = session('current_branch_id');
        $branch = Branch::find($branchId);

        return $branch ? "{$branch->name} 입주자 관리" : '입주자 관리';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create')
                ->label('입주자 추가하기')
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->action(fn () => $this->dispatch('open-tenant-management-modal')),
            Actions\ActionGroup::make([
                Action::make('downloadTemplate')
                    ->label('엑셀 파일 가이드 다운로드')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('primary')
                    ->action(function () {
                        $branchId = session('current_branch_id');
                        $branch = Branch::find($branchId);
                        $filename = $branch ? "{$branch->name}_입주자_템플릿.xlsx" : '입주자_템플릿.xlsx';

                        Notification::make()
                            ->title('템플릿 다운로드 완료')
                            ->success()
                            ->send();

                        return Excel::download(new TenantsTemplateExport(), $filename);
                    }),
                Action::make('uploadExcel')
                    ->label('엑셀 파일 업로드')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('success')
                    ->form([
                        Forms\Components\FileUpload::make('file')
                            ->label('엑셀 파일')
                            ->acceptedFileTypes([
                                'application/vnd.ms-excel',
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                            ])
                            ->required()
                            ->helperText('엑셀 파일 가이드에 맞춰 작성한 파일을 업로드하세요.')
                            ->disk('local')
                            ->directory('temp-uploads'),
                    ])
                    ->action(function (array $data) {
                        $branchId = session('current_branch_id');

                        if (!$branchId) {
                            Notification::make()
                                ->title('지점을 선택해주세요')
                                ->danger()
                                ->send();
                            return;
                        }

                        try {
                            $filePath = storage_path('app/' . $data['file']);

                            // 엑셀 파일 헤더 검증
                            $import = new TenantsImport($branchId, auth()->id());

                            Excel::import($import, $filePath);

                            // 에러가 있는 경우
                            if (count($import->failures()) > 0) {
                                $errorMessages = [];
                                foreach ($import->failures() as $failure) {
                                    $errorMessages[] = "행 {$failure->row()}: " . implode(', ', $failure->errors());
                                }

                                Notification::make()
                                    ->title('일부 데이터 업로드에 실패했습니다')
                                    ->body(implode("\n", array_slice($errorMessages, 0, 3)))
                                    ->danger()
                                    ->send();

                                // 임시 파일 삭제
                                @unlink($filePath);
                                return;
                            }

                            // 중복으로 건너뛴 데이터 확인
                            $skippedRows = $import->getSkippedRows();

                            if (count($skippedRows) > 0) {
                                $skippedList = [];
                                foreach (array_slice($skippedRows, 0, 5) as $skipped) {
                                    $phoneInfo = !empty($skipped['phone']) ? " ({$skipped['phone']})" : '';
                                    $skippedList[] = "• {$skipped['name']}{$phoneInfo}: 동일한 전화번호로 이미 등록된 데이터가 존재합니다";
                                }

                                $moreCount = count($skippedRows) > 5 ? '외 ' . (count($skippedRows) - 5) . '건' : '';

                                Notification::make()
                                    ->title('일부 데이터가 업로드되지 않았습니다')
                                    ->body("다음 데이터들은 충돌 방지를 위해 업로드되지 않았습니다. 입주자 관리 페이지에서 직접 수정해주세요:\n\n" . implode("\n", $skippedList) . ($moreCount ? "\n" . $moreCount : ''))
                                    ->warning()
                                    ->duration(15000)
                                    ->send();
                            }

                            Notification::make()
                                ->title('입주자 데이터가 업로드되었습니다')
                                ->success()
                                ->send();

                            // 임시 파일 삭제
                            @unlink($filePath);

                            // 페이지 새로고침
                            $this->redirect(static::getUrl());
                        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                            $failures = $e->failures();
                            $errorMessages = [];

                            foreach ($failures as $failure) {
                                $errorMessages[] = "행 {$failure->row()}: " . implode(', ', $failure->errors());
                            }

                            Notification::make()
                                ->title('엑셀 파일 형식이 올바르지 않습니다')
                                ->body('엑셀 파일 가이드를 다운로드하여 양식을 지켜 다시 작성 후 업로드해주세요. ' . implode(', ', array_slice($errorMessages, 0, 2)))
                                ->danger()
                                ->duration(10000)
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('업로드 실패')
                                ->body('엑셀 파일 가이드를 다운로드하여 양식을 지켜 다시 작성 후 업로드해주세요. 오류: ' . $e->getMessage())
                                ->danger()
                                ->duration(10000)
                                ->send();
                        }
                    })
                    ->modalWidth(Width::Large),
                Action::make('downloadExcel')
                    ->label('엑셀 파일 다운로드')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('warning')
                    ->action(function () {
                        $branchId = session('current_branch_id');
                        $branch = Branch::find($branchId);
                        $filename = $branch ? "{$branch->name}_입주자_목록.xlsx" : '입주자_목록.xlsx';

                        Notification::make()
                            ->title('다운로드 완료')
                            ->success()
                            ->send();

                        return Excel::download(new TenantsExport($branchId), $filename);
                    }),
            ])
                ->label('엑셀 관리')
                ->icon('heroicon-o-table-cells')
                ->color('primary')
                ->button(),
        ];
    }
}
