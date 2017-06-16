<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('failure when adding new meal option through backoffice-API - requested meal is not exist');

$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.meal.option.addfail1@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email'      => 'backoffice.meal.option.addfail1@nmotion.pp.ciklum.com',
        ],
        'address'        => []
    ]
);

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// adding failed - requested meal is not exist
$I->amGoingTo('send new meal option data to the backend server: adding failed - requested meal is not exist');
$I->sendPOST('/backoffice/meals/100500/options.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_NOT_FOUND);
