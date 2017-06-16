<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('delete meal extra ingredient through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.meal.extraingredient.delete@nmotion.com',
        'adminUser'      => [
            'email'      => 'backoffice.meal.extraingredient.delete@nmotion.com',
        ],
        'address'        => [],
        'menuCategories' => [
            0 => [
                'name'      => 'backoffice.meal.extraingredient.delete@nmotion.com',
                'menuMeals' => [
                    0 => [
                        'name' => 'backoffice.meal.extraingredient.delete@nmotion.com',
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
$I->amGoingTo('send delete request for one meal extra ingredient to the backend server: successful scenario');

$I->seeInDatabase('nmtn_meal_extra_ingredient', ['id' => $mealExtraIngredientId]);
$I->sendDELETE('/backoffice/mealextraingredients/' . $mealExtraIngredientId . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->dontSeeInDatabase('nmtn_meal_extra_ingredient', ['id' => $mealExtraIngredientId]);
