<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('edit existing meal extra ingredient through backoffice-API');

$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.meal.extraingredient.edit@nmotion.com',
        'adminUser'      => [
            'email'      => 'backoffice.meal.extraingredient.edit@nmotion.com',
        ],
        'address'        => [],
        'menuCategories' => [
            0 => [
                'name'      => 'Meal category 1',
                'menuMeals' => [
                    0 => [
                        'name' => 'Menu meal 1',
                        'mealExtraIngredients' => [
                            0 => ['name' => 'Small size']
                        ]
                    ]
                ]
            ]
        ]
    ]
);
$mealExtraIngredientId = $restaurant['menuCategories'][0]['menuMeals'][0]['mealExtraIngredients'][0]['id'];

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// successful scenario
$I->amGoingTo('check that meal extra ingredient is successfully edited');

$params = [
    'name'      => 'ChangedName',
    'price'     => 100500,      // should be replaced with net price calculated from priceIncludingTax
    'priceIncludingTax' => 0.11
];
$I->sendPUT('/backoffice/mealextraingredients/' . $mealExtraIngredientId . '.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase(
    'nmtn_meal_extra_ingredient',
    ['id' => $mealExtraIngredientId, 'name' => 'ChangedName', 'price' => 0.088]
);
