<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('get one restaurant as RA through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.restaurant.getonera@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email'      => 'backoffice.restaurant.getonera@nmotion.pp.ciklum.com',
        ],
        'address'        => []
    ]
);

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// successful scenario
$I->amGoingTo('as RA send get request for one restaurant to the backend server: successful scenario');
$I->sendGET('/backoffice/restaurants/' . $restaurant['id'] . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsNumberOfEntries(1);
