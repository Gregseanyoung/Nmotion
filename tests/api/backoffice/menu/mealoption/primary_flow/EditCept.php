<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('edit existing meal option through backoffice-API');

$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.meal.option.edit@nmotion.com',
        'adminUser'      => [
            'email'      => 'backoffice.meal.option.edit@nmotion.com',
        ],
        'address'        => [],
        'menuCategories' => [
            0 => [
                'name'      => 'backoffice.meal.option.edit@nmotion.com',
                'menuMeals' => [
                    0 => [
                        'name' => 'backoffice.meal.option.edit@nmotion.com',
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
$I->amGoingTo('check that meal option is successfully edited');

$params = [
    'name'      => 'ChangedName',
    'price'     => 100500,      // should be replaced with net price calculated from priceIncludingTax
    'priceIncludingTax' => 0.11
];
$I->sendPUT('/backoffice/mealoptions/' . $mealOptionId . '.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase(
    'nmtn_meal_option',
    ['id' => $mealOptionId, 'name' => 'ChangedName', 'price' => 0.088]
);
