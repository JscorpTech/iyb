<?php

namespace JscorpTech\IYB\Services;

use Exception;
use GuzzleHttp\Client;
use Jscorptech\IYB\Enums\TransactionStatus;

class API
{
    public const BASE_URL = "https://etest.ipakyulibank.uz:4443/ecomm2/MerchantHandler";
    public Client $client;
    public string $certificate_key;
    public string $certificate;

    function __construct(string $certificate, string $certificate_key)
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
            null => TransactionStatus::FAILED,
        };
    }

    function create_transaction(int $amount, string $ip = "1.2.3.4", string $description = "No Description", string $lang = "UZ", $currency = "860")
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
    public function check_status(string $trans_id)
    {
        $data = http_build_query([
            "command" => "v",
            "trans_id" => $trans_id,
        ]);
        $url = self::BASE_URL . "?$data";
        try {
            $response = $this->client->post($url, [
                "cert" => $this->certificate,
                "ssl_key" => $this->certificate_key,
                "verify" => false
            ]);
            return $this->parse_transaction_status($this->parse_response($response->getBody()->getContents())["RESULT"] ?? null);
        } catch (\Exception $e) {
            throw new Exception("Error creating transaction: " . $e->getMessage());
        }
    }
}
