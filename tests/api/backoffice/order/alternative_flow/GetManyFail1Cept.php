<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('fail geting many orders through backoffice-API - unknown order');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
    'email'          => 'backoffice.order.get.many.fail1@nmotion.pp.ciklum.com',
    'adminUser'      => [
        'email' => 'backoffice.order.get.many.fail1@nmotion.pp.ciklum.com'
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

$I->sendGET('/backoffice/orders/' . $orders[1]['id'] . ';' . $orders[3]['id'] . ';unknown');
$I->seeResponseCodeIs(HTTP_RESPONSE_NOT_FOUND);
$I->seeResponseContainsJson(['success' => false]);
