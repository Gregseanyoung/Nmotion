<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('calculate order totals using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addAnonymousUserFixture();

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$restaurant = $I->addRestaurantFixture(
    [
        'name'           => 'calculate.order@nmotion.pp.ciklum.com',
        'email'          => 'calculate.order@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'calculate.order@nmotion.pp.ciklum.com'
        ],
        'address'        => [],
        'menuCategories' => [
            0 => [
                'name'             => 'Beef',
                'time_from'        => 0,
                'time_to'          => 86300,
                'discount_percent' => 9,
                'visible'          => 1,
                'menuMeals'        => [
                    [
                        'name'             => 'Beef steak',
                        'price'            => 250,
                        'discount_percent' => 50,
                        'visible'          => 1
                    ]
                ]
            ],
            1 => [
                'name'             => 'Pork',
                'time_from'        => 0,
                'time_to'          => 86300,
                'discount_percent' => 15,
                'visible'          => 1,
                'menuMeals'        => [
                    [
                        'name'             => 'Roasted pig',
                        'price'            => 850,
                        'discount_percent' => 0,
                        'visible'          => 1
                    ]
                ]
            ],
            2 => [
                'name'             => 'Drinks',
                'time_from'        => 0,
                'time_to'          => 86300,
                'discount_percent' => 0,
                'visible'          => 1,
                'menuMeals'        => [
                    [
                        'name'             => 'Cola',
                        'price'            => 12,
                        'discount_percent' => 0,
                        'visible'          => 1
                    ]
                ]
            ]
        ]
    ]
);

$restaurantId = $restaurant['id'];

$beefSteakMeal    = $restaurant['menuCategories'][0]['menuMeals'][0];
$beefSteakMealId  = $beefSteakMeal['id'];
$roastedPigMeal   = $restaurant['menuCategories'][1]['menuMeals'][0];
$roastedPigMealId = $roastedPigMeal['id'];
$colaMeal         = $restaurant['menuCategories'][2]['menuMeals'][0];
$colaMealId       = $colaMeal['id'];

$I->addRestaurantCheckinFixture(
    [
        'user_id'       => $user['id'],
        'restaurant_id' => $restaurantId,
        'table_number'  => rand(1, 900)
    ]
);

$I->amGoingTo('send POST order request with first meal');

$params1 = [
    'orderMeals' => [
        [
            'name' => 'Beef steak',
            'price' => 250,
            'meal' => $beefSteakMealId,
            'quantity' => 1,
            'discountPercent' => 50
        ]
    ]
];
$I->sendPOST('/api/v1/restaurants/' . $restaurantId . '/orders', $params1);

$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            [
                'orderMeals' => [
                    [
                        "discountPrice"                       => 125,
                        "mealOptionDiscountPrice"             => 0,
                        "mealDiscountPercent"                 => 50,
                        "discountPriceIncludingTax"           => 156.25,
                        "priceIncludingTax"                   => 312.5,
                        "mealOptionDiscountPriceIncludingTax" => 0,
                        "mealOptionPriceIncludingTax"         => 0,
                        "name"                                => "Beef steak",
                        "price"                               => 250,
                        "discountPercent"                     => 50,
                        "quantity"                            => 1,
                        'meal'                                => ['id' => $beefSteakMealId],
                    ]
                ],
                "orderStatus"  => ["id" => 1],
                "productTotal" => 250,
                "discount"     => 131.25,
                "salesTax"     => 29.69,
                "tips"         => 0,
                "orderTotal"   => 148.44
            ]
        ]
    ]
);
$order = $I->grabDataFromJsonResponse('entries.0');

$I->amGoingTo('send PUT order request with first and second meals');

$params2 = [
    'orderMeals' => [
        [
            'id' => $order['orderMeals'][0]['id'],
            'name' => 'Beef steak',
            'price' => 250,
            'meal' => $beefSteakMealId,
            'quantity' => 1,
            'discountPercent' => 50
        ],
        [
            'name' => 'Roasted pig',
            'price' => 850,
            'meal' => $roastedPigMealId,
            'quantity' => 1,
            'discountPercent' => 15
        ],
    ]
];
$I->sendPUT('/api/v1/orders/' . $order['id'] . '.json', $params2);

