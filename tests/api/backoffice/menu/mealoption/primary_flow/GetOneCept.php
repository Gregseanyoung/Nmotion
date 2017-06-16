<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantTo('get one meal option through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.meal.option.getone@nmotion.com',
        'adminUser'      => [
            'email'      => 'backoffice.meal.option.getone@nmotion.com',
        ],
        'address'        => [],
        'menuCategories' => [
            0 => [
                'name'      => 'backoffice.meal.option.getone@nmotion.com',
                'menuMeals' => [
                    0 => [
                        'name' => 'backoffice.meal.option.getone@nmotion.com',
                        'mealOptions' => [
                            0 => ['name' => 'Small size']
                        ]
                    ]
                ]
            ]
        ]
    ]
);
$mealOptionId = $restaurant['menuCategories'][0]['menuMeals'][0]['mealOptions'][0]['id'];

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// successful scenario
$I->amGoingTo('send get request for one meal option to the backend server: successful scenario');
$I->sendGET('/backoffice/mealoptions/' . $mealOptionId . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            ['id' => $mealOptionId, 'name' => 'Small size']
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(1);
