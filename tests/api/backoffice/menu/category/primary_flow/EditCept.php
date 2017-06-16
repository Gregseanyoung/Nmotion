<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('add new menu category for my restaurant through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'rest.menu.category.edit.test@nmotion.pp.ciklum.com',
        'adminUser' => ['email' => 'user.rest.menu.category.edit.test@nmotion.pp.ciklum.com'],
        'address'   => []
    ]
);

$I->addMenuCategoryFixture(
    [
        'name'          => 'Category0Test',
        'timeFrom'      => 28800,
        'timeTo'        => 39600,
        'visible'       => true,
        'restaurant_id' => $restaurant['id'],
        'position'      => 0
    ]
);
$menuCategory = $I->addMenuCategoryFixture(
    [
        'name'          => 'Category1Test',
        'timeFrom'      => 28800,
        'timeTo'        => 39600,
        'visible'       => true,
        'restaurant_id' => $restaurant['id'],
        'position'      => 1
    ]
);
$I->addMenuCategoryFixture(
    [
        'name'          => 'Category2Test',
        'timeFrom'      => 28800,
        'timeTo'        => 39600,
        'visible'       => true,
        'restaurant_id' => $restaurant['id'],
        'position'      => 2
    ]
);
$I->addMenuCategoryFixture(
    [
        'name'          => 'Category3Test',
        'timeFrom'      => 28800,
        'timeTo'        => 39600,
        'visible'       => true,
        'restaurant_id' => $restaurant['id'],
        'position'      => 3
    ]
);

$I->amGoingTo(
    'check that after editing menu category position other menu categories positions are appropriately reordered'
);

$params = [
    'name'     => 'Category1Test',
    'timeFrom' => 28800,
    'timeTo'   => 39600,
    'visible'  => true,
    'position' => 3
];
$I->sendPUT('/backoffice/menucategories/' . $menuCategory['id'] . '.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_menu_category', ['name' => $menuCategory['name'], 'position' => 3]);
$I->seeInDatabase('nmtn_menu_category', ['name' => 'Category3Test', 'position' => 2]);
$I->seeInDatabase('nmtn_menu_category', ['name' => 'Category2Test', 'position' => 1]);
