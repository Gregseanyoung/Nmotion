<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('get one restaurant as SA through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.restaurant.getonesa@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email'      => 'backoffice.restaurant.getonesa@nmotion.pp.ciklum.com',
        ],
        'address'        => []
    ]
);

// successful scenario
$I->amGoingTo('as SA send get request for one restaurant to the backend server: successful scenario');
$I->sendGET('/backoffice/restaurants/' . $restaurant['id'] . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsNumberOfEntries(1);
