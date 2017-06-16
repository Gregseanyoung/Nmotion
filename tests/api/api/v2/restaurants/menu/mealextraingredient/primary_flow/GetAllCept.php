<?php

$response_entry_api_get_meal_extraingredients = [
    // used on mobile
    'id',
    'name',
    'price',
    'discountPrice',
    // just returned

];

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('get meal extra ingredients through API');
$I->haveHttpHeader('Content-Type', 'application/json');

$time = (time() + date('Z')) % 86400;

$restaurant = $I->addRestaurantFixture(
    [
        'visible'        => true,
        'menuCategories' => [
            [
                'name'      => 'Target Category',
                'visible'   => true,
                'time_from' => $time - 10,
                'time_to'   => $time + 50,
                'menuMeals' => [
                    0 => [
                        'name'    => 'Visible Meal with extra ingredient With TimeFrame',
                        'visible' => true,
                        'time_from' => $time - 10,
                        'time_to'   => $time + 50,
                        'mealExtraIngredients' => [
                            0 => ['name' => 'Visible extra ingredient within time frame 1'],
                            1 => ['name' => 'Visible extra ingredient within time frame 2']
                        ]
                    ],
                    1 => [
                        'name'    => 'Hidden Meal with extra ingredient',
                        'visible' => false,
                        'mealExtraIngredients' => [
                            0 => ['name' => 'Invisible extra ingredient']
                        ]
                    ],
                    2 => [
                        'name'      => 'Visible Meal with extra ingredient Not Within TimeFrame',
                        'visible'   => true,
                        'time_from' => 0,
                        'time_to'   => 1,
                        'mealExtraIngredients' => [
                            0 => ['name' => 'Visible extra ingredient not within time frame 1'],
                            1 => ['name' => 'Visible extra ingredient not within time frame 2']
                        ]
                    ],
                ]
            ],
        ]
    ]
);

$meals = $restaurant['menuCategories'][0]['menuMeals'];

// successful scenario
$I->amGoingTo('get meal extra ingredients from meal within available time frame');
$I->sendGET('/api/v2/meals/' . $meals[0]['id'] . '/extraingredients.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            ['name' => 'Visible extra ingredient within time frame 1'],
            ['name' => 'Visible extra ingredient within time frame 2']
        ]
    ]
);
$I->seeResponseEntriesHasFields($response_entry_api_get_meal_extraingredients);

$I->amGoingTo('get meal extra ingredients from invisible meal');
$I->sendGET('/api/v2/meals/' . $meals[1]['id'] . '/extraingredients.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->dontSeeResponseContainsJson(['name' => 'Invisible']);

$I->amGoingTo('get meal extra ingredients from meal not within time frame');
$I->sendGET('/api/v2/meals/' . $meals[2]['id'] . '/extraingredients.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->dontSeeResponseContainsJson(['name' => 'Visible not within time frame 1']);
$I->dontSeeResponseContainsJson(['name' => 'Visible not within time frame 2']);
