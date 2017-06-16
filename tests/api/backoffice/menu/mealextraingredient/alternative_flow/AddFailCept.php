<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('failure adding new extra ingredient with non-unique name for selected meal through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'rest.meal.extraingredient.add.test.fail@test123456.com',
        'adminUser' => ['email' => 'user.rest.meal.extraingredient.add.test.fail@nmotion.pp.ciklum.com'],
        'address'   => [],
        'menuCategories' => [
            0 => [
                'name' => 'Add Cat Meal Extra Ingredient Test Fail',
                'menuMeals' => [
                    0 => [
                        'name' => 'Add Meal Extra Ingredient Test Fail',
                        'mealExtraIngredients' => [
                            ['name' => 'Add Extra Ingredient Test Fail Name', 'price' => 1.54]
                        ]
                    ]
                ]
            ]
        ]
    ]
);
$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);
$meal = $restaurant['menuCategories'][0]['menuMeals'][0];

// validation failed - such extra ingredient name already exists
$I->amGoingTo('send new meal extra ingredient data with name which already exists for current meal');
$params = [
    'name'                    => 'Add Extra Ingredient Test Fail Name',
    'price'                   => 1.99
];
$I->sendPOST(
    '/backoffice/meals/' . $meal['id'] . '/extraingredient.json',
    $params
);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
