<?php
/**
 * @author samva
 */

$response_entry_api_post_restaurants_orders = [
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
$I->wantToTest('create order using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addAnonymousUserFixture();

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'api.order.add-guest.restaurant-email@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'api.order.add-guest.admin-email@nmotion.pp.ciklum.com'
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

$pizzaMeal                   = $restaurant['menuCategories'][0]['menuMeals'][0];
$pizzaMealId                 = $pizzaMeal['id'];
$pizzaMealOption             = $restaurant['menuCategories'][0]['menuMeals'][0]['mealOptions'][1];
$pizzaMealOptionId           = $pizzaMealOption['id'];
$pizzaMealExtraIngredientId1 = $restaurant['menuCategories'][0]['menuMeals'][0]['mealExtraIngredients'][0]['id'];
$pizzaMealExtraIngredientId2 = $restaurant['menuCategories'][0]['menuMeals'][0]['mealExtraIngredients'][2]['id'];

$beerMealId = $restaurant['menuCategories'][1]['menuMeals'][0]['id'];

$I->addRestaurantCheckinFixture(
    [
        'user_id'       => $user['id'],
        'restaurant_id' => $restaurantId,
        'table_number'  => rand(1, 100)
    ]
);

$I->amGoingTo('send request to the server with order');

$params = [
    'orderMeals' => [
        [
            'meal'                      => $pizzaMealId,
            'mealOption'                => $pizzaMealOptionId,
            'quantity'                  => 2,
            'mealComment'               => 'Some comment to pizza',
            'orderMealExtraIngredients' => [
                ['mealExtraIngredient' => $pizzaMealExtraIngredientId1],
                ['mealExtraIngredient' => $pizzaMealExtraIngredientId2]
            ]
        ],
        [
            'meal'        => $beerMealId,
            'mealOption'  => $restaurant['menuCategories'][1]['menuMeals'][0]['mealOptions'][1]['id'],
            'quantity'    => 3,
            'mealComment' => 'Some comment to beer'
        ]
    ],
    'tips'       => 10
];

$I->sendPOST('/api/v1/restaurants/' . $restaurantId . '/orders', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            [
                'orderMeals' => [
                    [
                        'meal'                      => ['id' => $pizzaMealId],
                        'mealOption'                => ['id' => $pizzaMealOptionId],
                        'quantity'                  => 2,
                        'mealComment'               => 'Some comment to pizza',
                        'name'                      => $pizzaMeal['name'],
                        'description'               => $pizzaMeal['description'],
                        'price'                     => $pizzaMeal['price'],
                        'mealOptionPrice'           => $pizzaMealOption['price'],
                        'mealOptionName'            => $pizzaMealOption['name'],
                        'discountPercent'           => $pizzaMeal['discountPercent'],
                        'orderMealExtraIngredients' => [
                            ['mealExtraIngredient' => ['id' => $pizzaMealExtraIngredientId1]],
                            ['mealExtraIngredient' => ['id' => $pizzaMealExtraIngredientId2]]
                        ]
                    ],
                    [
                        'meal'        => ['id' => $beerMealId],
                        'quantity'    => 3,
                        'mealComment' => 'Some comment to beer'
                    ]
                ],
                'tips'       => 10
            ]
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(1);
$I->seeResponseContainsNumberOfElements('entries.0.orderMeals', 2);
$I->seeResponseContainsNumberOfElements('entries.0.orderMeals.0.orderMealExtraIngredients', 2);
$I->seeResponseContainsNumberOfElements('entries.0.orderMeals.1.orderMealExtraIngredients', 0);
$I->seeResponseEntriesHasFields($response_entry_api_post_restaurants_orders);
