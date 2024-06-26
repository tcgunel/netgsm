<?php

namespace TCGunel\Netgsm\Traits;

use TCGunel\Netgsm\Constants\ResponseTypes;
use TCGunel\Netgsm\Services\NetgsmLogger;
use TCGunel\Netgsm\ServiceTypes;

trait SuccessfulResponseTrait
{
    private static $send_sms = [
        "00" => "Görevinizin tarih formatinda bir hata olmadığını gösterir.",

        "01" => "Mesaj gönderim baslangıç tarihinde hata var. Sistem tarihi ile değiştirilip işleme alındı.",

        "02" => "Mesaj gönderim sonlandırılma tarihinde hata var.Sistem tarihi ile değiştirilip işleme alındı.
                Bitiş tarihi başlangıç tarihinden küçük girilmiş ise, sistem bitiş tarihine içinde bulunduğu
                tarihe 24 saat ekler.",

        "1000" => "n:n gönderimlerde bu hata alınabilir, gönderdiğiniz SMSinizin başarıyla sistemimize ulaştığını
                  gösterir. Bu outputu almanızın anlamı, 5 dakika boyunca ard arda gönderdiğiniz SMS'lerin sistemimiz
                  tarafında çoklanarak (biriktirilerek) gönderileceği anlamına gelir.Bu görevid (bulkid) niz
                  sorgulanamaz.",
    ];

    /**
     * Reads responses and throws error messages taken directly from the documentation
     *
     * @url https://www.netgsm.com.tr/dokuman/
     *
     * @param string $work_type
     * @param string $response
     */
    protected function handleNetgsmResponse(string $work_type, string $response): void
    {
        if (method_exists(self::class, $work_type)) {

            self::$work_type($response);

        }
    }

    protected function send_sms(string $response)
    {
        $parts = explode(' ', $response);

        NetgsmLogger::$response_message = null;

        NetgsmLogger::$response_code = $response;

        if (in_array($parts[0], array_keys(self::$send_sms))) {

            NetgsmLogger::$response_message = self::$send_sms[$parts[0]];

            $this->result_code = $parts[0];

            $this->result = $parts[1];

        } else {

            $this->result = $response;

        }

        NetgsmLogger::$response_type = ResponseTypes::SUCCESS;

        NetgsmLogger::create();
    }

    protected function credit_query(string $response)
    {
        [$this->result_code, $this->result] = explode(' ', $response);

        NetgsmLogger::$response_code = $this->result_code;

        NetgsmLogger::$response_message = $this->result;

        NetgsmLogger::$response_type = ResponseTypes::SUCCESS;

        NetgsmLogger::create();
    }

    protected function package_campaign_query(string $response)
    {
        $lines = preg_split('/<br>/i', $response);

        foreach ($lines as $k => $line) {

            $parts = explode('|', $line);

            $this->result[$k] = [];

            foreach ($parts as $part) {

                $trimmed = trim($part);

                if ($trimmed !== "") {

                    $this->result[$k][] = $trimmed;

                }

            }
        }

        $this->result = array_filter($this->result);

        NetgsmLogger::$response_code = null;

        NetgsmLogger::$response_message = $response;

        NetgsmLogger::$response_type = ResponseTypes::SUCCESS;

        NetgsmLogger::create();
    }

    protected function send_otp_sms(string $response)
    {
        switch ($this->service_type) {
            case ServiceTypes::HTTP:

                [$this->result_code, $this->result] = explode(' ', $response);

                break;

            case ServiceTypes::XML:

                $xml = new \SimpleXMLElement($response);

                foreach ($xml->main->children() as $xml_node_name => $xml_node_value ){

                    if ($xml_node_name == 'code'){
                        $this->result_code = (string) $xml_node_value;
                    }

                    if ($xml_node_name == 'jobID'){
                        $this->result = (int) $xml_node_value;
                    }

                }

                break;
        }

        NetgsmLogger::$response_code = $this->result_code;

        NetgsmLogger::$response_message = $this->result;

        NetgsmLogger::$response_type = ResponseTypes::SUCCESS;

        NetgsmLogger::create();
    }
}
