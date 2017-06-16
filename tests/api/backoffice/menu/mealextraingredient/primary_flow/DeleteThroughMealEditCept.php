<?php

/** @var $scenario \Codeception\Scenario */
$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('delete meal extra ingredient through meal editing through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.meal.extraingredient.delete3@nmotion.com',
        'adminUser'      => ['email' => 'backoffice.meal.extraingredient.delete3@nmotion.com'],
        'address'        => [],
        'menuCategories' => [
            [
                'menuMeals' => [
                    [
                        'mealExtraIngredients' => [
                            ['name' => 'Small size', 'price' => 1],
                            ['name' => 'Medium size', 'price' => 2],
                            ['name' => 'Big size', 'price' => 3],
                            ['name' => 'Huge size', 'price' => 4]
                        ]
                    ]
                ]
            ]
        ]
    ]
);
$meal = $restaurant['menuCategories'][0]['menuMeals'][0];
$targetMealExtraIngredientId = (int)(string)$meal['mealExtraIngredients'][1]['id'];

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// adjust meal data to be ready be sent to the server
unset(
    $meal['restaurantId'],
    $meal['menuCategoryId'],
    $meal['logoAssetId'],
    $meal['mealExtraIngredients'][0]['mealId'],
    $meal['mealExtraIngredients'][1]['mealId'],
    $meal['mealExtraIngredients'][2]['mealId'],
    $meal['mealExtraIngredients'][3]['mealId']
);

// precondition: meal exist in the DB
$I->seeInDatabase('nmtn_meal_extra_ingredient', ['id' => $targetMealExtraIngredientId]);

$I->amGoingTo('send meal entity without 2nd extra ingredient');

// remove meal 2nd extra ingredient
unset($meal['mealExtraIngredients'][1]);
if ($scenario->running()) {
    $meal['mealExtraIngredients'] = array_values($meal['mealExtraIngredients']);
}

// our expectation
$I->expect('meal 2nd extra ingredient to be removed from the meal');

// action
$I->sendPUT('/backoffice/meals/' . $meal['id'], $meal);

// check results
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->dontSeeResponseContains('"name":"Medium size"');
$I->dontSeeInDatabase('nmtn_meal_extra_ingredient', ['id' => $targetMealExtraIngredientId]);
$I->seeInDatabase('nmtn_meal_extra_ingredient', ['id' => $meal['mealExtraIngredients'][0]['id']]);
$I->seeInDatabase('nmtn_meal_extra_ingredient', ['id' => $meal['mealExtraIngredients'][1]['id']]);
$I->seeInDatabase('nmtn_meal_extra_ingredient', ['id' => $meal['mealExtraIngredients'][2]['id']]);

// verify case when all meal extra ingredients being removed
$mealWithOutIngredients = is_object($meal) ? clone $meal : $meal;
unset(
    $mealWithOutIngredients['mealExtraIngredients'][0],
    $mealWithOutIngredients['mealExtraIngredients'][1],
    $mealWithOutIngredients['mealExtraIngredients'][2]
);

// action
$I->sendPUT('/backoffice/meals/' . $meal['id'], $mealWithOutIngredients);

// check results
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContains('"mealExtraIngredients":[]');
$I->dontSeeInDatabase('nmtn_meal_extra_ingredient', ['id' => $meal['mealExtraIngredients'][0]['id']]);
$I->dontSeeInDatabase('nmtn_meal_extra_ingredient', ['id' => $meal['mealExtraIngredients'][1]['id']]);
$I->dontSeeInDatabase('nmtn_meal_extra_ingredient', ['id' => $meal['mealExtraIngredients'][2]['id']]);
