<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest(
    'failure when getting menu categories for my restaurant through backoffice-API'
    . ' - requested restaurant is not exist'
);
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'rest.menu.category.getallfail1.test@nmotion.pp.ciklum.com',
        'adminUser'      => ['email' => 'user.rest.menu.category.getallfail1.test@nmotion.pp.ciklum.com'],
        'address'        => [],
        'menuCategories' => [
            ['name' => 'Delete Test Successful']
        ]
    ]
);

// get failed - requested restaurant is not exist
$I->amGoingTo('send get request for menu categories data to the backend server: requested restaurant is not exist');
$I->sendGET('/backoffice/restaurants/100500/menucategories.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_NOT_FOUND);
$I->seeResponseContainsJson(['success' => false]);
