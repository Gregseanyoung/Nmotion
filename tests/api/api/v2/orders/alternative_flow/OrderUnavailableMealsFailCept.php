<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('fail ordering unavailable meal using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addAnonymousUserFixture();

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$restaurant = $I->addRestaurantFixture(
    [
        'name' => 'api.v2.order.unavailable.meal',
        'menuCategories' => [
            0 => [
                'name'      => 'Available category',
                'visible'   => 1,
                'time_from' => 0,
                'time_to'   => 86399,
                'menuMeals' => [
                    0 => [
                        'name'    => 'Meal available for ordering',
                        'visible' => 1,
                        'time_from' => 0,
                        'time_to'   => 86399
                    ],
                    1 => [
                        'name'    => 'Hidden meal',
                        'visible' => 0
                    ],
                    2 => [
                        'name'    => 'Meal with unavailable timeframe',
                        'visible' => 1,
                        'time_from' => 86398,
                        'time_to'   => 86399
                    ]
                ]
            ],
            1 => [
                'name'      => 'Hidden category',
                'visible'   => 0,
                'menuMeals' => [
                    0 => [
                        'name'    => 'Meal with hidden category',
                        'visible' => 1
                    ]
                ]
            ],
            2 => [
                'name'      => 'Category with unavailable timeframe',
                'visible'   => 1,
                'time_from' => 86398,
                'time_to'   => 86399,
                'menuMeals' => [
                    0 => [
                        'name'    => 'Meal with category with unavailable timeframe',
                        'visible' => 1,
                        'time_from' => 0,
                        'time_to'   => 86399
                    ]
                ]
            ]
        ]
    ]
);
$restaurantId = $restaurant['id'];

$mealAvailableForOrderingId     = $restaurant['menuCategories'][0]['menuMeals'][0]['id'];
$hiddenMealId                   = $restaurant['menuCategories'][0]['menuMeals'][1]['id'];
$mealWithUnavailableTimeframeId = $restaurant['menuCategories'][0]['menuMeals'][2]['id'];
$hiddenCategoryMealId           = $restaurant['menuCategories'][1]['menuMeals'][0]['id'];
$mealWithCategoryWithUnavailableTimeframeId = $restaurant['menuCategories'][2]['menuMeals'][0]['id'];

$I->addRestaurantCheckinFixture(
    [
        'user_id'       => $user['id'],
        'restaurant_id' => $restaurantId,
        'table_number'  => rand(1, 100)
    ]
);

$I->amGoingTo('order available meal');
$params1 = [
    'orderMeals' => [
        [
            'meal'     => $mealAvailableForOrderingId,
            'quantity' => 1
        ]
    ]
];
$I->sendPOST('/api/v2/restaurants/' . $restaurantId . '/orders', $params1);
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);

$I->amGoingTo('order hidden meal');
$params2 = [
    'orderMeals' => [
        [
            'meal'     => $hiddenMealId,
            'quantity' => 1
        ]
    ]
];
$I->sendPOST('/api/v2/restaurants/' . $restaurantId . '/orders', $params2);
$I->seeResponseCodeIs(HTTP_RESPONSE_CONFLICT);
$I->seeResponseContainsJson(
    [
        'exception_code' => MEAL_NOT_AVAILABLE_MEAL_NOT_VISIBLE,
        'message'        => $hiddenMealId
    ]
);

$I->amGoingTo('order meal with unavailable timeframe');
$params3 = [
    'orderMeals' => [
        [
            'meal'     => $mealWithUnavailableTimeframeId,
            'quantity' => 1
        ]
    ]
];
$I->sendPOST('/api/v2/restaurants/' . $restaurantId . '/orders', $params3);
$I->seeResponseCodeIs(HTTP_RESPONSE_CONFLICT);
$I->seeResponseContainsJson(
    [
        'exception_code' => MEAL_NOT_AVAILABLE_MEAL_TIME_UNAVAILABLE,
        'message'        => $mealWithUnavailableTimeframeId
    ]
);

$I->amGoingTo('order meal with hidden category');
$params4 = [
    'orderMeals' => [
        [
            'meal'     => $hiddenCategoryMealId,
            'quantity' => 1
        ]
    ]
];
$I->sendPOST('/api/v2/restaurants/' . $restaurantId . '/orders', $params4);
$I->seeResponseCodeIs(HTTP_RESPONSE_CONFLICT);
$I->seeResponseContainsJson(
    [
        'exception_code' => MEAL_NOT_AVAILABLE_CATEGORY_NOT_VISIBLE,
        'message'        => $hiddenCategoryMealId
    ]
);

$I->amGoingTo('order meal with category with unavailable timeframe');
$params5 = [
    'orderMeals' => [
        [
            'meal'     => $mealWithCategoryWithUnavailableTimeframeId,
            'quantity' => 1
        ]
    ]
];
$I->sendPOST('/api/v2/restaurants/' . $restaurantId . '/orders', $params5);
$I->seeResponseCodeIs(HTTP_RESPONSE_CONFLICT);
$I->seeResponseContainsJson(
    [
        'exception_code' => MEAL_NOT_AVAILABLE_CATEGORY_TIME_UNAVAILABLE,
        'message'        => $mealWithCategoryWithUnavailableTimeframeId
    ]
);
