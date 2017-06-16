<?php
/**
 * @author samva
 */

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('get income for restaurant through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
    'email'          => 'api.order.income.restaurant-email@nmotion.pp.ciklum.com',
    'adminUser'      => [
        'email' => 'api.order.income.admin-email@nmotion.pp.ciklum.com'
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

for ($i = 1; $i <= 10; $i++) {
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

    if ($i % 2) {
        $time = strtotime(date('2013-' . $i . '-01 10:00:00'));
    } else {
        // this way we will have 4 days in one week and 5 days in one month
        $time = strtotime(date('2013-02-' . $i . ' 10:00:00'));
    }

    $params = [
        'updated_at' => $time,
        'order_id' => $orders[$i]['id']
    ];

    $I->addPaymentFixture($params);
}

$I->amGoingTo('get income for restaurant grouped by week');
$I->sendGET('/backoffice/restaurants/' . $restaurantId . '/income?period=w');
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContains('"productTotal":"40.00"');
$I->seeResponseContainsNumberOfEntries(7);

$I->amGoingTo('get income for restaurant grouped by 2 weeks');
$I->sendGET('/backoffice/restaurants/' . $restaurantId . '/income?period=2w');
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContains('"productTotal":"50.00"');
$I->seeResponseContainsNumberOfEntries(6);

$I->amGoingTo('get income for restaurant grouped by month');
$I->sendGET('/backoffice/restaurants/' . $restaurantId . '/income?period=m');
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContains('"productTotal":"50.00"');
$I->seeResponseContainsNumberOfEntries(6);
