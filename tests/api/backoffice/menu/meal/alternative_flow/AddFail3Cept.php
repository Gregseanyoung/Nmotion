<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest(
    'failure when adding new meal for selected menu category for my restaurant through backoffice-API'
    . ' - such meal name is already exist for given restaurant'
);
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'rest.menu.meal.add.test.fail3@test123456.com',
        'adminUser' => ['email' => 'user.rest.menu.meal.add.test.fail3@nmotion.pp.ciklum.com'],
        'address'   => []
    ]
);
$menuCategory1 = $I->addMenuCategoryFixture(
    [
        'name'          => 'Add Cat Test Fail3-1',
        'restaurant_id' => $restaurant['id']
    ]
);
$meal = $I->addMealFixture(
    [
        'name'             => 'Add Meal Test Fail3',
        'restaurant_id'    => $restaurant['id'],
        'menu_category_id' => $menuCategory1['id']
    ]
);
$menuCategory2 = $I->addMenuCategoryFixture(
    [
        'name'          => 'Add Cat Test Fail3-2',
        'restaurant_id' => $restaurant['id']
    ]
);

$params = [
    "name"            => $meal['name'],
    "description"     => "Meal description",
    "price"           => 50.05,
    "discountPercent" => 0,
    "visible"         => false
];

// validation success - such meal name is already exist for given restaurant, but in the other category
$I->amGoingTo(
    'send new meal data to the backend server: adding success - such meal name is already exist for given restaurant'
    . ', but in the other category'
);
$I->sendPOST('/backoffice/menucategories/' . $menuCategory2['id'] . '/meals.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);

// validation failed - such meal name is already exist for given restaurant's category
$I->amGoingTo(
    'send new meal data to the backend server: adding failed - such meal name is already exist for given category'
);
$I->sendPOST('/backoffice/menucategories/' . $menuCategory2['id'] . '/meals.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
