<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Linkhub\Popbill\Taxinvoice;
use Linkhub\Popbill\TaxinvoiceDetail;

$service = app(App\Services\PopbillService::class)->taxinvoice();
$corpNum = '1908702845';
$mgtKey = 'TEST'.date('YmdHis');

$ti = new Taxinvoice();
$ti->writeDate = date('Ymd');
$ti->chargeDirection = '정과금';
$ti->issueType = '정발행';
$ti->purposeType = '영수';
$ti->taxType = '과세';

$ti->invoicerCorpNum = $corpNum;
$ti->invoicerMgtKey = $mgtKey;
$ti->invoicerCorpName = '공급자 테스트';
$ti->invoicerCEOName = '대표자';
$ti->invoicerAddr = '서울시 테스트구';
$ti->invoicerBizClass = '업종';
$ti->invoicerBizType = '업태';
$ti->invoicerContactName = '담당자';
$ti->invoicerEmail = '';

$ti->invoiceeType = '사업자';
$ti->invoiceeCorpNum = '8888888888';
$ti->invoiceeCorpName = '공급받는자 테스트';
$ti->invoiceeCEOName = '대표자';
$ti->invoiceeAddr = '서울시 테스트구';
$ti->invoiceeBizClass = '업종';
$ti->invoiceeBizType = '업태';
$ti->invoiceeContactName1 = '담당자';
$ti->invoiceeEmail1 = '';

$ti->supplyCostTotal = '10000';
$ti->taxTotal = '1000';
$ti->totalAmount = '11000';

$detail = new TaxinvoiceDetail();
$detail->serialNum = 1;
$detail->purchaseDT = date('Ymd');
$detail->itemName = '테스트 품목';
$detail->qty = '1';
$detail->unitCost = '10000';
$detail->supplyCost = '10000';
$detail->tax = '1000';
$ti->detailList = [$detail];

try {
    echo "== RegistIssue ==\n";
    $res = $service->RegistIssue($corpNum, $ti, '발행메모');
    echo "code: {$res->code}\nmessage: {$res->message}\n";
    echo "ntsConfirmNum: ".($res->ntsConfirmNum ?? '')."\n";

    echo "\n== GetInfo ==\n";
    $info = $service->GetInfo($corpNum, 'SELL', $mgtKey);
    echo "stateCode: {$info->stateCode}\n";
    echo "stateMemo: ".($info->stateMemo ?? '')."\n";

    echo "\n== SendToNTS ==\n";
    $res2 = $service->SendToNTS($corpNum, 'SELL', $mgtKey);
    echo "code: {$res2->code}\nmessage: {$res2->message}\n";
} catch (\Throwable $e) {
    echo 'ERR: '.$e->getMessage()."\n";
}
