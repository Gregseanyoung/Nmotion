<?php
/**
 * @author seka
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('edit order using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addAnonymousUserFixture();

$nameSuffix = ' (in rst-edit-order)';

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

$pizzaMeal                   = $restaurant['menuCategories'][0]['menuMeals'][0];
$pizzaMealId                 = $restaurant['menuCategories'][0]['menuMeals'][0]['id'];
$pizzaMealOptionId           = $restaurant['menuCategories'][0]['menuMeals'][0]['mealOptions'][1]['id'];
$pizzaMealExtraIngredientId1 = $restaurant['menuCategories'][0]['menuMeals'][0]['mealExtraIngredients'][0]['id'];
$pizzaMealExtraIngredientId2 = $restaurant['menuCategories'][0]['menuMeals'][0]['mealExtraIngredients'][2]['id'];

$beerMealId = $restaurant['menuCategories'][1]['menuMeals'][0]['id'];

$masterStatusesThatDoNotAllowEditSlaveOrder = [
    ORDER_STATUS_PENDING_PAYMENT,
    ORDER_STATUS_PAID,
    ORDER_STATUS_SENT_TO_PRINTER
];

foreach ($masterStatusesThatDoNotAllowEditSlaveOrder as $status) {

    $slaveUser = $I->addAnonymousUserFixture();
    $I->haveHttpHeader('Auth', 'DeviceToken ' . $slaveUser['deviceIdentity']);

    $checkin = $I->addRestaurantCheckinFixture(
        [
            'user_id'       => $slaveUser['id'],
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
        ],
        'order_status_id' => $status
    ];
    $order = $I->addOrderFixture($orderParams);

    $slaveOrderParams = [
        'orderMeals'      => [
            [
                'meal_id'                   => $pizzaMealId,
                'quantity'                  => 1,
                'meal_comment'              => 'Some comment to pizza'
            ]
        ],
        'table_number'    => $checkin['tableNumber'],
        'master_id' => $order['id'],
        'restaurant_id' => $restaurantId,
        'user_id' => $slaveUser['id']
    ];
    $slaveOrder = $I->addOrderFixture($slaveOrderParams);

    $updateParams = [
        'orderMeals' => [
            [
                'meal'            => $pizzaMealId,
                'name'            => $pizzaMeal['name'],
                'description'     => $pizzaMeal['description'],
                'price'           => $pizzaMeal['price'],
                'discountPercent' => $pizzaMeal['discountPercent'],
                'mealOption'      => $pizzaMealOptionId,
                'quantity'        => 11,
                'mealComment'     => 'New Comment instead of Some comment to pizza'
            ]
        ],
        'tips'       => 20
    ];

    $I->amGoingTo('send request to the server with order for update');

    $I->sendPUT('/api/v2/orders/' . $slaveOrder['id'] . '.json', $updateParams);
    $I->seeResponseIsJson();
    $I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
    switch ($status) {
        case ORDER_STATUS_PENDING_PAYMENT:
            $I->seeResponseContainsJson(['success' => false, 'exception_code' => ORDER_EXCEPTION_PAYING_BY_OTHER]);
            break;
        case ORDER_STATUS_PAID:
        case ORDER_STATUS_SENT_TO_PRINTER:
            $I->seeResponseContainsJson(['success' => false, 'exception_code' => ORDER_EXCEPTION_PAID_BY_OTHER]);
            break;
        default:
            throw new \Exception('Unknown status in the current test');
    }
}
