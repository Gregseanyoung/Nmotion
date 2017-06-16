<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('add new menu category for my restaurant through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$user = $I->addUserFixture(
    ['email' => 'user.rest.menu.category.add.test@nmotion.pp.ciklum.com']
);
$address = $I->addRestaurantAddressFixture();
$restaurant = $I->addRestaurantFixture(
    [
        'email'                 => 'rest.menu.category.add.test@nmotion.pp.ciklum.com',
        'admin_user_id'         => $user['id'],
        'restaurant_address_id' => $address['id']
    ]
);

// successful registration
$I->amGoingTo('send new menu category data to the backend server: successful registration');
$params = [
    "name"     => "BreakfastTest",
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

$I->amGoingTo('check that for each new menu category position is incremented');
$params = [
    'name'     => 'Category1Test',
    'timeFrom' => 28800,
    'timeTo'   => 39600,
    'discountPercent' => 0,
    'visible'  => true
];
$I->dontSeeInDatabase('nmtn_menu_category', ['name' => $params['name']]);
$I->sendPOST('/backoffice/restaurants/' . $restaurant['id'] . '/menucategories.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_menu_category', ['position' => 1, 'name' => $params['name']]);

$params = [
    'name'     => 'Category2Test',
    'timeFrom' => 28800,
    'timeTo'   => 39600,
    'discountPercent' => 0,
    'visible'  => true
];
$I->dontSeeInDatabase('nmtn_menu_category', ['name' => $params['name']]);
$I->sendPOST('/backoffice/restaurants/' . $restaurant['id'] . '/menucategories.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_menu_category', ['position' => 2, 'name' => $params['name']]);

$params = [
    'name'     => 'Category3Test',
    'timeFrom' => 28800,
    'timeTo'   => 39600,
    'discountPercent' => 0,
    'visible'  => true
];
$I->dontSeeInDatabase('nmtn_menu_category', ['name' => $params['name']]);
$I->sendPOST('/backoffice/restaurants/' . $restaurant['id'] . '/menucategories.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_menu_category', ['position' => 3, 'name' => $params['name']]);
