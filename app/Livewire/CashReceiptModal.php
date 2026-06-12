<?php

namespace App\Livewire;

use App\Models\CashReceipt;
use App\Models\Tenant;
use App\Services\PopbillService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Linkhub\Popbill\PopbillException;
use Livewire\Attributes\On;
use Livewire\Component;

class CashReceiptModal extends Component
{
    public bool $isOpen = false;
    public string $mode = 'issue'; // issue | view
    public ?int $tenantId = null;
    public ?Tenant $tenant = null;
    public ?CashReceipt $receipt = null;

    // 발행 폼
    public string $receiptType = 'personal'; // personal | business
    public string $identifierNumber = '';

    protected function rules(): array
    {
        return [
            'receiptType' => 'required|in:personal,business',
            'identifierNumber' => 'required|string|min:10|max:13',
        ];
    }

    protected $messages = [
        'identifierNumber.required' => '번호를 입력해주세요.',
        'identifierNumber.min' => '올바른 번호를 입력해주세요.',
    ];

    #[On('open-cash-receipt-modal')]
    public function openIssueModal(int $tenantId): void
    {
        $this->resetState();
        $this->tenantId = $tenantId;
        $this->tenant = Tenant::with(['room', 'latestCashReceipt'])->find($tenantId);
        $this->mode = 'issue';
        $this->isOpen = true;
    }

    #[On('view-cash-receipt')]
    public function openViewModal(int $tenantId): void
    {
        $this->resetState();
        $this->tenantId = $tenantId;
        $this->tenant = Tenant::with(['room', 'latestCashReceipt'])->find($tenantId);
        $this->receipt = $this->tenant?->latestCashReceipt;
        $this->mode = 'view';
        $this->isOpen = true;
    }

    public function closeModal(): void
    {
        $this->isOpen = false;
        $this->resetState();
    }

