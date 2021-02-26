<?php

namespace TCGunel\Netgsm\SendOtpSms;

use TCGunel\Netgsm\ServiceTypes;
use TCGunel\Netgsm\Traits\FormatParamsTrait;

class FormatParams
{
    use FormatParamsTrait;

    /**
     * @param string $msg
     * @return FormatParams
     * @throws \Exception
     */
    protected function msg(string &$msg): FormatParams
    {
        /**
         * OTP mesajlar türkçe karakter içeremez.
         */
        $msg = str_replace(
            ['Ç','ç','Ğ','ğ','ı','İ','Ö','ö','Ş','ş','Ü','ü'],
            ['C','c','G','g','i','I','O','o','S','s','U','u'],
            $msg
        );

        switch ($this->service_type) {
            case ServiceTypes::HTTP:

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
