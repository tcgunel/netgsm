<?php

namespace TCGunel\Netgsm\SendSms;

use TCGunel\Netgsm\Exceptions\NetgsmSendSmsException;
use TCGunel\Netgsm\ServiceTypes;
use TCGunel\Netgsm\Traits\FormatParamsTrait;

class FormatParams
{
    use FormatParamsTrait;

    /**
     * @param integer|integer[]|string|string[] $gsm
     * @return FormatParams
     */
    protected function gsm(&$gsm): FormatParams
    {
        switch ($this->service_type) {
            case ServiceTypes::HTTP:

                if (is_array($gsm)) {

                    $gsm = join(',', $gsm);

                }

                break;

            default:

                if (is_string($gsm)) {

                    $gsm_numbers = explode(',', $gsm);

                    $gsm_numbers = array_map(function ($item) {

                        return trim($item);

                    }, $gsm_numbers);

                    $gsm = $gsm_numbers;

                }

                break;
        }

        if (is_array($gsm) && count($gsm) === 1) {

            $gsm = $gsm[0];

        }

        return $this;
    }

    /**
     * @param string|string[] $msg
     * @return FormatParams
     * @throws \Exception
     */
    protected function msg(&$msg): FormatParams
    {
        switch ($this->service_type) {
            case ServiceTypes::HTTP:

                if (is_array($msg)) {

                    throw new NetgsmSendSmsException('N:N Sms gönderimleriniz için soap veya xml servislerini kullanmalısınız.');

                }

                $msg = urlencode(
                    strip_tags(
                        preg_replace(
                            '/<br\s?\/?>/i', "\\n",
                            $msg
                        )
                    )
                );

                break;
        }

        if (is_array($msg) && count($msg) === 1) {

            $msg = $msg[0];

        }

        return $this;
    }

    /**
     * @param string|null $header
     * @param string $username
     * @return FormatParams
     */
    protected function header(?string &$header, string $username): FormatParams
    {
        if (in_array($header, ['', null])) {

            $header = $username;

        }

        return $this;
    }
}
