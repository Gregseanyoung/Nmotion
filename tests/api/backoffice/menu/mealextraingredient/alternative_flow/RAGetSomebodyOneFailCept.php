<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin of other Restaurant');
$I->wantToTest('failure when  get one somebody else\'s extra ingredient for a selected meal through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$otherRestaurantAdmin = $I->addUserFixture(
    [
        'email' => 'bo.rest.menu.extraingredient.af.getsomebodyonefail.otheradmin-email@nmotion.pp.ciklum.com',
        'roles' => [
            'ROLE_RESTAURANT_GUEST',
            'ROLE_RESTAURANT_STAFF',
            'ROLE_RESTAURANT_ADMIN'
        ]
    ]
);

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'bo.rest.menu.extraingredient.af.getsomebodyonefail.restaurant-email@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'bo.rest.menu.extraingredient.af.getsomebodyonefail.admin-email@nmotion.pp.ciklum.com'
        ],
        'address'        => [],
        'menuCategories' => [
            [
                'name'      => 'Add Cat Meal Extra Ingredient Test',
                'menuMeals' => [
                    [
                        'name'                 => 'Add Meal Extra Ingredient Test',
                        'mealExtraIngredients' => [
                            ['name' => 'Some meal extra ingredient af 1.1'],
                            ['name' => 'Some meal extra ingredient af 1.2'],
                        ]
                    ]
                ]
            ]
        ]
    ]
);

$I->willEvaluateAuthorizationToken($otherRestaurantAdmin['email'], $otherRestaurantAdmin['password']);

$I->amGoingTo('get one somebody else\'s meal\'s extra ingredient from the web server');

$extraIngredientId = $restaurant['menuCategories'][0]['menuMeals'][0]['mealExtraIngredients'][1]['id'];

$I->sendGET('/backoffice/mealextraingredients/' . $extraIngredientId . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_FORBIDDEN);
