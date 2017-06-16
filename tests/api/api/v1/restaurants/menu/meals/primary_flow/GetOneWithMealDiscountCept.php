<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('get one menu meal with meal discount through API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'api.menu.meal.getOne.withMealDiscount@nmotion.pp.ciklum.com',
        'visible'        => true,
        'adminUser'      => [
            'email' => 'api.menu.meal.getOne.withMealDiscount@nmotion.pp.ciklum.com'
        ],
        'address'        => [],
        'menuCategories' => [
            [
                'name'      => 'api.menu.meal.getOne.withMealDiscount@nmotion.pp.ciklum.com',
                'visible'   => true,
                'time_from' => 0,
                'time_to'   => 86399,
                'discount_percent' => 0,
                'menuMeals' => [
                    [
                        'name'                 => 'Meal',
                        'price'                => 100,
                        'discount_percent'     => 50,
                        'visible'              => true,
                        'mealExtraIngredients' => [
                            0 => [
                                'name'  => 'Extra ingredient 1',
                                'price' => 10,
                            ],
                            1 => [
                                'name'  => 'Extra ingredient 2',
                                'price' => 30,
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
);

$menuCategory = $restaurant['menuCategories'][0];
$menuMeal     = $menuCategory['menuMeals'][0];

// successful scenario
$I->amGoingTo('request menu meal for the category');
$I->sendGET(
    '/api/v1/meals/' . $menuMeal['id']
);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            [
                'name' => 'Meal',
                'price' => 100,
                'discountPrice' => 50,
                'mealDiscountPercent' => 50,
                'discountPercent' => 50,
                'mealExtraIngredients' => [
                    ['name' => 'Extra ingredient 1', 'discountPrice' => 5],
                    ['name' => 'Extra ingredient 2', 'discountPrice' => 15]
                ]
            ]
        ]
    ]
);
