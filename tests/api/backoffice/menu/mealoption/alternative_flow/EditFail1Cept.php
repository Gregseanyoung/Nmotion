<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('failure when editing meal option through backoffice-API - requested meal option is not exist');

$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.meal.option.editfail1@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email'      => 'backoffice.meal.option.editfail1@nmotion.pp.ciklum.com',
        ],
        'address'        => []
    ]
);

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// editing failed - requested meal option is not exist
$I->amGoingTo('send put request for one meal option to the backend server: fail - requested option not found');
$I->sendPUT('/backoffice/mealoptions/100500.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_NOT_FOUND);
$I->seeResponseContainsJson(['success' => false]);
