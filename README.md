<p align="center">
<a href='https://my.rightel.ir'  target="_blank">
<img  src='https://www.rightel.ir/rightel-bootstrap-theme/images/logo99-head.png'></img></a></p>

<p align="center">
<a href="https://packagist.org/packages/bahramali/myrightel-php" target="_blank"><img src="https://img.shields.io/packagist/dt/bahramali/myrightel-php" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/bahramali/myrightel-php" target="_blank"><img src="https://img.shields.io/packagist/v/bahramali/myrightel-php" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/bahramali/myrightel-php" target="_blank"><img src="https://img.shields.io/packagist/l/bahramali/myrightel-php?" alt="License"></a>
</p>

  

# MyRightel-PHP (No captcha)
MyRightel-PHP is a PHP library for interacting with MyRightel
#### You can log in without captcha

## Installation

```
composer require bahramali/myrightel-php
```

## Usage

```php
use MyRightelPHP\MyRightel;
set_time_limit(0);
require_once  __DIR__  .  '/vendor/autoload.php';

$MyRightel = new  MyRightel('phone number', 'Password');
```
```php
$MyRightel->getInventory(); // get inventory
$MyRightel->getFridayGift(); // get friday gift
```

## Example

*  [`index.php`](https://github.com/ErfanBahramali/MyRightel-PHP/blob/main/examples/index.php)

## About Us

This library can be used for easy interaction with MyRightel just like official applications.
#### You can log in without captcha


## Disclaimer


<b>This library is free and can not be sold.</b>


<b>The responsibility for using this library lies with the individual</b>

## License

MyRightel-PHP is licensed under the MIT License - see the [LICENSE](LICENSE) file for details