$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            [
                'orderMeals' => [
                    [
                        "discountPrice"                       => 125,
                        "mealOptionDiscountPrice"             => 0,
                        "mealDiscountPercent"                 => 50,
                        "discountPriceIncludingTax"           => 156.25,
                        "priceIncludingTax"                   => 312.5,
                        "mealOptionDiscountPriceIncludingTax" => 0,
                        "mealOptionPriceIncludingTax"         => 0,
                        "name"                                => "Beef steak",
                        "price"                               => 250,
                        "discountPercent"                     => 50,
                        "quantity"                            => 1,
                        'meal'                                => ['id' => $beefSteakMealId]
                    ],
                    [
                        "discountPrice"                       => 722.5,
                        "mealOptionDiscountPrice"             => 0,
                        "mealDiscountPercent"                 => 15,
                        "discountPriceIncludingTax"           => 903.13,
                        "priceIncludingTax"                   => 1062.5,
                        "mealOptionDiscountPriceIncludingTax" => 0,
                        "mealOptionPriceIncludingTax"         => 0,
                        "name"                                => "Roasted pig",
                        "price"                               => 850,
                        "discountPercent"                     => 0,
                        "quantity"                            => 1,
                        'meal'                                => ['id' => $roastedPigMealId]
                    ]
                ],
                "orderStatus"  => ["id" => 1],
                "productTotal" => 1100,
                "discount"     => 294.88,
                "salesTax"     => 201.28,
                "tips"         => 0,
                "orderTotal"   => 1006.4
            ]
        ]
    ]
);
$order2 = $I->grabDataFromJsonResponse('entries.0');

$I->amGoingTo('send PUT order request with first, second and third meals');

$params3 = [
    'orderMeals' => [
        [
            'name' => 'Cola',
            'price' => 12,
            'meal' => $colaMealId,
            'quantity' => 1,
            'discountPercent' => 0
        ],
        [
            'id' => $order2['orderMeals'][0]['id'],
            'name' => 'Beef steak',
            'price' => 250,
            'meal' => $beefSteakMealId,
            'quantity' => 1,
            'discountPercent' => 50
        ],
        [
            'id' => $order2['orderMeals'][1]['id'],
            'name' => 'Roasted pig',
            'price' => 850,
            'meal' => $roastedPigMealId,
            'quantity' => 1,
            'discountPercent' => 15
        ]
    ]
];
$I->sendPUT('/api/v1/orders/' . $order['id'] . '.json', $params3);

$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            [
                'orderMeals' => [
                    [
                        "discountPrice"                       => 125,
                        "mealOptionDiscountPrice"             => 0,
                        "mealDiscountPercent"                 => 50,
                        "discountPriceIncludingTax"           => 156.25,
                        "priceIncludingTax"                   => 312.5,
                        "mealOptionDiscountPriceIncludingTax" => 0,
                        "mealOptionPriceIncludingTax"         => 0,
                        "name"                                => "Beef steak",
                        "price"                               => 250,
                        "discountPercent"                     => 50,
                        "quantity"                            => 1,
                        'meal'                                => ['id' => $beefSteakMealId]
                    ],
                    [
                        "discountPrice"                       => 722.5,
                        "mealOptionDiscountPrice"             => 0,
                        "mealDiscountPercent"                 => 15,
                        "discountPriceIncludingTax"           => 903.13,
                        "priceIncludingTax"                   => 1062.5,
                        "mealOptionDiscountPriceIncludingTax" => 0,
                        "mealOptionPriceIncludingTax"         => 0,
                        "name"                                => "Roasted pig",
                        "price"                               => 850,
                        "discountPercent"                     => 0,
                        "quantity"                            => 1,
                        'meal'                                => ['id' => $roastedPigMealId]
                    ],
                    [
                        "discountPrice"                       => 12,
                        "mealOptionDiscountPrice"             => 0,
                        "mealDiscountPercent"                 => 0,
                        "discountPriceIncludingTax"           => 15,
                        "priceIncludingTax"                   => 15,
                        "mealOptionDiscountPriceIncludingTax" => 0,
                        "mealOptionPriceIncludingTax"         => 0,
                        "name"                                => "Cola",
                        "price"                               => 12,
                        "discountPercent"                     => 0,
                        "quantity"                            => 1,
                        'meal'                                => ['id' => $colaMealId]
                    ]
                ],
                "orderStatus"  => ["id" => 1],
                "productTotal" => 1112,
                "discount"     => 295.48,
                "salesTax"     => 204.13,
                "tips"         => 0,
                "orderTotal"   => 1020.65
            ]
        ]
    ]
);
$order3 = $I->grabDataFromJsonResponse('entries.0');

