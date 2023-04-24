<?php

return [
    'adminEmail' => 'sessia.marketplace@yandex.ru',
    'senderEmail' => 'sessia.marketplace@yandex.ru',
    'senderName' => 'SESSIA Marketplace',
    'user.passwordResetTokenExpire' => 3600,
    'bsVersion' => '5.x',
    'sessia' => [
        'url' => 'https://api.sessia.com/api',
    ],    
    'marketplaceImportLimit' => 1, // сколько заказов заливать за одну синхронизацию
    'stores' => [
        [
            'id' => 6641,
            'name' => 'NRK87.',
            'email' => 'info@nrk87.com',
        ],
        [
            'id' => 8291,
            'name' => 'UME',
            'email' => 'info@ume.pet',
        ],
        [
            'id' => 6278,
            'name' => 'ProjectV',
            'email' => 'info@projectvint.ru',
        ],
        [
            'id' => 6232,
            'name' => 'CoffeeCell',
            'email' => 'info@coffeecell.com',
        ],
    ],
    'marketplace' => [
        'ozon' => [
            'active' => true,
            'class' => '\app\models\Ozon',
            'url' => 'https://api-seller.ozon.ru',
            'clientID' => '472899',
            // 'clientID' => '786171',
            'apiKey' => '61e1a701-587f-496d-8034-f6a121618e3c',
            'user' => [
                'name' => 'Маркетплейсов Озон Дмитриевич',
                'email' => 'sessia.ozon@yandex.ru',
                'phone' => '+79000006966',
            ],
            'mailto' => [
                'dmitry.boltunov@freedomgroupint.com',
                'sessia.marketplace@yandex.ru',
            ],
        ],
        'wildberries' => [
            'active' => true,
            'class' => '\app\models\Wildberries',
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
            'mailto' => [
                'dmitry.boltunov@freedomgroupint.com',
                'sessia.marketplace@yandex.ru',
            ],
        ],
    ],
];
