<?php

/** @var $scenario \Codeception\Scenario */
$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('delete meal option through meal editing through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.meal.option.delete3@nmotion.com',
        'adminUser'      => ['email' => 'backoffice.meal.option.delete3@nmotion.com'],
        'address'        => [],
        'menuCategories' => [
            [
                'menuMeals' => [
                    [
                        'mealOptions' => [
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
$targetMealOptionId = (int)(string)$meal['mealOptions'][1]['id'];

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// adjust meal data to be ready be sent to the server
unset(
    $meal['restaurantId'],
    $meal['menuCategoryId'],
    $meal['logoAssetId'],
    $meal['mealOptions'][0]['mealId'],
    $meal['mealOptions'][2]['mealId'],
    $meal['mealOptions'][3]['mealId']
);

// precondition: meal exist in the DB
$I->seeInDatabase('nmtn_meal_option', ['id' => $targetMealOptionId]);

$I->amGoingTo('send meal entity without 2nd option');

// remove meal 2nd extra ingredient
unset($meal['mealOptions'][1]);
if ($scenario->running()) {
    $meal['mealOptions'] = array_values($meal['mealOptions']);
}

// our expectation
$I->expect('meal 2nd option to be removed from the meal');

// action
$I->sendPUT('/backoffice/meals/' . $meal['id'], $meal);

// check results
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->dontSeeResponseContains('"name":"Medium size"');
$I->dontSeeInDatabase('nmtn_meal_option', ['id' => $targetMealOptionId]);
$I->seeInDatabase('nmtn_meal_option', ['id' => $meal['mealOptions'][0]['id']]);
$I->seeInDatabase('nmtn_meal_option', ['id' => $meal['mealOptions'][1]['id']]);
$I->seeInDatabase('nmtn_meal_option', ['id' => $meal['mealOptions'][2]['id']]);

// verify case when all meal options being removed
$mealWithOutIngredients = is_object($meal) ? clone $meal : $meal;
unset(
    $mealWithOutIngredients['mealOptions'][0],
    $mealWithOutIngredients['mealOptions'][1],
    $mealWithOutIngredients['mealOptions'][2]
);

// action
$I->sendPUT('/backoffice/meals/' . $meal['id'], $mealWithOutIngredients);

// check results
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContains('"mealOptions":[]');
$I->dontSeeInDatabase('nmtn_meal_option', ['id' => $meal['mealOptions'][0]['id']]);
$I->dontSeeInDatabase('nmtn_meal_option', ['id' => $meal['mealOptions'][1]['id']]);
$I->dontSeeInDatabase('nmtn_meal_option', ['id' => $meal['mealOptions'][2]['id']]);
