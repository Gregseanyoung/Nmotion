<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest(
    'failure when adding new menu category for my restaurant through backoffice-API - requested restaurant is not exist'
);
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'rest.menu.category.add.bad1test@nmotion.pp.ciklum.com',
        'adminUser' => ['email' => 'user.rest.menu.category.add.bad1test@nmotion.pp.ciklum.com'],
        'address'   => []
    ]
);

// registration failed - requested restaurant is not exist
$I->amGoingTo(
    'send new menu category data to the backend server: registration failed - requested restaurant is not exist'
);
$I->sendPOST('/backoffice/restaurants/100500/menucategories.json', []);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_NOT_FOUND);
