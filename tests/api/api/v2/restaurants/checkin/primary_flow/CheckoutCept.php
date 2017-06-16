<?php
/**
 * @author tiger
 * @author seka
 * @author samva <vas@ciklum.com>
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('check-in and check-out within restaurant using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addAnonymousUserFixture();

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$restaurant = $I->addRestaurantFixture(
    [
        'visible'        => 1,
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

$checkin = $I->addRestaurantCheckinFixture(
    [
        'user_id'       => $user['id'],
        'restaurant_id' => $restaurant['id'],
        'table_number'  => rand(1, 100)
    ]
);

$pizzaMealId = $restaurant['menuCategories'][0]['menuMeals'][0]['id'];

$orderParams = [
    'restaurant_id'   => $restaurant['id'],
    'user_id'         => $user['id'],
    'orderMeals'      => [
        ['meal_id' => $pizzaMealId]
    ],
    'table_number'    => $checkin['tableNumber'],
    'order_status_id' => ORDER_STATUS_NEW_ORDER
];

$order = $I->addOrderFixture($orderParams);

$I->amGoingTo('send check-out request to the backend server');

$I->sendPOST('/api/v2/restaurants/' . $restaurant['id'] . '/checkout');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase(
    'nmtn_restaurant_checkin',
    ['restaurant_id' => $restaurant['id'], 'checked_out' => 1]
);
$I->seeInDatabase(
    'nmtn_order',
    ['id' => $order['id'], 'order_status_id' => ORDER_STATUS_CANCELLED]
);
