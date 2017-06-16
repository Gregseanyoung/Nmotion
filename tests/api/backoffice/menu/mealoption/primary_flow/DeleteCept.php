<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('delete meal option through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.meal.option.delete@nmotion.com',
        'adminUser'      => [
            'email'      => 'backoffice.meal.option.delete@nmotion.com',
        ],
        'address'        => [],
        'menuCategories' => [
            0 => [
                'name'      => 'backoffice.meal.option.delete@nmotion.com',
                'menuMeals' => [
                    0 => [
                        'name' => 'backoffice.meal.option.delete@nmotion.com',
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
$mealOptionOldDefaultId = $restaurant['menuCategories'][0]['menuMeals'][0]['mealOptions'][1]['id'];
$mealId = $restaurant['menuCategories'][0]['menuMeals'][0]['id'];

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// successful scenario
$I->amGoingTo('send delete request for one meal option to the backend server: successful scenario');

$I->seeInDatabase('nmtn_meal_option', ['id' => $mealOptionOldDefaultId]);
$I->sendDELETE('/backoffice/mealoptions/' . $mealOptionOldDefaultId . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->dontSeeInDatabase('nmtn_meal_option', ['id' => $mealOptionOldDefaultId]);