$I->amGoingTo('send PUT order request with first, second and third meals with mixed up positions');

$params4 = [
    'orderMeals' => [
        [
            'id' => $order3['orderMeals'][0]['id'],
            'name' => 'Beef steak',
            'price' => 250,
            'meal' => $beefSteakMealId,
            'quantity' => 1,
            'discountPercent' => 50
        ],
        [
            'id' => $order3['orderMeals'][2]['id'],
            'name' => 'Cola',
            'price' => 12,
            'meal' => $colaMealId,
            'quantity' => 1,
            'discountPercent' => 0
        ],
        [
            'id' => $order3['orderMeals'][1]['id'],
            'name' => 'Roasted pig',
            'price' => 850,
            'meal' => $roastedPigMealId,
            'quantity' => 1,
            'discountPercent' => 15
        ]
    ]
];
$I->sendPUT('/api/v1/orders/' . $order['id'] . '.json', $params4);

$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            [
                'orderMeals' => [
                    [
                        "discountPrice"                       => 125,
                        "mealOptionDiscountPrice"             => 0,
                        "mealDiscountPercent"                 => 50,
                        "discountPriceIncludingTax"           => 156.25,
                        "priceIncludingTax"                   => 312.5,
                        "mealOptionDiscountPriceIncludingTax" => 0,
                        "mealOptionPriceIncludingTax"         => 0,
                        "name"                                => "Beef steak",
                        "price"                               => 250,
                        "discountPercent"                     => 50,
                        "quantity"                            => 1,
                        'meal'                                => ['id' => $beefSteakMealId]
                    ],
                    [
                        "discountPrice"                       => 722.5,
                        "mealOptionDiscountPrice"             => 0,
                        "mealDiscountPercent"                 => 15,
                        "discountPriceIncludingTax"           => 903.13,
                        "priceIncludingTax"                   => 1062.5,
                        "mealOptionDiscountPriceIncludingTax" => 0,
                        "mealOptionPriceIncludingTax"         => 0,
                        "name"                                => "Roasted pig",
                        "price"                               => 850,
                        "discountPercent"                     => 0,
                        "quantity"                            => 1,
                        'meal'                                => ['id' => $roastedPigMealId]
                    ],
                    [
                        "discountPrice"                       => 12,
                        "mealOptionDiscountPrice"             => 0,
                        "mealDiscountPercent"                 => 0,
                        "discountPriceIncludingTax"           => 15,
                        "priceIncludingTax"                   => 15,
                        "mealOptionDiscountPriceIncludingTax" => 0,
                        "mealOptionPriceIncludingTax"         => 0,
                        "name"                                => "Cola",
                        "price"                               => 12,
                        "discountPercent"                     => 0,
                        "quantity"                            => 1,
                        'meal'                                => ['id' => $colaMealId]
                    ]
                ],
                "orderStatus"  => ["id" => 1],
                "productTotal" => 1112,
                "discount"     => 295.48,
                "salesTax"     => 204.13,
                "tips"         => 0,
                "orderTotal"   => 1020.65
            ]
        ]
    ]
);
