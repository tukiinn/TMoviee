<?php

return [
    'vnpay' => [
        'tmn_code' => env('VNPAY_TMN_CODE', 'Z4TFCUTA'),
        'hash_secret' => env('VNPAY_HASH_SECRET', 'YZH3Y12YNHEMI6FKI7AIEANDEJVG0QIM'),
        'url' => env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
    ],

    'momo' => [
        'partner_code' => env('MOMO_PARTNER_CODE', 'MOMOBKUN20180529'),
        'access_key' => env('MOMO_ACCESS_KEY', 'klm05TvNBzhg7h7j'),
        'secret_key' => env('MOMO_SECRET_KEY', 'at67qH6mk8w5Y1nAyMoYKMWACiEi2BSa'),
        'endpoint' => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
    ],

]; 