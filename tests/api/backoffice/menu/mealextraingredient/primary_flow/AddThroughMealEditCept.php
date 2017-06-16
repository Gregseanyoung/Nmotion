<?php

/** @var $scenario \Codeception\Scenario */
$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('add meal extra ingredient through meal editing through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.meal.extraingredient.add-through-meal-edit@nmotion.com',
        'adminUser'      => ['email' => 'backoffice.meal.extraingredient.add-through-meal-edit@nmotion.com'],
        'address'        => [],
        'menuCategories' => [
            [
                'menuMeals' => [
                    [
                        'mealExtraIngredients' => [
                            ['name' => 'Normal size', 'price' => 2]
                        ]
                    ]
                ]
            ]
        ]
    ]
);
$meal = $restaurant['menuCategories'][0]['menuMeals'][0];
$mealExtraIngredient =& $meal['mealExtraIngredients'][0];

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// adjust meal data to be ready be sent to the server
unset(
    $meal['restaurantId'],
    $meal['menuCategoryId'],
    $meal['logoAssetId'],
    $mealExtraIngredient['mealId']
);

$I->amGoingTo('send meal entity with 2 new extra ingredient');

// remove meal 2nd extra ingredient
$meal['mealExtraIngredients'] = [
    ['name' => 'Small size', 'price' => 1],
    $meal['mealExtraIngredients'][0],
    ['name' => 'Big size', 'price' => 3]
];

// our expectation
$I->expect('2 new extra ingredients to be added to the meal');

// action
$I->sendPUT('/backoffice/meals/' . $meal['id'], $meal);

// check results
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContains('"name":"Small size"');
$I->seeResponseContains('"name":"Normal size"');
$I->seeResponseContains('"name":"Big size"');
