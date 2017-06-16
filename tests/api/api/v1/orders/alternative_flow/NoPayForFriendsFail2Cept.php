<?php
/**
 * @author samva
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('fail to unlink friends\'s orders for paying with not new order using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$users = [
    0 => $I->addUserFixture(['email' => 'api.order.fail2-nopayforfriends-user.user@nmotion.pp.ciklum.com']),
    1 => $I->addUserFixture(['email' => 'api.order.fail2-nopayforfriends-friend1.user@nmotion.pp.ciklum.com'])
];

$I->willEvaluateAuthorizationToken($users[0]['username'], $users[0]['password']);

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'api.order.fail2-nopayforfriends.restaurant-email@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'api.order.fail2-nopayforfriends.admin-email@nmotion.pp.ciklum.com'
        ],
        'address'        => [],
        'visible'        => true,
        'menuCategories' => [
            [
                'name'      => 'Pizzas',
                'visible'   => true,
                'menuMeals' => [
                    [
                        'name'                 => 'Pizza standard',
                        'visible'              => true,
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
                'visible'   => true,
                'menuMeals' => [
                    [
                        'name'        => 'Leffe brune',
                        'visible'     => true,
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

$linkEntries = [
    ['uri' => '/api/v1/orders/' . $orders[1]['id'], 'link-param' => 'rel="slave"']
];

$I->sendUNLINK('/api/v1/orders/' . $orders[0]['id'], $linkEntries);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_NOT_FOUND);
$I->seeResponseContainsJson(['success' => false, 'message' => 'Linked resource not found.']);
