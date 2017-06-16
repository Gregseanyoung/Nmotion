<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('failed callback response from payment system - no request params');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'payment.callback.failed1@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'payment.callback.failed1@nmotion.pp.ciklum.com'
        ],
        'visible'        => true,
        'address'        => [],
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

$pizzaMealId = $restaurant['menuCategories'][0]['menuMeals'][0]['id'];

$user    = $I->addAnonymousUserFixture();
$checkin = $I->addRestaurantCheckinFixture(
    [
        'user_id'       => $user['id'],
        'restaurant_id' => $restaurant['id'],
        'table_number'  => rand(1, 100)
    ]
);

$order = $I->addOrderFixture(
    [
        'restaurant_id'   => $restaurant['id'],
        'user_id'         => $user['id'],
        'table_number'    => $checkin['tableNumber'],
        'orderMeals'      => [
            ['meal_id' => $pizzaMealId]
        ],
        'order_status_id' => ORDER_STATUS_PENDING_PAYMENT
    ]
);

$I->amGoingTo('send payment callback response with no params to the backend server');

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$I->sendPOST('/paymentconfirmation/', []);
$I->seeResponseCodeIs(HTTP_RESPONSE_BAD_REQUEST);
