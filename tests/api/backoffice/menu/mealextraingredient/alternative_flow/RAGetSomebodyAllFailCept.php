<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin of other Restaurant');
$I->wantToTest('failure when get all extra ingredients for a selected meal through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$otherRestaurantAdmin = $I->addUserFixture(
    [
        'email' => 'bo.rest.menu.extraingredient.af.getsomebodyallfail.otheradmin-email@nmotion.pp.ciklum.com',
        'roles' => [
            'ROLE_RESTAURANT_GUEST',
            'ROLE_RESTAURANT_STAFF',
            'ROLE_RESTAURANT_ADMIN'
        ]
    ]
);

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'bo.rest.menu.extraingredient.af.getownallfail.restaurant-email@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'bo.rest.menu.extraingredient.af.getownallfail.admin-email@nmotion.pp.ciklum.com'
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

$I->willEvaluateAuthorizationToken($otherRestaurantAdmin['email'], $otherRestaurantAdmin['password']);

$I->amGoingTo('get all meal\'s extra ingredients from the web server');

$mealId = $restaurant['menuCategories'][0]['menuMeals'][0]['id'];

$I->sendGET('/backoffice/meals/' . $mealId . '/extraingredients.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_FORBIDDEN);
