<?php
/**
 * @author samva
 */

$response_entry_api_link_order = [
    // used on mobile

    'id',
    'consolidatedProductTotal',
    'consolidatedDiscount',
    'consolidatedSalesTax',
    'consolidatedTips',
    'consolidatedOrderTotal',
    'consolidatedOrderTotalInCents',
    'createdAt',

    'orderMeals.[].id',
    'orderMeals.[].name',
    'orderMeals.[].mealComment',
    'orderMeals.[].price',
    'orderMeals.[].discountPercent',
    'orderMeals.[].discountPrice',
    'orderMeals.[].quantity',

    'orderTotalWhenSlave',

    // just returned
    'tableNumber',
    'orderStatus.id',
    'productTotal',
    'discount',
    'salesTax',
    'tips',
    'orderTotal',
    'orderTotalInCents',

    'restaurant.id',
    'restaurant.name',
    'restaurant.address.longitude',
    'restaurant.address.latitude',
    'restaurant.address.addressLine1',
    'restaurant.address.postalCode',

    'orderMeals.[].description',
    'orderMeals.[].mealDiscountPercent',
    'orderMeals.[].mealOptionDiscountPrice',
    'orderMeals.[].mealOptionName',
    'orderMeals.[].mealOptionPrice',

    'orderMeals.[].orderMealExtraIngredients.[].id',
    'orderMeals.[].orderMealExtraIngredients.[].name',
    'orderMeals.[].orderMealExtraIngredients.[].price',
    'orderMeals.[].orderMealExtraIngredients.[].discountPrice',
    'orderMeals.[].orderMealExtraIngredients.[].mealExtraIngredient.id'
];

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('link friends\'s orders for paying using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$users = [
    0 => $I->addUserFixture(['email' => 'api.v2.order.payforfriends-user.user@nmotion.pp.ciklum.com']),
    1 => $I->addUserFixture(['email' => 'api.v2.order.payforfriends-friend1.user@nmotion.pp.ciklum.com']),
    2 => $I->addUserFixture(['email' => 'api.v2.order.payforfriends-friend2.user@nmotion.pp.ciklum.com']),
    3 => $I->addUserFixture(['email' => 'api.v2.order.payforfriends-friend3.user@nmotion.pp.ciklum.com']),
    4 => $I->addUserFixture(['email' => 'api.v2.order.payforfriends-friend4.user@nmotion.pp.ciklum.com'])
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
        'restaurant_id' => $restaurantId,
        'user_id'       => $users[$i]['id'],
        'table_number'  => $checkin['tableNumber'],
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
                'meal_id' => $beerMealId
            ]
        ]
    ];
    $orders[$i]  = $I->addOrderFixture($orderParams);
}

$linkEntries = [
    ['uri' => '/api/v2/orders/' . $orders[2]['id'], 'link-param' => 'rel="slave"'],
    ['uri' => '/api/v2/orders/' . $orders[4]['id'], 'link-param' => 'rel="slave"']
];

$I->sendLINK('/api/v2/orders/' . $orders[0]['id'], $linkEntries);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(200);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_order', ['id' => $orders[0]['id'], 'order_status_id' => 1, 'master_id' => null]);
$I->seeInDatabase('nmtn_order', ['id' => $orders[1]['id'], 'order_status_id' => 1, 'master_id' => null]);
$I->seeInDatabase('nmtn_order', ['id' => $orders[2]['id'], 'order_status_id' => 1, 'master_id' => $orders[0]['id']]);
$I->seeInDatabase('nmtn_order', ['id' => $orders[3]['id'], 'order_status_id' => 1, 'master_id' => null]);
$I->seeInDatabase('nmtn_order', ['id' => $orders[4]['id'], 'order_status_id' => 1, 'master_id' => $orders[0]['id']]);
$I->seeResponseEntriesHasFields($response_entry_api_link_order);
