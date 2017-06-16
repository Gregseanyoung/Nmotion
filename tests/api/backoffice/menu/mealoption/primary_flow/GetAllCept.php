<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('get all meal options for given meal through backoffice-API');

$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.meal.option.getall@nmotion.com',
        'adminUser'      => [
            'email'      => 'backoffice.meal.option.getall@nmotion.com',
        ],
        'address'        => [],
        'menuCategories' => [
            0 => [
                'name'      => 'backoffice.meal.option.getall@nmotion.com',
                'menuMeals' => [
                    0 => [
                        'name' => 'backoffice.meal.option.getall@nmotion.com',
                        'mealOptions' => [
                            0 => ['name' => 'Small size'],
                            1 => ['name' => 'Medium size'],
                            2 => ['name' => 'Large size']
                        ]
                    ]
                ]
            ]
        ]
    ]
);
$meal = $restaurant['menuCategories'][0]['menuMeals'][0];

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// successful scenario
$I->amGoingTo('send get request for meal options data to the backend server: successful scenario');
$I->sendGET('/backoffice/meals/' . $meal['id'] . '/options.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            ['id' => $meal['mealOptions'][0]['id']],
            ['id' => $meal['mealOptions'][1]['id']],
            ['id' => $meal['mealOptions'][2]['id']]
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(3);
