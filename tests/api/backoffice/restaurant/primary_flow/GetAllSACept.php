<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('get all restaurants as SA through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$I->addRestaurantFixture(
    [
        'email'          => 'backoffice.restaurant.getallsa@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email'      => 'backoffice.restaurant.getallsa@nmotion.pp.ciklum.com',
        ],
        'address'        => []
    ]
);

// successful scenario
$I->amGoingTo('as SA send get request for all restaurants to the backend server: successful scenario');
$I->sendGET('/backoffice/restaurants.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