    public function issue(PopbillService $popbill): void
    {
        $this->validate();

        if (!$this->tenant) {
            $this->closeModal();
            return;
        }

        $branch = $this->tenant->branch;
        if (!$branch || empty($branch->business_number)) {
            $this->addError('identifierNumber', '지점 사업자번호가 등록되어 있지 않습니다. 설정에서 먼저 등록해주세요.');
            return;
        }

        $amount = (int) ($this->tenant->room?->monthly_rent ?? 0);
        $corpNum = preg_replace('/\D/', '', (string) $branch->business_number);
        $identifier = preg_replace('/\D/', '', $this->identifierNumber);

        $receipt = DB::transaction(function () use ($amount, $identifier) {
            return CashReceipt::create([
                'tenant_id' => $this->tenant->id,
                'branch_id' => $this->tenant->branch_id,
                'receipt_number' => CashReceipt::generateReceiptNumber(),
                'popbill_mgt_key' => CashReceipt::generatePopbillMgtKey(),
                'issued_date' => now()->toDateString(),
                'trade_date' => now()->format('Ymd'),
                'amount' => $amount,
                'receipt_type' => $this->receiptType,
                'identifier_number' => $identifier,
                'payment_method' => $this->tenant->payment_method,
                'state_code' => '100', // 임시: 발행요청 전
            ]);
        });

        $cb = $popbill->makeCashbill();
        $cb->mgtKey = $receipt->popbill_mgt_key;
        $cb->tradeType = '승인거래';
        $cb->tradeUsage = $this->receiptType === 'business' ? '지출증빙용' : '소득공제용';
        $cb->taxationType = '과세';
        $cb->totalAmount = (string) $amount;
        $cb->supplyCost = (string) (int) round($amount / 1.1);
        $cb->tax = (string) ($amount - (int) round($amount / 1.1));
        $cb->serviceFee = '0';
        $cb->franchiseCorpNum = $corpNum;
        $cb->franchiseCorpName = $branch->name;
        $cb->franchiseCEOName = $branch->ceo_name ?? '';
        $cb->franchiseAddr = $branch->business_address ?? $branch->address ?? '';
        $cb->franchiseTEL = $branch->phone ?? '';
        $cb->identityNum = $identifier;
        $cb->customerName = $this->tenant->name ?? '';
        $cb->itemName = '월 임대료';
        $cb->orderNumber = $receipt->receipt_number;
        $cb->email = $this->tenant->email ?? '';
        $cb->hp = $this->tenant->phone ?? '';
        $cb->smssendYN = false;

        try {
            $res = $popbill->issueCashbill($corpNum, $cb, '월 임대료 현금영수증');

            $receipt->update([
                'confirm_num' => $res->confirmNum ?? null,
                'trade_date' => $res->tradeDate ?? $receipt->trade_date,
                'state_code' => '200', // 발행완료
                'issued_at' => now(),
            ]);

            $this->receipt = $receipt->fresh();
            $this->mode = 'view';
            $this->dispatch('payment-updated');
        } catch (PopbillException $e) {
            Log::warning('popbill cashbill issue failed', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'mgtKey' => $receipt->popbill_mgt_key,
            ]);

            $receipt->delete();
            $this->addError('identifierNumber', '현금영수증 발행 실패: '.$e->getMessage());
        }
    }

    public function cancel(PopbillService $popbill): void
    {
        if (!$this->receipt || $this->receipt->isCanceled()) {
            return;
        }
        if (empty($this->receipt->confirm_num) || empty($this->receipt->trade_date)) {
            $this->addError('cancel', '원본 국세청승인번호가 없어 취소할 수 없습니다.');
            return;
        }

        $branch = $this->receipt->branch;
        $corpNum = preg_replace('/\D/', '', (string) ($branch->business_number ?? ''));
        if (strlen($corpNum) !== 10) {
            $this->addError('cancel', '지점 사업자번호를 확인해주세요.');
            return;
        }

        $revokeMgtKey = 'RV'.now()->format('YmdHis').strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));

        try {
            $res = $popbill->revokeCashbill(
                $corpNum,
                $revokeMgtKey,
                $this->receipt->confirm_num,
                $this->receipt->trade_date,
                '월 임대료 현금영수증 취소'
            );

            $this->receipt->update([
                'revoke_mgt_key' => $revokeMgtKey,
                'revoke_confirm_num' => $res->confirmNum ?? null,
                'state_code' => '300', // 취소발행 완료
                'canceled_at' => now(),
                'cancel_memo' => '월 임대료 현금영수증 취소',
            ]);

            $this->receipt = $this->receipt->fresh();
            $this->dispatch('payment-updated');
        } catch (PopbillException $e) {
            Log::warning('popbill cashbill revoke failed', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'receipt_id' => $this->receipt->id,
            ]);
            $this->addError('cancel', '취소 실패: '.$e->getMessage());
        }
    }

    public function openPopupUrl(PopbillService $popbill): void
    {
        if (!$this->receipt || empty($this->receipt->popbill_mgt_key)) {
            return;
        }
        $branch = $this->receipt->branch;
        $corpNum = preg_replace('/\D/', '', (string) ($branch->business_number ?? ''));
        if (strlen($corpNum) !== 10) {
            return;
        }

        try {
            $url = $popbill->getCashbillPopUpURL($corpNum, $this->receipt->popbill_mgt_key);
            $this->dispatch('open-external-url', url: $url);
        } catch (PopbillException $e) {
            Log::warning('popbill popup url failed', [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
            ]);
            $this->addError('popup', '팝업 URL 생성 실패: '.$e->getMessage());
        }
    }

    private function resetState(): void
    {
        $this->reset(['tenantId', 'tenant', 'receipt', 'identifierNumber', 'mode']);
        $this->receiptType = 'personal';
    }

    public function render()
    {
        return view('livewire.cash-receipt-modal');
    }
}
