<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest(
    'failure when adding new menu category for my restaurant through backoffice-API - such menu category for this '
    . 'restaurant already exists'
);
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'rest.menu.category.add.bad2test@nmotion.pp.ciklum.com',
        'adminUser' => ['email' => 'user.rest.menu.category.add.bad2test@nmotion.pp.ciklum.com'],
        'address'   => []
    ]
);

// successful registration
$I->amGoingTo('send new menu category data to the backend server: successful registration');
$params = [
    "name"     => "Bad2Test",
    "timeFrom" => 28800,
    "timeTo"   => 39600,
    "discountPercent" => 0,
    "visible"  => false
];
$I->dontSeeInDatabase('nmtn_menu_category', ['name' => $params['name']]);
$I->sendPOST('/backoffice/restaurants/' . $restaurant['id'] . '/menucategories.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_menu_category', ['name' => $params['name']]);

// validation failed - such menu category for this restaurant already exists
$I->amGoingTo(
    'send new menu category data to the backend server: validation failed - such menu category for this restaurant '
    . 'already exists'
);
$I->sendPOST('/backoffice/restaurants/' . $restaurant['id'] . '/menucategories.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
