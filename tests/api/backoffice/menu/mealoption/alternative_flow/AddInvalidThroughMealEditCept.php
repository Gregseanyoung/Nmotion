<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('failure when adding new meal option through meal editing through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'rest.meal.option.add.test.fail3@nmotion.pp.ciklum.com',
        'adminUser'      => ['email' => 'rest.meal.option.add.test.fail3@nmotion.pp.ciklum.com'],
        'address'        => [],
        'menuCategories' => [
            [
                'menuMeals' => [
                    [
                        'mealOptions' => [
                            ['name' => 'bugaga', 'price' => 1]
                        ]
                    ]
                ]
            ]
        ]
    ]
);
$meal = $restaurant['menuCategories'][0]['menuMeals'][0];

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

unset(
    $meal['restaurantId'],
    $meal['menuCategoryId'],
    $meal['logoAssetId'],
    $meal['mealOptions'][0]['mealId'],
    $meal['mealOptions'][0]['mealId']
);

$meal['mealOptions'][] = [
    'name'  => '',
    'price' => 2
];

// validation failed - option name should not be blank
$I->amGoingTo('send new meal option data with blank name');
$I->sendPUT('/backoffice/meals/' . $meal['id'], $meal);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
$I->seeResponseContainsJson(
    [
        'success' => false,
        'errors' => [
            [
                'children' => [
                    'mealOptions' => [
                        'children' => [
                            1 => [
                                'children' => [
                                    'name' => [
                                        'errors' => []
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
$I->dontSeeResponseContains('This form should not contain extra fields');
