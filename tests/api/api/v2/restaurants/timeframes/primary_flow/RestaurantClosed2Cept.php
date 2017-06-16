<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('if restaurant is closed when operation time set to NULL - NULL using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'visible'        => 1,
        'operationTimes' => [
            ['day_of_the_week' => 1, 'time_from' => null, 'time_to' => null],
            ['day_of_the_week' => 2, 'time_from' => null, 'time_to' => null],
            ['day_of_the_week' => 3, 'time_from' => null, 'time_to' => null],
            ['day_of_the_week' => 4, 'time_from' => null, 'time_to' => null],
            ['day_of_the_week' => 5, 'time_from' => null, 'time_to' => null],
            ['day_of_the_week' => 6, 'time_from' => null, 'time_to' => null],
            ['day_of_the_week' => 7, 'time_from' => null, 'time_to' => null]
        ]
    ]
);

$user = $I->addAnonymousUserFixture();
$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$I->amGoingTo('check if restaurant is closed');

$I->sendGET('/api/v2/restaurants/' . $restaurant['id']);

$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            ['isOpen' => false]
        ]
    ]
);
