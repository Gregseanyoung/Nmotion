<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('check in to takeaway restaurant using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'visible'        => 1,
        'in_house'       => 0,
        'takeaway'       => 1,
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

$tableNumber = 'takeaway';

$user1 = $I->addAnonymousUserFixture();
$user2 = $I->addAnonymousUserFixture();

$I->amGoingTo('check in to the takeaway restaurant by user1');

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user1['deviceIdentity']);

$I->sendPOST(
    '/api/v2/restaurants/' . $restaurant['id'] . '/checkin',
    ['serviceType' => RESTAURANT_SERVICE_TYPE_TAKEAWAY, 'table' => $tableNumber]
);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase(
    'nmtn_restaurant_checkin',
    [
        'restaurant_id' => $restaurant['id'],
        'user_id'       => $user1['id'],
        'service_type_id'   => RESTAURANT_SERVICE_TYPE_TAKEAWAY,
        'checked_out'   => false
    ]
);

$I->amGoingTo('check in to the same takeaway restaurant by user2');

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user2['deviceIdentity']);

$I->sendPOST(
    '/api/v2/restaurants/' . $restaurant['id'] . '/checkin',
    ['serviceType' => RESTAURANT_SERVICE_TYPE_TAKEAWAY, 'table' => $tableNumber]
);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase(
    'nmtn_restaurant_checkin',
    [
        'restaurant_id' => $restaurant['id'],
        'user_id'       => $user2['id'],
        'service_type_id'   => RESTAURANT_SERVICE_TYPE_TAKEAWAY,
        'checked_out'   => false
    ]
);
