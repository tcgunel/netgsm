<?php

namespace TCGunel\Netgsm\Traits;

use TCGunel\Netgsm\Constants\ResponseTypes;
use TCGunel\Netgsm\Services\NetgsmLogger;

trait ErrorsTrait
{
    private static $send_sms_errors = [
        "20" => "Mesaj metninde ki problemden dolayı gönderilemediğini veya standart maksimum mesaj
                karakter sayısını geçtiğini ifade eder. (Standart maksimum karakter sayısı 917 dir.
                Eğer mesajınız türkçe karakter içeriyorsa Türkçe Karakter Hesaplama menüsunden karakter
                sayılarının hesaplanış şeklini görebilirsiniz.)",

        "30" => "Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin
                olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip
                dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı ,
                web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.",

        "40" => "Mesaj başlığınızın (gönderici adınızın) sistemde tanımlı olmadığını
                ifade eder. Gönderici adlarınızı API ile sorgulayarak kontrol edebilirsiniz.",

        "70" => "Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan
                birinin eksik olduğunu ifade eder.",

        "80" => "Gönderim sınır aşımı.",

        "100" => "Sistem hatası",

        "101" => "Sistem hatası"
    ];

    private static $credit_query_errors = [
        "30" => "Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin
                olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip
                dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı ,
                web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.",

        "40" => "Arama kriterlerinize göre listelenecek kayıt olmadığını ifade eder.",

        "60" => "Hesabınızda tanımlı paket veya kampanyanız bulunmamaktadır.",

        "70" => "Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan
                birinin eksik olduğunu ifade eder.",

        "100" => "Sistem hatası, sınır aşımı.(dakikada en fazla 5 kez sorgulanabilir.)",

        "101" => "Sistem hatası"
    ];

    private static $package_campaign_query_errors = [
        "30" => "Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin
                olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip
                dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı ,
                web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.",

        "40" => "Arama kriterlerinize göre listelenecek kayıt olmadığını ifade eder.",

        "70" => "Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan
                birinin eksik olduğunu ifade eder.",

        "100" => "Sistem hatası, sınır aşımı.(dakikada en fazla 5 kez sorgulanabilir.)",

        "101" => "Sistem hatası"
    ];

    /**
     * Reads responses and throws error messages taken directly from the documentation
     *
     * @url https://www.netgsm.com.tr/dokuman/
     *
     * @param string $work_type
     * @param string $response
     * @throws \Exception
     */
    protected function handleNetgsmErrors(string $work_type, string $response): void
    {
        $property = $work_type . '_errors';

        if (property_exists(self::class, $property)) {

            if (in_array($response, array_keys(self::${$property}))) {

                NetgsmLogger::$response_code = $response;

                NetgsmLogger::$response_message = self::${$property}[$response];

                NetgsmLogger::$response_type = ResponseTypes::ERROR;

                NetgsmLogger::create();

                throw new \Exception(self::${$property}[$response], 422);
            }

        }
    }
}
