<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('add new meal option for selected meal through backoffice-API');

$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.meal.option.add@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email'      => 'backoffice.meal.option.add@nmotion.pp.ciklum.com',
        ],
        'address'        => [],
        'menuCategories' => [
            0 => [
                'name'      => 'Rolls',
                'menuMeals' => [
                    0 => ['name' => 'Green Dragon roll'],
                ]
            ]
        ]
    ]
);
$meal = $restaurant['menuCategories'][0]['menuMeals'][0];

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// successful scenario
$I->amGoingTo('send new meal option data to the backend server: successful scenario');
$params = [
    'name'      => 'Meal Option',
    'price'     => 50.05
];
$I->dontSeeInDatabase('nmtn_meal_option', ['name' => $params['name']]);
$I->sendPOST('/backoffice/meals/' . $meal['id'] . '/options.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_meal_option', ['name' => $params['name']]);
//$I->seeInDatabase('nmtn_meal', ['id' => $meal['id'], 'meal_option_default_id' => 1]);
