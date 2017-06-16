<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('failure when adding new extra ingredient for selected meal through backoffice-API - incorrect data');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'rest.meal.extraingredient.add.test.fail1@test123456.com',
        'adminUser' => ['email' => 'user.rest.meal.extraingredient.add.test.fail1@nmotion.pp.ciklum.com'],
        'address'   => [],
        'menuCategories' => [
            0 => [
                'name' => 'Add Cat Meal Extra Ingredient Test Fail1',
                'menuMeals' => [
                    0 => ['name' => 'Add Meal Extra Ingredient Test Fail1']
                ]
            ]
        ]
    ]
);
$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);
$meal = $restaurant['menuCategories'][0]['menuMeals'][0];

// validation failed - such extra ingredient name already exists
$I->amGoingTo('send new meal extra ingredient data with invalid parameters');
$params = [
    'name'                    => '',
    'price'                   => "invalid"
];
$I->sendPOST(
    '/backoffice/meals/' . $meal['id'] . '/extraingredient.json',
    $params
);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
