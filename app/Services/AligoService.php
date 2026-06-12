<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class AligoService
{
    protected array $config;

    public function __construct()
    {
        $this->config = config('aligo');
    }

    public function isTest(): bool
    {
        return (bool) ($this->config['is_test'] ?? true);
    }

    public function send(string $receiver, string $message, ?string $title = null, array $extra = []): array
    {
        $sender = $this->normalizePhone($this->config['sender'] ?? '');
        if ($sender === '') {
            throw new RuntimeException('ALIGO_SENDER 가 설정되지 않았습니다.');
        }

        $payload = array_merge([
            'key'      => $this->config['api_key'],
            'user_id'  => $this->config['user_id'],
            'sender'   => $sender,
            'receiver' => $this->normalizePhone($receiver),
            'msg'      => $message,
            'msg_type' => $this->measureBytes($message) > 90 ? 'LMS' : 'SMS',
            'testmode_yn' => $this->isTest() ? 'Y' : 'N',
        ], $extra);

        if ($title !== null && $title !== '') {
            $payload['title'] = $title;
        }

        $endpoint = rtrim($this->config['base_url'], '/').'/send/';
        return $this->post($endpoint, $payload);
    }

    public function remain(): array
    {
        $endpoint = rtrim($this->config['base_url'], '/').'/remain/';
        return $this->post($endpoint, [
            'key'     => $this->config['api_key'],
            'user_id' => $this->config['user_id'],
        ]);
    }

    protected function post(string $endpoint, array $payload): array
    {
        $res = Http::asForm()
            ->timeout((int) ($this->config['timeout'] ?? 10))
            ->post($endpoint, $payload);

        $json = $res->json();
        if (!is_array($json)) {
            throw new RuntimeException('알리고 응답 파싱 실패: '.$res->body());
        }
        return $json;
    }

    protected function normalizePhone(string $phone): string
    {
        return preg_replace('/\D/', '', $phone) ?? '';
    }

    /**
     * Aligo는 EUC-KR 기준 바이트 수로 SMS(90 bytes) / LMS 분기.
     * 한글 1자=2 bytes, ASCII 1 byte, 이모지 등 BMP 외 문자는 4 bytes로 취급.
     */
    protected function measureBytes(string $message): int
    {
        $bytes = 0;
        foreach (mb_str_split($message, 1, 'UTF-8') as $ch) {
            $code = mb_ord($ch, 'UTF-8') ?: 0;
            if ($code > 0xFFFF) {
                $bytes += 4;
            } elseif ($code > 0x7F) {
                $bytes += 2;
            } else {
                $bytes += 1;
            }
        }
        return $bytes;
    }
}
