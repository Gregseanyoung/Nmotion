<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('get one menu category for my restaurant through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'rest.menu.category.getone.test@nmotion.pp.ciklum.com',
        'adminUser' => ['email' => 'user.rest.menu.category.getone.test@nmotion.pp.ciklum.com'],
        'address'   => [],
        'menuCategories' => [
            ['name' => 'Test Category']
        ]
    ]
);
$menuCategory = $restaurant['menuCategories'][0];

// successful scenario
$I->amGoingTo('send get request for one menu category data to the backend server: successful scenario');
$I->seeInDatabase('nmtn_menu_category', ['id' => $menuCategory['id']]);
$I->sendGET('/backoffice/menucategories/' . $menuCategory['id'] . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsJson(['entries' => [['id' => $menuCategory['id']]]]);
$I->seeResponseContainsNumberOfEntries(1);
