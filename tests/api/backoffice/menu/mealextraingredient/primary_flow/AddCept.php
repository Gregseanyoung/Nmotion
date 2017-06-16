<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('add new meal extra ingredient for selected meal through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'bo.restaurant.menu.mealextraingredient.add.restaurant.nmotion@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'bo.restaurant.menu.mealextraingredient.add.admin.nmotion@nmotion.pp.ciklum.com'
        ],
        'address'        => [],
        'menuCategories' => [
            0 => [
                'name'      => 'Add Cat Meal Extra Ingredient Test',
                'menuMeals' => [
                    0 => ['name' => 'Add Meal Extra Ingredient Test']
                ]
            ]
        ]
    ]
);

// successful scenario
$I->amGoingTo('post new meal extra ingredient to server');
$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

$mealId = $restaurant['menuCategories'][0]['menuMeals'][0]['id'];
$params = [
    'name'  => 'Add Extra Ingredient Test',
    'price' => 1.99
];
$I->dontSeeInDatabase('nmtn_meal_extra_ingredient', ['name' => $params['name']]);
$I->sendPOST('/backoffice/meals/' . $mealId . '/extraingredient.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_meal_extra_ingredient', ['name' => $params['name']]);
