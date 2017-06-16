<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('failure when deleting meal option through backoffice-API - requested meal option is not exist');

$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.meal.option.deletefail1@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email'      => 'backoffice.meal.option.deletefail1@nmotion.pp.ciklum.com',
        ],
        'address'        => []
    ]
);

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// deleting failed - requested meal option is not exist
$I->amGoingTo('send delete request for one meal option to the backend server: fail - requested option not found');
$I->sendDELETE('/backoffice/mealoptions/100500.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_NOT_FOUND);
$I->seeResponseContainsJson(['success' => false]);
