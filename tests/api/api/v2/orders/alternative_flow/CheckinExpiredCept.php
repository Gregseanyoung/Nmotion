<?php
/**
 * @author samva
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('fail with expired check-in to edit order using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addAnonymousUserFixture();

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$restaurant = $I->addRestaurantFixture(
    [
        'menuCategories' => [
            [
                'name'      => 'Pizzas',
                'menuMeals' => [
                    ['name' => 'Pizza standard']
                ]
            ]
        ]
    ]
);

$pizzaMeal   = $restaurant['menuCategories'][0]['menuMeals'][0];
$pizzaMealId = $restaurant['menuCategories'][0]['menuMeals'][0]['id'];

$checkin = $I->addRestaurantCheckinFixture(
    [
        'user_id'       => $user['id'],
        'restaurant_id' => $restaurant['id'],
        'table_number'  => rand(1, 100),
        'checked_out'   => true
    ]
);

$orderParams = [
    'restaurant_id' => $restaurant['id'],
    'user_id'       => $user['id'],
    'table_number'  => $checkin['tableNumber'],
    'orderMeals'    => [
        [
            'meal_id'          => $pizzaMealId,
            'name'             => $pizzaMeal['name'],
            'description'      => $pizzaMeal['description'],
            'price'            => $pizzaMeal['price'],
            'discount_percent' => $pizzaMeal['discountPercent'],
        ]
    ]
];

$order = $I->addOrderFixture($orderParams);

$updateParams = [
    'orderMeals' => [
        [
            'meal'            => $pizzaMealId,
            'name'            => $pizzaMeal['name'],
            'description'     => $pizzaMeal['description'],
            'price'           => $pizzaMeal['price'],
            'discountPercent' => $pizzaMeal['discountPercent'],
            'quantity'        => 1
        ]
    ],
    'tips'       => 20
];

$I->amGoingTo('fail send request to the server with order for update');

$I->sendPUT('/api/v2/orders/' . $order['id'] . '.json', $updateParams);
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
$I->seeResponseContainsJson(
    [
        'success' => false,
        'message' => 'Restaurant checkin is expired.'
    ]
);
