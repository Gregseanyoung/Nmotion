<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('get one extra ingredient for a selected meal through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'bo.rest.menu.extraingredient.pf.getownone.restaurant-email@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'bo.rest.menu.extraingredient.pf.getownone.admin-email@nmotion.pp.ciklum.com'
        ],
        'address'        => [],
        'menuCategories' => [
            [
                'name'      => 'Add Cat Meal Extra Ingredient Test',
                'menuMeals' => [
                    [
                        'name'                 => 'Add Meal Extra Ingredient Test',
                        'mealExtraIngredients' => [
                            ['name' => 'Some meal extra ingredient pf 1.1'],
                            ['name' => 'Some meal extra ingredient pf 1.2'],
                        ]
                    ]
                ]
            ]
        ]
    ]
);

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

$I->amGoingTo('get one meal\'s extra ingredient from the web server');

$extraIngredientId = $restaurant['menuCategories'][0]['menuMeals'][0]['mealExtraIngredients'][1]['id'];

$I->sendGET('/backoffice/mealextraingredients/' . $extraIngredientId . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            ['id' => $extraIngredientId, 'name' => 'Some meal extra ingredient pf 1.2']
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(1);
