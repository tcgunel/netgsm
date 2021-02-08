<?php

namespace TCGunel\Netgsm\Traits;

trait ErrorsTrait
{
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
        if (method_exists(self::class, $work_type)) {

            self::$work_type($response);

        }
    }

    protected function send_sms(string $response)
    {
        switch ($response) {
            case "20":
                throw new \Exception(
                    'Mesaj metninde ki problemden dolayı gönderilemediğini veya standart maksimum mesaj
                    karakter sayısını geçtiğini ifade eder. (Standart maksimum karakter sayısı 917 dir.
                    Eğer mesajınız türkçe karakter içeriyorsa Türkçe Karakter Hesaplama menüsunden karakter
                    sayılarının hesaplanış şeklini görebilirsiniz.)',
                    422
                );

            case "30":
                throw new \Exception(
                    'Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin
                        olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip
                        dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı ,
                        web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.',
                    422
                );

            case "40":
                throw new \Exception(
                    'Mesaj başlığınızın (gönderici adınızın) sistemde tanımlı olmadığını
                        ifade eder. Gönderici adlarınızı API ile sorgulayarak kontrol edebilirsiniz.',
                    422
                );

            case "70":
                throw new \Exception(
                    'Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan
                    birinin eksik olduğunu ifade eder.',
                    422
                );

            case "80":
                throw new \Exception(
                    'Gönderim sınır aşımı.',
                    422
                );

            case "100":
            case "101":
                throw new \Exception(
                    'Sistem hatası',
                    422
                );
        }
    }

    protected function credit_query(string $response)
    {
        switch ($response) {
            case "30":
                throw new \Exception(
                    'Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin
                        olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip
                        dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı ,
                        web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.',
                    422
                );

            case "40":
                throw new \Exception(
                    'Arama kriterlerinize göre listelenecek kayıt olmadığını ifade eder.',
                    422
                );

            case "60":
                throw new \Exception(
                    'Hesabınızda tanımlı paket veya kampanyanız bulunmamaktadır.',
                    422
                );

            case "70":
                throw new \Exception(
                    'Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan
                    birinin eksik olduğunu ifade eder.',
                    422
                );

            case "100":
                throw new \Exception(
                    'Sistem hatası, sınır aşımı.(dakikada en fazla 5 kez sorgulanabilir.)',
                    422
                );

            case "101":
                throw new \Exception(
                    'Sistem hatası',
                    422
                );
        }
    }

    protected function package_campaign_query(string $response)
    {
        switch ($response) {
            case "30":
                throw new \Exception(
                    'Ggeçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin
                        olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip
                        dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı ,
                        web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.',
                    422
                );

            case "40":
                throw new \Exception(
                    'Arama kriterlerinize göre listelenecek kayıt olmadığını ifade eder.',
                    422
                );

            case "70":
                throw new \Exception(
                    'Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan
                    birinin eksik olduğunu ifade eder.',
                    422
                );

            case "100":
                throw new \Exception(
                    'Sistem hatası, sınır aşımı.(dakikada en fazla 5 kez sorgulanabilir.)',
                    422
                );

            case "101":
                throw new \Exception(
                    'Sistem hatası',
                    422
                );
        }
    }
}
