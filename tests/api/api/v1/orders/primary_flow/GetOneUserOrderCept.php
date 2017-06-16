<?php
/**
 * @author seka
 * @author samva
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('get one of user orders using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addAnonymousUserFixture();

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'api.order.one.restaurant-email@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'api.order.one.admin-email@nmotion.pp.ciklum.com'
        ],
        'address'        => [],
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

$beerMealId      = $restaurant['menuCategories'][1]['menuMeals'][0]['id'];
$beerMealOptions = $restaurant['menuCategories'][1]['menuMeals'][0]['mealOptions'];


$checkin = $I->addRestaurantCheckinFixture(
    [
        'user_id'       => $user['id'],
        'restaurant_id' => $restaurantId,
        'table_number'  => rand(1, 100)
    ]
);

$orders                  = [];
$expectedThirdOrderMeals = [];
for ($i = 1; $i < 5; $i++) {
    $params     = [
        'orderMeals'      => [
            [
                'meal_id'                   => $pizzaMealId,
                'meal_option_id'            => $pizzaMealOptions[rand(0, 2)]['id'],
                'quantity'                  => rand(1, 5),
                'meal_comment'              => 'Some comment to pizza ' . $i,
                'orderMealExtraIngredients' => [
                    ['meal_extra_ingredient_id' => $pizzaMealExtraIngredients[rand(0, 2)]['id']],
                    ['meal_extra_ingredient_id' => $pizzaMealExtraIngredients[rand(0, 2)]['id']]
                ]
            ],
            [
                'meal_id'        => $beerMealId,
                'meal_option_id' => $beerMealOptions[rand(0, 1)]['id'],
                'quantity'       => rand(1, 5),
                'meal_comment'   => 'Some comment to beer ' . $i
            ]
        ],
        'table_number'    => 2,
        'order_status_id' => rand(1, 5),
        'restaurant_id'   => $restaurantId,
        'user_id'         => $user['id']
    ];
    $order      = $I->addOrderFixture($params);
    $orders[$i] = $order;
    if ($i == 3) {
        $expectedThirdOrderMeals = [
            ['id' => $order['orderMeals'][0]['id']],
            ['id' => $order['orderMeals'][1]['id']]
        ];
    }
}

$I->sendGET('/api/v1/orders/' . $orders[3]['id']);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsNumberOfEntries(1);
$I->seeResponseContainsJson(
    [
        "entries" => [
            ["orderMeals" => $expectedThirdOrderMeals]
        ]
    ]
);
