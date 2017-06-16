<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest(
    'failure when adding new meal for selected menu category for my restaurant through backoffice-API - incorrect data'
);
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'rest.menu.meal.add.test.fail4@test123456.com',
        'adminUser' => ['email' => 'user.rest.menu.meal.add.test.fail4@test123456.com'],
        'address'   => []
    ]
);
$menuCategory = $I->addMenuCategoryFixture(
    [
        'name' => 'Add Meal Test Fail4',
        'restaurant_id' => $restaurant['id']
    ]
);

// validation failed - incorrect data
$I->amGoingTo('send new meal data to the backend server: validation failed - incorrect data');
$params = [
    "name"        => "Incorrect",
    "description" => "",
    "price"       => "incorrect",
    "visible"     => false
];
$I->sendPOST('/backoffice/menucategories/' . $menuCategory['id'] . '/meals.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
