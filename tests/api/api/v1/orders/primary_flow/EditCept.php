<?php
/**
 * @author samva
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('edit order using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addAnonymousUserFixture();

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$nameSuffix = ' (in rst-edit-order)';

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'api.order.edit-guest.restaurant-email@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'api.order.edit-guest.admin-email@nmotion.pp.ciklum.com'
        ],
        'address'        => [],
        'menuCategories' => [
            [
                'name'      => 'Pizzas' . $nameSuffix,
                'menuMeals' => [
                    [
                        'name'                 => 'Pizza standard' . $nameSuffix,
                        'mealOptions'          => [
                            ['name' => 'Small size' . $nameSuffix, 'price' => 0.5],
                            ['name' => 'Medium size' . $nameSuffix],
                            ['name' => 'Large size' . $nameSuffix]
                        ],
                        'mealExtraIngredients' => [
                            ['name' => 'Olive' . $nameSuffix],
                            ['name' => 'Ketchup' . $nameSuffix],
                            ['name' => 'Parmesan' . $nameSuffix],
                        ]
                    ]
                ]
            ],
            [
                'name'      => 'Drinks' . $nameSuffix,
                'menuMeals' => [
                    [
                        'name'        => 'Leffe brune' . $nameSuffix,
                        'mealOptions' => [
                            ['name' => '0.3' . $nameSuffix],
                            ['name' => '0.5' . $nameSuffix]
                        ]
                    ]
                ]
            ]
        ]
    ]
);

$restaurantId = $restaurant['id'];

$pizzaMeal                   = $restaurant['menuCategories'][0]['menuMeals'][0];
$pizzaMealId                 = $restaurant['menuCategories'][0]['menuMeals'][0]['id'];
$pizzaMealOption             = $restaurant['menuCategories'][0]['menuMeals'][0]['mealOptions'][1];
$pizzaMealOptionId           = $restaurant['menuCategories'][0]['menuMeals'][0]['mealOptions'][1]['id'];
$pizzaMealExtraIngredientId1 = $restaurant['menuCategories'][0]['menuMeals'][0]['mealExtraIngredients'][0]['id'];
$pizzaMealExtraIngredientId2 = $restaurant['menuCategories'][0]['menuMeals'][0]['mealExtraIngredients'][2]['id'];

$beerMealId = $restaurant['menuCategories'][1]['menuMeals'][0]['id'];

$checkin = $I->addRestaurantCheckinFixture(
    [
        'user_id'       => $user['id'],
        'restaurant_id' => $restaurantId,
        'table_number'  => rand(1, 100)
    ]
);

$orderParams = [
    'restaurant_id' => $restaurantId,
    'user_id'       => $user['id'],
    'table_number'  => $checkin['tableNumber'],
    'orderMeals'    => [
        [
            'meal_id'           => $pizzaMealId,
            'meal_option_id'    => $pizzaMealOptionId,
            'meal_option_name'  => $pizzaMealOption['name'],
            'meal_option_price' => $pizzaMealOption['price'],
            'name'              => $pizzaMeal['name'],
            'description'       => $pizzaMeal['description'],
            'price'             => $pizzaMeal['price'],
            'discount_percent'  => $pizzaMeal['discountPercent'],
            'orderMealExtraIngredients' => [
                ['meal_extra_ingredient_id' => $pizzaMealExtraIngredientId1],
                ['meal_extra_ingredient_id' => $pizzaMealExtraIngredientId2]
            ]
        ],
        [
            'meal_id' => $beerMealId
        ]
    ]
];

$order = $I->addOrderFixture($orderParams);

$updateParams = [
    'orderMeals' => [
        [
            'meal'            => $pizzaMealId,
            'name'            => 'asdf',
            'description'     => '',
            'price'           => 200,
            'discountPercent' => 100,
            'mealOption'      => $pizzaMealOptionId,
            'mealOptionName'  => 'Some name',
            'mealOptionPrice' => 10000000,
            'quantity'        => 11,
            'mealComment'     => 'New Comment instead of Some comment to pizza'
        ]
    ],
    'tips'       => 20
];

$I->amGoingTo('send request to the server with order for update');

$I->sendPUT('/api/v1/orders/' . $order['id'] . '.json', $updateParams);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            [
                'orderMeals' => [
                    [
                        'meal'            => ['id' => $pizzaMealId],
                        'mealOption'      => ['id' => $pizzaMealOptionId],
                        'name'            => $pizzaMeal['name'],
                        'description'     => $pizzaMeal['description'],
                        'price'           => $pizzaMeal['price'],
                        'mealOptionPrice' => $pizzaMealOption['price'],
                        'mealOptionName'  => $pizzaMealOption['name'],
                        'discountPercent' => $pizzaMeal['discountPercent'],
                        'mealComment'     => 'New Comment instead of Some comment to pizza',
                        'quantity'        => 11
                    ]
                ],
                'tips'       => 20
            ]
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(1);
$I->seeResponseContainsNumberOfElements('entries.0.orderMeals', 1);
$I->seeResponseContainsNumberOfElements('entries.0.orderMeals.0.orderMealExtraIngredients', 0);
