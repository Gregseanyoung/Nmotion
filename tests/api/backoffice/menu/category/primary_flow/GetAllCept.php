<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('get all menu categories for my restaurant through backoffice-API');

$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'rest.menu.category.getall.test@nmotion.pp.ciklum.com',
        'adminUser' => ['email' => 'user.rest.menu.category.getall.test@nmotion.pp.ciklum.com'],
        'address'   => []
    ]
);
$menuCategory1 = $I->addMenuCategoryFixture(
    [
        'restaurant_id' => $restaurant['id']
    ]
);
$menuCategory2 = $I->addMenuCategoryFixture(
    [
        'name'          => 'Test Menu Category 2',
        'restaurant_id' => $restaurant['id'],
        'position'      => 2
    ]
);

// successful scenario
$I->amGoingTo('send get request for menu categories data to the backend server: successful scenario');
$I->sendGET('/backoffice/restaurants/' . $restaurant['id'] . '/menucategories.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsJson(
    [
        'entries' => [
            ['id' => $menuCategory1['id']],
            ['id' => $menuCategory2['id']]
        ]
    ]
);
