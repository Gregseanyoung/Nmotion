<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('failure when adding new meal extra ingredient through meal editing through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'rest.meal.extraingredient.add.test.fail3@nmotion.pp.ciklum.com',
        'adminUser'      => ['email' => 'rest.meal.extraingredient.add.test.fail3@nmotion.pp.ciklum.com'],
        'address'        => [],
        'menuCategories' => [
            [
                'menuMeals' => [
                    [
                        'mealExtraIngredients' => [
                            ['name' => 'sauce', 'price' => 1]
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
    $meal['mealExtraIngredients'][0]['mealId']
);

$meal['mealExtraIngredients'][0]['name'] = '';

// validation failed - extra ingredient name should not be blank
$I->amGoingTo('send new meal extra ingredient data with blank name');
$I->sendPUT('/backoffice/meals/' . $meal['id'], $meal);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
