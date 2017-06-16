<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('get many orders through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
    'email'          => 'backoffice.order.get.many@nmotion.pp.ciklum.com',
    'adminUser'      => [
        'email' => 'backoffice.order.get.many@nmotion.pp.ciklum.com'
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
                    ]
                ]
            ]
        ]
    ]
    ]
);

$restaurantId = $restaurant['id'];

$pizzaMeal       = $restaurant['menuCategories'][0]['menuMeals'][0];
$pizzaMealOption = $restaurant['menuCategories'][0]['menuMeals'][0]['mealOptions'][1];

$user = $I->addAnonymousUserFixture();

for ($i = 1; $i <= 3; $i++) {
    $params     = [
        'restaurant_id' => $restaurantId,
        'user_id'       => $user['id'],
        'table_number'  => 2,
        'orderMeals'    => [
            [
                'meal_id'                   => $pizzaMeal['id'],
                'meal_option_id'            => $pizzaMealOption['id'],
            ]
        ]
    ];
    $orders[$i] = $I->addOrderFixture($params);
}

$I->amGoingTo('get one order');
$I->sendGET('/backoffice/orders/' . $orders[2]['id']);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsNumberOfEntries(1);

$I->amGoingTo('get many orders');
$I->sendGET('/backoffice/orders/' . $orders[1]['id'] . ';' . $orders[2]['id'] . ';' . $orders[3]['id']);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsNumberOfEntries(3);
