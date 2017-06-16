<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('check in to in house / room service restaurant using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'visible'        => 1,
        'in_house'       => 1,
        'takeaway'       => 0,
        'room_service'   => 1,
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

$tableNumber = 'in house / room service';

$user1 = $I->addAnonymousUserFixture();
$user2 = $I->addAnonymousUserFixture();
$user3 = $I->addAnonymousUserFixture();

$I->amGoingTo('check in to the restaurant with in house service type by user1');

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user1['deviceIdentity']);

$I->sendPOST(
    '/api/v2/restaurants/' . $restaurant['id'] . '/checkin',
    ['serviceType' => RESTAURANT_SERVICE_TYPE_IN_HOUSE, 'table' => $tableNumber]
);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase(
    'nmtn_restaurant_checkin',
    [
        'restaurant_id'   => $restaurant['id'],
        'user_id'         => $user1['id'],
        'service_type_id' => RESTAURANT_SERVICE_TYPE_IN_HOUSE,
        'table_number'    => $tableNumber,
        'checked_out'     => false
    ]
);


$I->amGoingTo('check in to the same restaurant and table with room service by user2');

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user2['deviceIdentity']);

$I->sendPOST(
    '/api/v2/restaurants/' . $restaurant['id'] . '/checkin',
    ['serviceType' => RESTAURANT_SERVICE_TYPE_ROOM_SERVICE, 'table' => $tableNumber]
);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase(
    'nmtn_restaurant_checkin',
    [
        'restaurant_id'   => $restaurant['id'],
        'user_id'         => $user2['id'],
        'service_type_id' => RESTAURANT_SERVICE_TYPE_ROOM_SERVICE,
        'table_number'    => $tableNumber,
        'checked_out'     => false
    ]
);


$I->amGoingTo('check in to the same restaurant and table with room service by user3');

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user3['deviceIdentity']);

$I->sendPOST(
    '/api/v2/restaurants/' . $restaurant['id'] . '/checkin',
    ['serviceType' => RESTAURANT_SERVICE_TYPE_ROOM_SERVICE, 'table' => $tableNumber]
);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CONFLICT);
$I->seeResponseContainsJson(
    [
        'success' => false,
        'message' => 'Table ' . $tableNumber . ' is not empty. Join them? Or change table number.'
    ]
);
