<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('fail check in to restaurant with unsupported room service using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'visible'        => 1,
        'in_house'       => 1,
        'takeaway'       => 0,
        'room_service'   => 0,
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

$tableNumber = rand(1, 100);

$user = $I->addAnonymousUserFixture();

$I->amGoingTo('fail to check in to the restaurant with room service type');

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$I->sendPOST(
    '/api/v2/restaurants/' . $restaurant['id'] . '/checkin',
    ['serviceType' => RESTAURANT_SERVICE_TYPE_ROOM_SERVICE, 'table' => $tableNumber]
);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
