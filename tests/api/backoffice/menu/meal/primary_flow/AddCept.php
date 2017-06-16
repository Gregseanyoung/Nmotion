<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('add new meal for selected menu category for my restaurant through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'rest.menu.meal.add.test@nmotion.pp.ciklum.com',
        'adminUser' => ['email' => 'user.rest.menu.meal.add.test@nmotion.pp.ciklum.com'],
        'address'   => []
    ]
);
$menuCategory = $I->addMenuCategoryFixture(
    [
        'name'          => 'Add Meal Test Successful',
        'restaurant_id' => $restaurant['id']
    ]
);

// successful scenario
$I->amGoingTo('send new meal data to the backend server: successful scenario');
$params1 = [
    "name"        => "Meal1",
    "description" => "Meal1 description",
    "price"       => 50.05,
    "discountPercent" => 0,
    "visible"     => false
];
$I->dontSeeInDatabase('nmtn_meal', ['name' => $params1['name']]);
$I->sendPOST('/backoffice/menucategories/' . $menuCategory['id'] . '/meals.json', $params1);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_meal', ['name' => $params1['name']]);

$I->amGoingTo('check that for each new meal position is incremented');
$params2 = [
    "name"        => "Meal2",
    "description" => "Meal2 description",
    "price"       => 45,
    "discountPercent" => 0,
    "visible"     => true
];
$I->dontSeeInDatabase('nmtn_meal', ['name' => $params2['name']]);
$I->sendPOST('/backoffice/menucategories/' . $menuCategory['id'] . '/meals.json', $params2);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_meal', ['position' => 1, 'name' => $params2['name']]);

$params3 = [
    "name"        => "Meal3",
    "description" => "Meal3 description",
    "price"       => 0,
    "discountPercent" => 0,
    "visible"     => true
];
$I->dontSeeInDatabase('nmtn_meal', ['name' => $params3['name']]);
$I->sendPOST('/backoffice/menucategories/' . $menuCategory['id'] . '/meals.json', $params3);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_meal', ['position' => 2, 'name' => $params3['name']]);

$params4 = [
    "name"        => "Meal4",
    "description" => "Meal4 description",
    "price"       => 1000.99,
    "discountPercent" => 10,
    "visible"     => false
];
$I->dontSeeInDatabase('nmtn_meal', ['name' => $params4['name']]);
$I->sendPOST('/backoffice/menucategories/' . $menuCategory['id'] . '/meals.json', $params4);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_meal', ['position' => 3, 'name' => $params4['name']]);

$I->amGoingTo('check that it is possible to create meal with predefined extra ingredients');
$params5 = [
    'name'                 => 'Meal5',
    'description'          => 'Meal5 description',
    'price'                => 1,
    'discountPercent' => 10,
    'visible'              => true,
    'mealExtraIngredients' => [
        [
            'id'    => '',
            'name'  => 'test',
            'price' => 1
        ]
    ]
];
$I->dontSeeInDatabase('nmtn_meal', ['name' => $params5['name']]);
$I->sendPOST('/backoffice/menucategories/' . $menuCategory['id'] . '/meals.json', $params5);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_meal', ['name' => $params5['name']]);
