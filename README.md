[![License](https://poser.pugx.org/tcgunel/netgsm/license)](https://packagist.org/packages/tcgunel/netgsm)
[![Buy us a tree](https://img.shields.io/badge/Treeware-%F0%9F%8C%B3-lightgreen)](https://plant.treeware.earth/tcgunel/netgsm)

# Laravel Netgsm Channel Entegrasyonu (Laravel 7.x|8.x)
Netgsm hizmetlerini laravel ile kolay ve hızlıca kullanmaya başlamak için geliştirilen bir paket. Netgsm
tarafından sunulan tüm metodları (SOAP Servisi, XML POST ve HTTP GET) destekleyecek şekilde hazırlanmıştır.

Şimdilik sadece SMS hizmetleri için geliştirme yapılıyor diğer hizmetler için de gerekli oldukça 
geliştirme yapılacaktır.

### SMS Yapılacaklar Listesi

- [x] SMS Gönderimi
- [ ] SMS İptali
- [ ] SMS Sorgulama
- [ ] OTP Sorgulama
- [ ] Flash SMS
- [ ] Gelen SMS
- [ ] Gönderici Adı Sorgulama
- [x] Karakter Hesaplama
- [ ] İzinli / Kara liste

### Hesap Apileri Yapılacaklar Listesi

- [x] Kredi Sorgulama
- [x] Paket Kampanya Sorgulama

## Requirements
| PHP    | Laravel | Package |
|--------|---------|---------|
| ^7.2.5 | 7.x     | v1.0.0  |

| PHP    | Laravel | Package |
|--------|---------|---------|
| ^7.2.5 | 8.x     | v2.0.0  |

## Kurulum

1) Paket indirin:
```
composer require TCGunel/netgsm
```

2) Opsiyonel olarak config dosyasını çekebilirsiniz:
```
php artisan vendor:publish --tag=netgsm-config
```

Aşağıdaki ayarlar yapılabilmektedir;
* (string) username => Genel olarak kullanmak istenilen hesabın kullanıcı adı bilgisi.

* (string) password => Genel olarak kullanmak istenilen hesabın şifre bilgisi.

* (bool) log => Loglama işlemini aktif et, api ile yapılan her işlemi kayıt altına alır. 
  
* (string) service => Xml, soap veya http. 

* (bool) encoding => Türkçe desteği açıp kapat. Gönderilen SMS'lerdeki karakter hesabı bu ayara göre değişebilir. [Referans](https://www.netgsm.com.tr/dokuman/#soap-servisi-sms-g%C3%B6nderme)

* (string) header => Varsa mesaj başlık bilgisi, girilmezse username için girilen numara kullanılır.

* (string) filter => İzinli data filtrenizi uygulamak isterseniz '1' olarak gönderebilirsiniz. [Referans](https://www.netgsm.com.tr/dokuman/#soap-servisi-sms-g%C3%B6nderme)

* (string) bayikodu => Bayilik kodunuz varsa.

3) Loglama aktif olduğunda gerekli migration dosyasını publish etmek için,
```
php artisan vendor:publish --provider="TCGunel\Netgsm\NetgsmServiceProvider" --tag="migrations"
```

## Lumen compatibility

Lumen ile test edilmemiştir.

Sms Gönderim Örnekleri
====================
**1:n Gönderim**

```
    $sms = new \TCGunel\Netgsm\SendSms\SendSms();
    
    $sms->setMsg('MESAJ İÇERİĞİ');
    
    // Tek numaraya gönder.
    $sms
    
        ->setMsg('MESAJ İÇERİĞİ'); // Mesaj İçeriği
        
        ->setGsm(5554443322)
        
         //->setGsm([5554443322, 1112223322]);  // Birden fazla numaraya gönder. Son çağırılan setGsm değerleri kullanılır.
         
         //->setEncoding('1');  // Opsiyonel. Boş string TR destek kapat veya '1' ile aç.
         
         //->setStartdate('011220210100');  // Opsiyonel. ddMMyyyyHHmm formatında gönderme tarihi.
         
         //->setStopdate('011220212359');  // Opsiyonel. ddMMyyyyHHmm iki tarih arası gönderimlerde bitiş tarihi.
         
         //->setBayikodu('?');  // Opsiyonel. Bayi üyesi ise bayiye ait kod.
         
         //->setFilter('1');  // Opsiyonel. Dolu gönderilirse netgsm filtreniz uygulanarak yasaklı numaralara gönderim yapılmaz.
         
         //->setUsername('800800800');  // Opsiyonel. Gönderimi farklı bir hesap ile yapmak için.
         
         //->setPassword('*********');  // Opsiyonel. Gönderimi farklı bir hesap ile yapmak için.
    
    $sms->execute();
```

**n:n Gönderim**

Bir defada her numaraya kendi mesajını gönderebilmek için kullanılır.

```
->setMsg(['Mesaj 1', 'Mesaj 2', 'Mesaj 3']); // Mesaj içerikleri
        
->setGsm([5554443322, 4443332211, 3332221100])
```

Kredi Sorgulama
====================
```
$creditQuery = new CreditQuery();

$creditQuery

    ->setUsername('800800800') // Opsiyonel.
    
    ->setPassword('*********'); // Opsiyonel.


$creditQuery->execute();

$creditQuery->result; // String olarak TL bakiye barındırır. E.g. 150,77
```

Paket Sorgulama
====================
```
$packageCampaignQuery = new PackageCampaignQuery();

$packageCampaignQuery

    ->setUsername('800800800') // Opsiyonel.

    ->setPassword('*********'); // Opsiyonel.

$packageCampaignQuery->execute();

$packageCampaignQuery->result; // Hesaba bağlı tüm paket bilgisini array[] olarak barındırır.
```

## Test
```
composer test
```

## Authors

* [**Tolga Can GÜNEL**](https://github.com/tcgunel) - *Altyapı ve proje başlangıcı*

[comment]: <> (See also the list of [contributors]&#40;https://github.com/freshbitsweb/laravel-log-enhancer/graphs/contributors&#41; who participated in this project.)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details

## Treeware

This package is [Treeware](https://treeware.earth). If you use it in production, then we ask that you [**buy the world a tree**](https://plant.treeware.earth/tcgunel/netgsm) to thank us for our work. By contributing to the Treeware forest you’ll be creating employment for local families and restoring wildlife habitats.
