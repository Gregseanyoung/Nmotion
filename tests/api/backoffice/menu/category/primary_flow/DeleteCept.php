<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('delete menu category for my restaurant through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'rest.menu.category.delete.test@nmotion.pp.ciklum.com',
        'adminUser'      => ['email' => 'user.rest.menu.category.delete.test@nmotion.pp.ciklum.com'],
        'address'        => [],
        'menuCategories' => [
            ['name' => 'Delete Test Successful']
        ]
    ]
);
$menuCategory = $restaurant['menuCategories'][0];

// successful scenario
$I->amGoingTo('send delete request for one menu category to the backend server: successful scenario');
$I->seeInDatabase('nmtn_menu_category', ['id' => $menuCategory['id']]);
$I->sendDELETE('/backoffice/menucategories/' . $menuCategory['id'] . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->dontSeeInDatabase('nmtn_menu_category', ['id' => $menuCategory['id']]);
