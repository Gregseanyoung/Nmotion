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
    'serviceType',
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
        'visible'        => true,
        'menuCategories' => [
            [
                'name'      => 'Pizzas',
                'menuMeals' => [
                    [
                        'name'                 => 'Pizza standard',
                        'time_from'            => 0,
                        'time_to'              => 0,
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
                        'time_from'   => 0,
                        'time_to'     => 0,
                        'mealOptions' => [
                            ['name' => '0.3'],
                            ['name' => '0.5']
                        ]
                    ],
                    [
                        'name'        => 'Coca-Cola',
                        'time_from'   => 0,
                        'time_to'     => 0,
                        'mealOptions' => [
                            ['name' => '0.5'],
                            ['name' => '1.0']
                        ]
                    ],
                    [
                        'name'        => 'Fanta',
                        'time_from'   => 86000,
                        'time_to'     => 85000,
                        'mealOptions' => [
                            ['name' => '0.5'],
                            ['name' => '1.0']
                        ]
                    ]
                ]
            ],
            [
                'name'      => 'Deserts',
                'time_from' => 86000,
                'time_to'   => 85000,
                'menuMeals' => [
                    [

                        'name'        => 'Lava Cake',
                        'mealOptions' => [
                            ['name' => 'Cake']
                        ],
                        'time_from'   => 0,
                        'time_to'     => 85000
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
$beerMealOption = $restaurant['menuCategories'][1]['menuMeals'][0]['mealOptions'][1]['id'];
$colaMealId = $restaurant['menuCategories'][1]['menuMeals'][1]['id'];
$colaMealOption = $restaurant['menuCategories'][1]['menuMeals'][1]['mealOptions'][1]['id'];
$fantaMealId = $restaurant['menuCategories'][1]['menuMeals'][2]['id'];
$fantaMealOption = $restaurant['menuCategories'][1]['menuMeals'][2]['mealOptions'][1]['id'];

$lavaMealId = $restaurant['menuCategories'][2]['menuMeals'][0]['id'];
$lavaMealOption = $restaurant['menuCategories'][2]['menuMeals'][0]['mealOptions'][0]['id'];

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
            'mealOption'  => $beerMealOption,
            'quantity'    => 3,
            'mealComment' => 'Some comment to beer'
        ],
        [
            'meal'        => $colaMealId,
            'mealOption'  => $colaMealOption,
            'quantity'    => 1,
            'mealComment' => 'bla bla'
        ],
        [
            'meal'        => $fantaMealId,
            'mealOption'  => $fantaMealOption,
            'quantity'    => 1,
            'mealComment' => 'bla bla'
        ],
        [
            'meal'        => $lavaMealId,
            'mealOption'  => $lavaMealOption,
            'quantity'    => 1,
            'mealComment' => 'bla bla'
        ]
    ],
    'tips'       => 10
];

$I->sendPOST('/api/v2/restaurants/' . $restaurantId . '/orders', $params);
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
                    ],
                    [
                        'meal' => ['id' => $colaMealId]
                    ],
                    [
                        'meal' => ['id' => $fantaMealId]
                    ],
                    [
                        'meal' => ['id' => $lavaMealId]
                    ]
                ],
                'tips'       => 10
            ]
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(1);
$I->seeResponseContainsNumberOfElements('entries.0.orderMeals', 5);
$I->seeResponseContainsNumberOfElements('entries.0.orderMeals.0.orderMealExtraIngredients', 2);
$I->seeResponseContainsNumberOfElements('entries.0.orderMeals.1.orderMealExtraIngredients', 0);
$I->seeResponseEntriesHasFields($response_entry_api_post_restaurants_orders);
