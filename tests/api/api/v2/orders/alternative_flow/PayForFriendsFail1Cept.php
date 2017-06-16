<?php
/**
 * @author samva
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('fail to link friends\'s orders for paying with empty header link using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$users = [
    0 => $I->addUserFixture(['email' => 'api.v2.order.fail1-payforfriends-user.user@nmotion.pp.ciklum.com']),
    1 => $I->addUserFixture(['email' => 'api.v2.order.fail1-payforfriends-friend1.user@nmotion.pp.ciklum.com'])
];

$I->willEvaluateAuthorizationToken($users[0]['username'], $users[0]['password']);

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

$orders = [];

for ($i = 0; $i < count($users); $i++) {
    $checkin = $I->addRestaurantCheckinFixture(
        [
            'user_id'       => $users[$i]['id'],
            'restaurant_id' => $restaurantId,
            'table_number'  => 12
        ]
    );

    $orderParams = [
        'restaurant_id'   => $restaurantId,
        'user_id'         => $users[$i]['id'],
        'table_number'    => $checkin['tableNumber'],
        'order_status_id' => $i == 1 ? 2 : 1,
        'orderMeals'      => [
            [
                'meal_id'                   => $pizzaMealId,
                'meal_option_id'            => $pizzaMealOptions[rand(0, 2)]['id'],
                'orderMealExtraIngredients' => [
                    ['meal_extra_ingredient_id' => $pizzaMealExtraIngredients[rand(0, 2)]['id']],
                    ['meal_extra_ingredient_id' => $pizzaMealExtraIngredients[rand(0, 2)]['id']]
                ]
            ],
            [
                'meal_id' => $beerMealId
            ]
        ]
    ];
    $orders[$i]  = $I->addOrderFixture($orderParams);
}

$I->sendLINK('/api/v2/orders/' . $orders[0]['id'], []);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(412);
$I->seeResponseContainsJson(['success' => false, 'message' => 'Link(s) header not found.']);
