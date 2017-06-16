<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('get one somebody else\'s extra ingredient for a selected meal through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'bo.rest.menu.extraingredient.pf.getsomebodyone.restaurant-email@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'bo.rest.menu.extraingredient.pf.getsomebodyone.admin-email@nmotion.pp.ciklum.com'
        ],
        'address'        => [],
        'menuCategories' => [
            [
                'name'      => 'Add Cat Meal Extra Ingredient Test',
                'menuMeals' => [
                    [
                        'name'                 => 'Add Meal Extra Ingredient Test',
                        'mealExtraIngredients' => [
                            ['name' => 'Some meal extra ingredient pf 3.1'],
                            ['name' => 'Some meal extra ingredient pf 3.2'],
                        ]
                    ]
                ]
            ]
        ]
    ]
);

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$I->amGoingTo('get one somebody else\'s meal\'s extra ingredient from the web server');

$extraIngredientId = $restaurant['menuCategories'][0]['menuMeals'][0]['mealExtraIngredients'][1]['id'];

$I->sendGET('/backoffice/mealextraingredients/' . $extraIngredientId . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            ['id' => $extraIngredientId, 'name' => 'Some meal extra ingredient pf 3.2']
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(1);
