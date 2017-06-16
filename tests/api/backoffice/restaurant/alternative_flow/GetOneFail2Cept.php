<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('fail to get restaurant as guest through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');
$I->haveHttpHeader('Auth', 'DeviceToken ' . md5(microtime()));

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.restaurant.getonefail2@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email'      => 'backoffice.restaurant.getonefail2@nmotion.pp.ciklum.com',
        ],
        'address'        => []
    ]
);

// fail - guest
$I->amGoingTo('as guest send get request for one restaurant to the backend server: fail - guest');
$I->sendGET('/backoffice/restaurants/' . $restaurant['id'] . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_FORBIDDEN);
$I->seeResponseContainsJson(['success' => false]);
