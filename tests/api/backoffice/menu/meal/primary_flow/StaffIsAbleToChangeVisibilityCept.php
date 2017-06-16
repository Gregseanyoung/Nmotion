<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Staff');
$I->wantToTest('possibility to change visibility status for existing meal through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'visible'        => true,
        'menuCategories' => [
            [
                'name'      => 'Rolls',
                'visible'   => true,
                'menuMeals' => [
                    ['name' => 'Green Dragon roll', 'position' => 0, 'visible' => true],
                    ['name' => 'Red Dragon roll', 'position' => 1, 'visible' => true],
                    ['name' => 'California roll', 'position' => 2, 'visible' => true]
                ]
            ]
        ]
    ]
);
$staff = $I->addRestaurantStaffFixture($restaurant['id']);

$I->willEvaluateAuthorizationToken($staff['email'], $staff['password']);

$menuMeals = $restaurant['menuCategories'][0]['menuMeals'];
$menuMeals[1]['visible'] = false;
unset($menuMeals[1]['restaurantId'], $menuMeals[1]['menuCategoryId'], $menuMeals[1]['logoAssetId']);

$I->amGoingTo('send request to the server to change visibility for meal');
$I->sendPUT('/backoffice/meals/' . $menuMeals[1]['id'], $menuMeals[1]);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_meal', ['id' => $menuMeals[0]['id'], 'visible' => true]);
$I->seeInDatabase('nmtn_meal', ['id' => $menuMeals[1]['id'], 'visible' => false]);
$I->seeInDatabase('nmtn_meal', ['id' => $menuMeals[2]['id'], 'visible' => true]);
