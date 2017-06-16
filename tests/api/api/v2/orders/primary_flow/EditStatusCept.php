<?php
/**
 * @author samva
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('change order status using API with patch');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addAnonymousUserFixture();

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$nameSuffix = ' (in rst-patch-order)';

$restaurant = $I->addRestaurantFixture(
    [
        'menuCategories' => [
            [
                'name'      => 'Pizzas' . $nameSuffix,
                'menuMeals' => [
                    [
                        'name'                 => 'Pizza standard' . $nameSuffix,
                        'mealOptions'          => [
                            ['name' => 'Small size' . $nameSuffix],
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

$pizzaMealId                 = $restaurant['menuCategories'][0]['menuMeals'][0]['id'];
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
            'meal_id'                   => $pizzaMealId,
            'meal_option_id'            => $pizzaMealOptionId,
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

$I->amGoingTo('send request to the server with order status for partial update');
$I->sendPATCH('/api/v2/orders/' . $order['id'] . '.json', ['status' => ORDER_STATUS_PENDING_PAYMENT]);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            [
                'orderStatus' => ['id' => ORDER_STATUS_PENDING_PAYMENT]
            ]
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(1);
