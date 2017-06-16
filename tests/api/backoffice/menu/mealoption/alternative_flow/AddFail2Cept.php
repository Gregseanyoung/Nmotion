<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('failure when adding new meal option through backoffice-API - incorrect data');

$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.meal.option.addfail2@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email'      => 'backoffice.meal.option.addfail2@nmotion.pp.ciklum.com',
        ],
        'address'        => [],
        'menuCategories' => [
            0 => [
                'name'      => 'backoffice.meal.option.addfail2@nmotion.pp.ciklum.com',
                'menuMeals' => [
                    0 => ['name' => 'backoffice.meal.option.addfail2@nmotion.pp.ciklum.com'],
                ]
            ]
        ]
    ]
);
$meal = $restaurant['menuCategories'][0]['menuMeals'][0];

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// validation failed - incorrect data
$params = [
    'name'      => 'Incorrect data',
    'price'     => -100,
    'isDefault' => 'incorrect',
    'extra'     => 'extra field'
];
$I->amGoingTo('send new meal option data to the backend server: adding failed - incorrect data');
$I->sendPOST('/backoffice/meals/' . $meal['id'] . '/options.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
