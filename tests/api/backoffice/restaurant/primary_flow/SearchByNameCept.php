<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('get restaurants using name as filter through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$I->addRestaurantFixture(
    [
        'email'          => 'backoffice.restaurant.search-by-name@nmotion.pp.ciklum.com',
        'name'           => 'RESTAURANT VERY SECRET NAME',
        'adminUser'      => ['email' => 'backoffice.restaurant.search-by-name@nmotion.pp.ciklum.com'],
        'address'        => []
    ]
);

$I->addRestaurantFixture(
    [
        'email'          => 'backoffice.restaurant.search-by-name2@nmotion.pp.ciklum.com',
        'name'           => 'Test',
        'adminUser'      => ['email' => 'backoffice.restaurant.search-by-name2@nmotion.pp.ciklum.com'],
        'address'        => []
    ]
);

$I->addRestaurantFixture(
    [
        'email'          => 'backoffice.restaurant.search-by-name3@nmotion.pp.ciklum.com',
        'name'           => 'Common',
        'adminUser'      => ['email' => 'backoffice.restaurant.search-by-name3@nmotion.pp.ciklum.com'],
        'address'        => []
    ]
);

// successful scenario
$I->amGoingTo('send get request for restaurants who contains search query within their name');
$I->sendGET('/backoffice/restaurants.json?filter[0][property]=search&filter[0][value]=VERY SECRET NAME');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            [
                'name' => 'RESTAURANT VERY SECRET NAME'
            ]
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(1);
