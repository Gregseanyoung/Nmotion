<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('get all extra ingredients for a selected meal through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'bo.rest.menu.extraingredient.pf.getownall.restaurant-email@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'bo.rest.menu.extraingredient.pf.getownall.admin-email@nmotion.pp.ciklum.com'
        ],
        'address'        => [],
        'menuCategories' => [
            [
                'name'      => 'Add Cat Meal Extra Ingredient Test',
                'menuMeals' => [
                    [
                        'name'                 => 'Add Meal Extra Ingredient Test',
                        'mealExtraIngredients' => [
                            ['name' => 'Some meal extra ingredient pf 2.1'],
                            ['name' => 'Some meal extra ingredient pf 2.2'],
                            ['name' => 'Some meal extra ingredient pf 2.3']
                        ]
                    ]
                ]
            ]
        ]
    ]
);

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

$I->amGoingTo('get all meal\'s extra ingredients from the web server');

$mealId = $restaurant['menuCategories'][0]['menuMeals'][0]['id'];

$I->sendGET('/backoffice/meals/' . $mealId . '/extraingredients.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            ['name' => 'Some meal extra ingredient pf 2.1'],
            ['name' => 'Some meal extra ingredient pf 2.2'],
            ['name' => 'Some meal extra ingredient pf 2.3']
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(3);
