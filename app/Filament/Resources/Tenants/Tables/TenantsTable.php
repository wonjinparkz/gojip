<?php

namespace App\Filament\Resources\Tenants\Tables;

use App\Models\TaxInvoice;
use App\Models\Tenant;
use App\Services\PopbillService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Linkhub\Popbill\PopbillException;

class TenantsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('이름')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('연락처')
                    ->searchable(),
                Tables\Columns\TextColumn::make('room_number')
                    ->label('호실')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('room_type')
                    ->label('방 유형'),
                Tables\Columns\TextColumn::make('monthly_rent')
                    ->label('월세')
                    ->money('KRW')
                    ->sortable(),
                Tables\Columns\TextColumn::make('move_in_date')
                    ->label('입주일')
                    ->date('Y.m.d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_payment_date')
                    ->label('마지막 입금일')
                    ->date('Y.m.d')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('결제 방법')
                    ->formatStateUsing(fn (string $state = null): string => match($state) {
                        'card' => '카드',
                        'transfer' => '계좌이체',
                        'cash' => '현금',
                        default => '-',
                    })
                    ->placeholder('-'),
                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('납부 상태')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'paid' => '납부완료',
                        'overdue' => '연체',
                        'pending' => '미납',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'paid',
                        'danger' => 'overdue',
                        'warning' => 'pending',
                    ]),
                Tables\Columns\TextColumn::make('move_out_date')
                    ->label('퇴실일')
                    ->date('Y.m.d')
                    ->placeholder('-')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('상태')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'active' => '입주중',
                        'inactive' => '퇴실',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'active',
                        'secondary' => 'inactive',
                    ]),
                Tables\Columns\IconColumn::make('is_blacklisted')
                    ->label('블랙리스트')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_status')
                    ->label('납부 상태')
                    ->options([
                        'paid' => '납부완료',
                        'pending' => '미납',
                        'overdue' => '연체',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->label('상태')
                    ->options([
                        'active' => '입주중',
                        'inactive' => '퇴실',
                    ]),
                Tables\Filters\TernaryFilter::make('is_blacklisted')
                    ->label('블랙리스트')
                    ->placeholder('전체')
                    ->trueLabel('블랙리스트만')
                    ->falseLabel('정상만'),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('issueTaxInvoice')
                    ->label('세금계산서 발행')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->visible(fn (Tenant $record) => !empty($record->business_number))
                    ->requiresConfirmation()
                    ->modalHeading('전자세금계산서 발행 및 국세청 전송')
                    ->modalDescription('월 임대료 기준으로 세금계산서를 발행하고 국세청에 즉시 전송합니다.')
                    ->action(function (Tenant $record, PopbillService $popbill) {
                        $branch = $record->branch;
                        if (!$branch || empty($branch->business_number)) {
                            Notification::make()
                                ->title('지점 사업자번호가 없습니다.')
                                ->danger()->send();
                            return;
                        }

                        $invoicerCorpNum = preg_replace('/\D/', '', $branch->business_number);
                        $invoiceeCorpNum = preg_replace('/\D/', '', $record->business_number);
                        if (strlen($invoicerCorpNum) !== 10 || strlen($invoiceeCorpNum) !== 10) {
                            Notification::make()->title('사업자번호 형식이 올바르지 않습니다.')->danger()->send();
                            return;
                        }

                        $total = (int) ($record->monthly_rent ?? 0);
                        $supply = (int) round($total / 1.1);
                        $tax = $total - $supply;

                        $invoice = DB::transaction(fn () => TaxInvoice::create([
                            'tenant_id' => $record->id,
                            'branch_id' => $record->branch_id,
                            'mgt_key' => TaxInvoice::generateMgtKey(),
                            'invoicer_corp_num' => $invoicerCorpNum,
                            'invoicee_corp_num' => $invoiceeCorpNum,
                            'item_name' => '월 임대료',
                            'supply_cost' => $supply,
                            'tax' => $tax,
                            'total_amount' => $total,
                            'write_date' => now()->toDateString(),
                        ]));

                        $ti = $popbill->makeTaxinvoice();
                        $ti->writeDate = now()->format('Ymd');
                        $ti->chargeDirection = '정과금';
                        $ti->issueType = '정발행';
                        $ti->purposeType = '영수';
                        $ti->taxType = '과세';
                        $ti->invoicerCorpNum = $invoicerCorpNum;
                        $ti->invoicerMgtKey = $invoice->mgt_key;
                        $ti->invoicerCorpName = $branch->name;
                        $ti->invoicerCEOName = $branch->ceo_name ?? '';
                        $ti->invoicerAddr = $branch->business_address ?? $branch->address ?? '';
                        $ti->invoicerBizClass = $branch->business_class ?? '';
                        $ti->invoicerBizType = $branch->business_type ?? '';
                        $ti->invoicerContactName = $branch->ceo_name ?? '';
                        $ti->invoicerEmail = '';
                        $ti->invoiceeType = '사업자';
                        $ti->invoiceeCorpNum = $invoiceeCorpNum;
                        $ti->invoiceeCorpName = $record->corp_name ?? $record->name;
                        $ti->invoiceeCEOName = $record->corp_ceo_name ?? $record->name;
                        $ti->invoiceeAddr = $record->corp_address ?? '';
                        $ti->invoiceeBizClass = $record->corp_business_class ?? '';
                        $ti->invoiceeBizType = $record->corp_business_type ?? '';
                        $ti->invoiceeContactName1 = $record->name;
                        $ti->invoiceeEmail1 = $record->email ?? '';
                        $ti->supplyCostTotal = (string) $supply;
                        $ti->taxTotal = (string) $tax;
                        $ti->totalAmount = (string) $total;

                        $detail = $popbill->makeTaxinvoiceDetail();
                        $detail->serialNum = 1;
                        $detail->purchaseDT = now()->format('Ymd');
                        $detail->itemName = '월 임대료';
                        $detail->qty = '1';
                        $detail->unitCost = (string) $supply;
                        $detail->supplyCost = (string) $supply;
                        $detail->tax = (string) $tax;
                        $ti->detailList = [$detail];

                        try {
                            $res = $popbill->registIssueTaxinvoice($invoicerCorpNum, $ti, '월 임대료 세금계산서');
                            $invoice->update([
                                'nts_confirm_num' => $res->ntsConfirmNum ?? null,
                                'state_code' => '3020', // 발행 완료
                                'issued_at' => now(),
                            ]);

                            try {
                                $popbill->sendTaxinvoiceToNTS($invoicerCorpNum, $invoice->mgt_key);
                                $invoice->update([
                                    'state_code' => '3030',
                                    'sent_to_nts_at' => now(),
                                ]);
                            } catch (PopbillException $e) {
                                Log::warning('popbill taxinvoice SendToNTS failed', [
                                    'code' => $e->getCode(), 'message' => $e->getMessage(),
                                ]);
                            }

                            Notification::make()
                                ->title('세금계산서 발행 완료')
                                ->body('승인번호: '.($invoice->nts_confirm_num ?? '대기중'))
                                ->success()->send();
                        } catch (PopbillException $e) {
                            Log::warning('popbill taxinvoice issue failed', [
                                'code' => $e->getCode(), 'message' => $e->getMessage(),
                            ]);
                            $invoice->delete();
                            Notification::make()
                                ->title('발행 실패')
                                ->body($e->getMessage())
                                ->danger()->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
