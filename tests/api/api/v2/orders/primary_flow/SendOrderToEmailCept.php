<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('send order details to my email using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addUserFixture(['email' => 'api.v2.order.sendordertoemail.user@nmotion.pp.ciklum.com']);

$I->willEvaluateAuthorizationToken($user['username'], $user['password']);

$restaurant = $I->addRestaurantFixture(
    [
        'menuCategories' => [
            [
                'name'      => 'Pizzas',
                'menuMeals' => [
                    [
                        'name'                 => 'Pizza standard',
                        'mealOptions'          => [
                            ['name' => 'Small size'],
                            ['name' => 'Medium size'],
                            ['name' => 'Large size']
                        ],
                        'mealExtraIngredients' => [
                            ['name' => 'Olive'],
                            ['name' => 'Ketchup'],
                            ['name' => 'Parmesan'],
                        ]
                    ]
                ]
            ],
            [
                'name'      => 'Drinks',
                'menuMeals' => [
                    [
                        'name'        => 'Leffe brune',
                        'mealOptions' => [
                            ['name' => '0.3'],
                            ['name' => '0.5']
                        ]
                    ]
                ]
            ]
        ]
    ]
);

$restaurantId = $restaurant['id'];

$pizzaMealId               = $restaurant['menuCategories'][0]['menuMeals'][0]['id'];
$pizzaMealOptions          = $restaurant['menuCategories'][0]['menuMeals'][0]['mealOptions'];
$pizzaMealExtraIngredients = $restaurant['menuCategories'][0]['menuMeals'][0]['mealExtraIngredients'];

$beerMealId = $restaurant['menuCategories'][1]['menuMeals'][0]['id'];

$I->addRestaurantCheckinFixture(
    [
        'user_id'       => $user['id'],
        'restaurant_id' => $restaurantId,
        'table_number'  => 2
    ]
);

$orderParams = [
    'restaurant_id' => $restaurantId,
    'user_id'       => $user['id'],
    'orderMeals'    => [
        [
            'meal_id'                   => $pizzaMealId,
            'meal_option_id'            => $pizzaMealOptions[rand(0, 2)]['id'],
            'orderMealExtraIngredients' => [
                ['meal_extra_ingredient_id' => $pizzaMealExtraIngredients[rand(0, 2)]['id']],
                ['meal_extra_ingredient_id' => $pizzaMealExtraIngredients[rand(0, 2)]['id']]
            ]
        ],
        [
            'meal_id'      => $beerMealId,
            'quantity'     => rand(1, 5),
            'meal_comment' => 'Some comment to beer'
        ]
    ]
];

$order = $I->addOrderFixture($orderParams);

$I->sendPOST('/api/v2/orders/' . $order['id'] . '/sendtoemail.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(200);
$I->seeResponseContainsJson(['success' => true]);
