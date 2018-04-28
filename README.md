This is PHP wrapper/library for veritrans Air-Direct API.

* This Component is not an official one and only supports Tokens API and Charges API.
* This API is experimental and may be removed anytime without any notice.
* Please do not ask any questions about this API to [veritrans](https://www.veritrans.co.jp/).
* The author shall not be held responsible or liable, under any circumstances, for any damages resulting from using this component.

## Installation
1. Add this require line to your `composer.json`:

```
{
    "require": {
        "yumaeda/veritrans": "1.0.*"
    }
}
```
2. `composer install` on your terminal.

## How to Use

```
<?php
use Yumaeda\Payment\Veritrans\Veritrans;
use Yumaeda\Payment\Veritrans\CreditCard;

$order_id     = <Order ID>;     // Unique order ID for your veritrans
$client_key   = <ClientKey>;    // Your veritrans client key
$server_key   = <ServerKey>;    // Your veritrans server key
$total        = <Total in JPY>; // e.g. 9999
$card_number  = <CardNumber>;   // e.g. '111111111111111'
$cvv          = <CVV>;          // e.g. '1234'
$expire_month = <Exp Month>;    // e.g. 9
$expire_year  = <Exp Year>;     // e.g. 2022

$veritrans = new Veritrans($client_key, $server_key);
$credit_card = new CreditCard($card_number, $expire_month, $expire_year, $cvv);

$veritrans->setCreditCard($credit_card);
$veritrans->charge($order_id, $total, true);
```

## Testing
Not available

## Contributing
Please send a pull request.

## Support
Please send an email to yumaeda@gmail.com.

## Author
Yukitaka Maeda

## Software License
MIT
