<?php

namespace TCGunel\Netgsm\Traits;

use TCGunel\Netgsm\Constants\ResponseTypes;
use TCGunel\Netgsm\Exceptions\NetgsmException;
use TCGunel\Netgsm\Services\NetgsmLogger;

trait ErrorsTrait
{
	private static $send_sms_errors = [
		20 => "Mesaj metninde ki problemden dolayı gönderilemediğini veya standart maksimum mesaj
                karakter sayısını geçtiğini ifade eder. (Standart maksimum karakter sayısı 917 dir.
                Eğer mesajınız türkçe karakter içeriyorsa Türkçe Karakter Hesaplama menüsunden karakter
                sayılarının hesaplanış şeklini görebilirsiniz.)",

		30 => "Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin
                olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip
                dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı ,
                web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.",

		40 => "Mesaj başlığınızın (gönderici adınızın) sistemde tanımlı olmadığını
                ifade eder. Gönderici adlarınızı API ile sorgulayarak kontrol edebilirsiniz.",

		70 => "Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan
                birinin eksik olduğunu ifade eder.",

		80 => "Gönderim sınır aşımı.",

		100 => "Sistem hatası",

		101 => "Sistem hatası"
	];

	private static $credit_query_errors = [
		30 => "Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin
                olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip
                dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı ,
                web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.",

		40 => "Arama kriterlerinize göre listelenecek kayıt olmadığını ifade eder.",

		60 => "Hesabınızda tanımlı paket veya kampanyanız bulunmamaktadır.",

		70 => "Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan
                birinin eksik olduğunu ifade eder.",

		100 => "Sistem hatası, sınır aşımı.(dakikada en fazla 5 kez sorgulanabilir.)",

		101 => "Sistem hatası"
	];

	private static $package_campaign_query_errors = [
		30 => "Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin
                olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip
                dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı ,
                web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.",

		40 => "Arama kriterlerinize göre listelenecek kayıt olmadığını ifade eder.",

		70 => "Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan
                birinin eksik olduğunu ifade eder.",

		100 => "Sistem hatası, sınır aşımı.(dakikada en fazla 5 kez sorgulanabilir.)",

		101 => "Sistem hatası"
	];

	private static $send_otp_sms_errors = [
		20 => "Mesaj metni ya da mesaj boyunu kontrol ediniz.",
		21 => "Mesaj metni ya da mesaj boyunu kontrol ediniz.",

		30 => "Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin
                olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip
                dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı ,
                web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.",
		39 => "Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin
                olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip
                dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı ,
                web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.",

		40 => "Gönderici adınızı kontrol ediniz.",
		41 => "Gönderici adınızı kontrol ediniz.",

		50 => "Gönderilen numarayı kontrol ediniz.",
		52 => "Gönderilen numarayı kontrol ediniz.",

		60 => "Hesabınızda OTP SMS Paketi tanımlı değildir, kontrol ediniz.",
		63 => "Hesabınızda OTP SMS Paketi tanımlı değildir, kontrol ediniz.",
		64 => "Hesabınızda OTP SMS Paketi tanımlı değildir, kontrol ediniz.",
		69 => "Hesabınızda OTP SMS Paketi tanımlı değildir, kontrol ediniz.",

		70 => "Input parametrelerini kontrol ediniz.",
		79 => "Input parametrelerini kontrol ediniz.",

		80 => "Sorgulama sınır aşımı.(dakikada 100 adet gönderim yapılabilir.)",

		100 => "Sistem hatası",
		110 => "Sistem hatası",
	];

	/**
	 * @return string[]
	 */
	public static function getCreditQueryErrors(): array
	{
		return self::$credit_query_errors;
	}

	/**
	 * @return string[]
	 */
	public static function getPackageCampaignQueryErrors(): array
	{
		return self::$package_campaign_query_errors;
	}

	/**
	 * @return string[]
	 */
	public static function getSendSmsErrors(): array
	{
		return self::$send_sms_errors;
	}

	/**
	 * @return string[]
	 */
	public static function getSendOtpSmsErrors(): array
	{
		return self::$send_otp_sms_errors;
	}

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

			$response = self::getCodeFromXmlResponse($response);

			if (array_key_exists($response, self::${$property})) {

				NetgsmLogger::$response_code = $response;

				NetgsmLogger::$response_message = self::${$property}[$response];

				NetgsmLogger::$response_type = ResponseTypes::ERROR;

				NetgsmLogger::create();

				throw new NetgsmException(self::${$property}[$response], 422);
			}

		}
	}

	protected function getCodeFromXmlResponse(string $response)
	{
		if (strpos($response, '<?xml') !== FALSE) {

			$xml = new \SimpleXMLElement($response);

			foreach ($xml->main->children() as $xml_node_name => $xml_node_value ){

				if ($xml_node_name == 'code'){

					return (string) $xml_node_value;

				}
			}
		}

		return $response;
	}
}
