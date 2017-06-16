<?php
/**
 * @author seka
 * @author samva
 */

$response_entry_api_get_restaurant_checkin_orders = [
    'id',
    'resourceUrl',
    'orderTotal',
    'orderTotalWhenSlave',
    'consolidatedOrderTotal',
    'tips',
    'restaurant.id',
    'restaurant.name',
    'user.firstName',
    'user.lastName',
    'createdAt'
];

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('get list of orders by the same table using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'api.order.friends.list.restaurant-email@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'api.order.friends.list.admin-email@nmotion.pp.ciklum.com'
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

$user = $I->addAnonymousUserFixture();

$checkin = $I->addRestaurantCheckinFixture(
    [
        'user_id'       => $user['id'],
        'restaurant_id' => $restaurantId,
        'table_number'  => rand(1, 100)
    ]
);

$friendCount = 4;
for ($i = 1; $i <= $friendCount; $i++) {
    $friend = $I->addAnonymousUserFixture();

    $I->amGoingTo('checkin to restaurant table');
    $I->haveHttpHeader('Auth', 'DeviceToken ' . $friend['deviceIdentity']);

    $friendCheckin = $I->addRestaurantCheckinFixture(
        [
            'user_id'       => $friend['id'],
            'restaurant_id' => $restaurantId,
            'table_number'  => $checkin['tableNumber']
        ]
    );

    $params     = [
        'restaurant_id' => $restaurantId,
        'user_id'       => $friend['id'],
        'table_number'  => $friendCheckin['tableNumber'],
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
                'meal_id'        => $beerMealId,
                'meal_option_id' => $beerMealOptions[rand(0, 1)]['id']
            ]
        ]
    ];
    $orders[$i] = $I->addOrderFixture($params);
}

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);
$I->sendGET('/api/v1/restaurants/' . $restaurantId . '/checkin/orders');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsNumberOfEntries($friendCount);
$I->seeResponseEntriesHasFields($response_entry_api_get_restaurant_checkin_orders);
