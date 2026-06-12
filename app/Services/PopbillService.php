<?php

namespace App\Services;

use Linkhub\Popbill\Cashbill;
use Linkhub\Popbill\PopbillCashbill;
use Linkhub\Popbill\PopbillException;
use Linkhub\Popbill\PopbillTaxinvoice;
use Linkhub\Popbill\Taxinvoice;
use Linkhub\Popbill\TaxinvoiceDetail;

class PopbillService
{
    protected array $config;
    protected ?PopbillTaxinvoice $taxinvoice = null;
    protected ?PopbillCashbill $cashbill = null;

    public function __construct()
    {
        $this->config = config('popbill');
    }

    public function taxinvoice(): PopbillTaxinvoice
    {
        if ($this->taxinvoice === null) {
            $this->taxinvoice = $this->apply(new PopbillTaxinvoice(
                $this->config['link_id'],
                $this->config['secret_key']
            ));
        }

        return $this->taxinvoice;
    }

    public function cashbill(): PopbillCashbill
    {
        if ($this->cashbill === null) {
            $this->cashbill = $this->apply(new PopbillCashbill(
                $this->config['link_id'],
                $this->config['secret_key']
            ));
        }

        return $this->cashbill;
    }

    /**
     * Cashbill DTO 팩토리.
     * 팝빌 SDK는 한 파일(PopbillCashbill.php)에 여러 클래스가 묶여 있고 PSR-4로는
     * 파일명이 일치하는 PopbillCashbill만 autoload되므로, Cashbill DTO를 사용하려면
     * 먼저 해당 파일이 로드되어 있어야 한다. 이 팩토리가 그 책임을 진다.
     */
    public function makeCashbill(): Cashbill
    {
        $this->cashbill(); // PopbillCashbill.php 로드 보장
        return new Cashbill();
    }

    public function getBalance(string $corpNum): float
    {
        return $this->taxinvoice()->GetBalance($corpNum);
    }

    /**
     * Taxinvoice DTO 팩토리. (PopbillCashbill과 같은 이유로 팩토리를 둔다)
     */
    public function makeTaxinvoice(): Taxinvoice
    {
        $this->taxinvoice();
        return new Taxinvoice();
    }

    public function makeTaxinvoiceDetail(): TaxinvoiceDetail
    {
        $this->taxinvoice();
        return new TaxinvoiceDetail();
    }

    public function registIssueTaxinvoice(string $corpNum, Taxinvoice $ti, ?string $memo = null)
    {
        return $this->taxinvoice()->RegistIssue($corpNum, $ti, $memo);
    }

    public function sendTaxinvoiceToNTS(string $corpNum, string $mgtKey)
    {
        return $this->taxinvoice()->SendToNTS($corpNum, 'SELL', $mgtKey);
    }

    public function getTaxinvoiceInfo(string $corpNum, string $mgtKey)
    {
        return $this->taxinvoice()->GetInfo($corpNum, 'SELL', $mgtKey);
    }

    public function getTaxinvoicePopUpURL(string $corpNum, string $mgtKey): string
    {
        return $this->taxinvoice()->GetPopUpURL($corpNum, 'SELL', $mgtKey);
    }

    public function getCashbillBalance(string $corpNum): float
    {
        return $this->cashbill()->GetBalance($corpNum);
    }

    /**
     * 현금영수증 즉시발행 (RegistIssue)
     *
     * @return object { code, message, confirmNum?, tradeDate? }
     * @throws PopbillException
     */
    public function issueCashbill(string $corpNum, Cashbill $cashbill, ?string $memo = null)
    {
        return $this->cashbill()->RegistIssue($corpNum, $cashbill, $memo);
    }

    /**
     * 이미 발행완료된 현금영수증의 취소발행(음수 금액 전표).
     * 팝빌은 RegistIssue로 발행된 건은 CancelIssue가 아닌 RevokeRegistIssue 를 사용해야 한다.
     *
     * @param string $corpNum          발행자 사업자번호(10자리, 숫자만)
     * @param string $newMgtKey        취소발행용 새 문서관리번호(원본과 달라야 함)
     * @param string $orgConfirmNum    원본 국세청승인번호
     * @param string $orgTradeDate     원본 거래일자(Ymd)
     * @param string|null $memo
     */
    public function revokeCashbill(
        string $corpNum,
        string $newMgtKey,
        string $orgConfirmNum,
        string $orgTradeDate,
        ?string $memo = null
    ) {
        return $this->cashbill()->RevokeRegistIssue(
            $corpNum,
            $newMgtKey,
            $orgConfirmNum,
            $orgTradeDate,
            false,
            $memo
        );
    }

    public function getCashbillInfo(string $corpNum, string $mgtKey)
    {
        return $this->cashbill()->GetInfo($corpNum, $mgtKey);
    }

    public function getCashbillPopUpURL(string $corpNum, string $mgtKey): string
    {
        return $this->cashbill()->GetPopUpURL($corpNum, $mgtKey);
    }

    public function isTestMode(): bool
    {
        return (bool) ($this->config['is_test'] ?? true);
    }

    /**
     * 공통 환경 플래그 적용.
     * 운영 전환 시 .env POPBILL_IS_TEST=false 만 변경하면 됨.
     */
    private function apply(object $client): object
    {
        $client->IsTest((bool) $this->config['is_test']);
        $client->IPRestrictOnOff((bool) $this->config['ip_restrict']);
        $client->UseStaticIP((bool) $this->config['use_static_ip']);
        $client->UseGAIP((bool) $this->config['use_ga_ip']);
        $client->UseLocalTimeYN((bool) $this->config['use_local_time']);

        return $client;
    }
}
