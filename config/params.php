<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'user.passwordResetTokenExpire' => 3600,
    'bsVersion' => '5.x',
    'sessia' => [
        'url' => 'https://api.sessia.com/api',
    ],    
    'marketplace' => [
        'ozon' => [
            'url' => 'https://api-seller.ozon.ru',
            'clientID' => '786171', // 472899
            'apiKey' => '61e1a701-587f-496d-8034-f6a121618e3c',
            'user' => [
                'name' => 'Маркетплейсов Озон Дмитриевич',
                'email' => 'sessia.ozon@yandex.ru',
                'phone' => '+79000006966',
            ],
        ],
        'wildberries' => [
            'suppliers' => [
                'url' => 'https://suppliers-api.wildberries.ru',
                'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhY2Nlc3NJRCI6ImZiMDk2ZDQ3LTdhNzUtNDRmMS05MjA3LTE0ZjcwYWNiNTczNSJ9.lPj1aAgHRr1nCCeBeAjr1R6CeskzSPfUfdhmD2CtGcI',
            ],
            'statistics' => [
                'url' => 'https://statistics-api.wildberries.ru',
                'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhY2Nlc3NJRCI6Ijg5MzYyMzU0LWJlMjMtNGY4OC04OTJjLTdhNGU1ZjVlZmFlZCJ9.aO669Cjw8Ov027D7faK4qzX0S2x95S64V2PbIUrC-dg',
            ],
            'user' => [
                'name' => 'Маркетплейсов Ягодка Иванович',
                'email' => 'sessia.wildberries@yandex.ru',
                'phone' => '+79000925344',
            ],
        ],
    ],
];
