# hatmeoNew/CheckoutCod

> This package is a part of NexaMerchant, a Laravel E-commerce package. This package is used to provide a Cash on Delivery payment method for the NexaMerchant package.

[![Build Status](https://github.com/hatmeoNew/CheckoutCod/workflows/Laravel/badge.svg)](https://github.com/hatmeoNew/CheckoutCod)
[![Release](https://img.shields.io/github/release/hatmeoNew/CheckoutCod.svg?style=flat-square)](https://github.com/hatmeoNew/CheckoutCod/releases)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/Nexa-Merchant/CheckoutCod.svg?style=flat-square)](https://packagist.org/packages/hatmeo/CheckoutCod)
[![Total Downloads](https://img.shields.io/packagist/dt/hatmeoNew/CheckoutCod.svg?style=flat-square)](https://packagist.org/packages/hatmeo/CheckoutCod)

# What is COD?

Cash on delivery (COD) is a type of transaction in which the recipient makes payment for a good at the time of delivery. If the purchaser does not make payment when the good is delivered, the good is returned to the seller. The COD method is used by many businesses that sell goods through a catalog or online.


# How to Install

```
NexaMerchant\CheckoutCod\Providers\CheckoutCodServiceProvider::class,
```
Add it to config/app.php $providers

# How to Install with Composer

```
composer require hatmeo/checkoutcod
```

# How to Publish the Config file

```bash
php artisan vendor:publish --provider="NexaMerchant\CheckoutCod\Providers\CheckoutCodServiceProvider"
```

