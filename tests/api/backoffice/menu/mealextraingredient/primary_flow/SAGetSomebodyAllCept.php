<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('get all somebody else\'s extra ingredient for a selected meal through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'bo.rest.menu.extraingredient.pf.getsomebodyall.restaurant-email@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'bo.rest.menu.extraingredient.pf.getsomebodyall.admin-email@nmotion.pp.ciklum.com'
        ],
        'address'        => [],
        'menuCategories' => [
            [
                'name'      => 'Add Cat Meal Extra Ingredient Test',
                'menuMeals' => [
                    [
                        'name'                 => 'Add Meal Extra Ingredient Test',
                        'mealExtraIngredients' => [
                            ['name' => 'Some meal extra ingredient pf 4.1'],
                            ['name' => 'Some meal extra ingredient pf 4.2'],
                            ['name' => 'Some meal extra ingredient pf 4.3'],
                            ['name' => 'Some meal extra ingredient pf 4.4']
                        ]
                    ]
                ]
            ]
        ]
    ]
);

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$I->amGoingTo('get all somebody else\'s meal\'s extra ingredient from the web server');

$mealId = $restaurant['menuCategories'][0]['menuMeals'][0]['id'];

$I->sendGET('/backoffice/meals/' . $mealId . '/extraingredients.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            ['name' => 'Some meal extra ingredient pf 4.1'],
            ['name' => 'Some meal extra ingredient pf 4.2'],
            ['name' => 'Some meal extra ingredient pf 4.3'],
            ['name' => 'Some meal extra ingredient pf 4.4']
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(4);
