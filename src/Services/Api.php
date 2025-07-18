<?php

namespace JscorpTech\IYB\Services;

use Exception;
use GuzzleHttp\Client;
use JscorpTech\IYB\Enums\TransactionStatus;

class Api
{
    public const BASE_URL = "https://etest.ipakyulibank.uz:4443/ecomm2/MerchantHandler";
    public Client $client;
    public string $certificate_key;
    public string $certificate;

    public function __construct(string $certificate, string $certificate_key)
    {
        $this->client = new Client();
        $this->certificate = $certificate;
        $this->certificate_key = $certificate_key;
    }

    public function parse_response(string $data)
    {
        $segments = explode("\n", $data);
        $result = [];
        foreach ($segments as $segment) {
            if (strpos($segment, ":") !== false) {
                list($key, $value) = explode(":", $segment);
                $result[trim($key)] = trim($value);
            }
        }
        return $result;
    }

    public function parse_transaction_status(string $status)
    {
        return match ($status) {
            "CREATED" => TransactionStatus::CREATED,
            "OK" => TransactionStatus::OK,
            "CANCELED" => TransactionStatus::CANCELED,
            default => TransactionStatus::FAILED
        };
    }

    /**
     * Yangi transaction yaratish
     */
    public function create_transaction(int $amount, string $ip = "1.2.3.4", string $description = "No Description", string $lang = "UZ", $currency = "860")
    {
        $data = http_build_query([
            "command" => "v",
            "amount" => $amount,
            "currency" => $currency,
            "client_ip_addr" => $ip,
            "description" => $description,
            "language" => $lang,
        ]);
        $url = self::BASE_URL . "?$data";
        try {
            $response = $this->client->post($url, [
                "cert" => $this->certificate,
                "ssl_key" => $this->certificate_key,
                "verify" => false
            ]);
            return $this->parse_response($response->getBody()->getContents())["TRANSACTION_ID"];
        } catch (\Exception $e) {
            throw new Exception("Error creating transaction: " . $e->getMessage());
        }
    }

    /**
     *Transaction holatini tekshirish
     */
    public function check_transaction_status(string $trans_id): TransactionStatus
    {
        $data = http_build_query([
            "command" => "c",
            "trans_id" => $trans_id,
        ]);
        $url = self::BASE_URL . "?$data";
        try {
            $response = $this->client->post($url, [
                "cert" => $this->certificate,
                "ssl_key" => $this->certificate_key,
                "verify" => false
            ]);
            $data = $this->parse_response($response->getBody()->getContents());
            if (!isset($data['RESULT'])) {
                throw new Exception($data['error'] ?? "Unknown error");
            }
            return $this->parse_transaction_status($data["RESULT"] ?? "");
        } catch (\Exception $e) {
            throw new Exception("Error creating transaction: " . $e->getMessage());
        }
    }
}
