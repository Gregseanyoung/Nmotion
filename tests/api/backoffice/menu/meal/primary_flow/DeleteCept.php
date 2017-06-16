<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('delete meal for given menu category for my restaurant through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.restaurant.menu.meal.delete@nmotion.com',
        'adminUser'      => [
            'email'      => 'backoffice.restaurant.menu.meal.delete@nmotion.com',
            'password'   => 'test1234',
            'roles'      => ['ROLE_RESTAURANT_ADMIN'],
        ],
        'address'        => [],
        'menuCategories' => [
            0 => [
                'name'      => 'backoffice.restaurant.menu.meal.delete@nmotion.com',
                'menuMeals' => [
                    0 => ['name' => 'Green Dragon roll', 'position' => 0],
                    1 => ['name' => 'Red Dragon roll', 'position' => 1],
                    2 => ['name' => 'California roll', 'position' => 2]
                ]
            ]
        ]
    ]
);
$mealId = $restaurant['menuCategories'][0]['menuMeals'][1]['id'];

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// successful scenario
$I->amGoingTo('send delete request for one meal to the backend server: successful scenario');
$I->seeInDatabase('nmtn_meal', ['id' => $mealId]);
$I->sendDELETE('/backoffice/meals/' . $mealId . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->dontSeeInDatabase('nmtn_meal', ['id' => $mealId]);

$I->amGoingTo('check that after deleting meal other meals positions are appropriately reordered');
$I->seeInDatabase('nmtn_meal', ['name' => 'Green Dragon roll', 'position' => 0]);
$I->seeInDatabase('nmtn_meal', ['name' => 'California roll', 'position' => 1]);
