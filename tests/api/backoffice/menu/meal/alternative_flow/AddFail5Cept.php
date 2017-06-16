<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('failure when adding new meal with duplicated new extraingredient names through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'bo.menu.meal.add-fail5.restaurant@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'bo.menu.meal.add-fail5.admin@nmotion.pp.ciklum.com'
        ],
        'address'        => [],
        'menuCategories' => [
            ['name' => 'Pizzas']
        ]
    ]
);

$params = [
    'name'                 => 'Meal5',
    'description'          => 'Meal5 description',
    'price'                => 100,
    'discountPercent'      => 10,
    'visible'              => true,
    'mealExtraIngredients' => [
        [
            'name'  => 'test',
            'price' => 10
        ],
        [
            'name'  => 'test',
            'price' => 10
        ]
    ]
];

$I->amGoingTo('failure when posting new meal with duplicated extra ingredients to server');
$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);
$I->sendPOST('/backoffice/menucategories/' . $restaurant['menuCategories'][0]['id'] . '/meals.json', $params);

$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
$I->seeResponseContainsJson(
    [
        'success' => false,
        'message' => 'Validation failed',
        'errors'  => [
            [
                'children' => [
                    'mealExtraIngredients' => [
                        'children' => [
                            1 => [
                                'children' => [
                                    'name' => [
                                        'errors' => [
                                            'Ingredient with such name is duplicated for given meal.'
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
);
