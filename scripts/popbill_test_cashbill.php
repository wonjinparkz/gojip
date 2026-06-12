<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Services\PopbillService;
use Linkhub\Popbill\PopbillException;

/** @var PopbillService $popbill */
$popbill = app(PopbillService::class);

$corpNum = '1908702845'; // 기존 taxinvoice 테스트와 동일 (팝빌 테스트 연동원)
$mgtKey = 'CB'.date('YmdHis').strtoupper(bin2hex(random_bytes(2)));

$cb = $popbill->makeCashbill();
$cb->mgtKey = $mgtKey;
$cb->tradeType = '승인거래';
$cb->tradeUsage = '소득공제용';
$cb->taxationType = '과세';
$cb->totalAmount = '55000';
$cb->supplyCost = '50000';
$cb->tax = '5000';
$cb->serviceFee = '0';
$cb->franchiseCorpNum = $corpNum;
$cb->franchiseCorpName = '테스트 고시원';
$cb->franchiseCEOName = '대표자';
$cb->franchiseAddr = '서울시 테스트구';
$cb->franchiseTEL = '02-0000-0000';
$cb->identityNum = '01012345678'; // 개인 식별: 휴대폰
$cb->customerName = '테스트 입주자';
$cb->itemName = '월 임대료';
$cb->orderNumber = 'ORD'.date('YmdHis');
$cb->email = '';
$cb->hp = '01012345678';
$cb->smssendYN = false;

try {
    echo "== isTestMode ==\n";
    var_export($popbill->isTestMode());
    echo "\n";

    echo "\n== GetCashbillBalance ==\n";
    echo $popbill->getCashbillBalance($corpNum)."\n";

    echo "\n== RegistIssue (cashbill) ==\n";
    echo "mgtKey: $mgtKey\n";
    $res = $popbill->issueCashbill($corpNum, $cb, '테스트 현금영수증 발행');
    echo "code: {$res->code}\nmessage: {$res->message}\n";
    echo "confirmNum: ".($res->confirmNum ?? '')."\n";
    echo "tradeDate: ".($res->tradeDate ?? '')."\n";

    echo "\n== GetInfo ==\n";
    $info = $popbill->getCashbillInfo($corpNum, $mgtKey);
    echo "stateCode: ".($info->stateCode ?? '')."\n";
    echo "confirmNum: ".($info->confirmNum ?? '')."\n";
    echo "totalAmount: ".($info->totalAmount ?? '')."\n";

    echo "\n== GetPopUpURL ==\n";
    echo $popbill->getCashbillPopUpURL($corpNum, $mgtKey)."\n";

    echo "\n== RevokeRegistIssue ==\n";
    $revokeMgtKey = 'RV'.date('YmdHis').strtoupper(bin2hex(random_bytes(2)));
    echo "revokeMgtKey: $revokeMgtKey\n";
    $c = $popbill->revokeCashbill(
        $corpNum,
        $revokeMgtKey,
        $res->confirmNum,
        $res->tradeDate,
        '테스트 취소발행'
    );
    echo "code: {$c->code}\nmessage: {$c->message}\n";
    echo "confirmNum(revoke): ".($c->confirmNum ?? '')."\n";

    echo "\n== GetInfo (revoke doc) ==\n";
    $info2 = $popbill->getCashbillInfo($corpNum, $revokeMgtKey);
    echo "stateCode: ".($info2->stateCode ?? '')."\n";
    echo "totalAmount: ".($info2->totalAmount ?? '')."\n";
} catch (PopbillException $e) {
    echo 'POPBILL_ERR code='.$e->getCode().' msg='.$e->getMessage()."\n";
} catch (\Throwable $e) {
    echo 'ERR: '.$e->getMessage()."\n";
}
