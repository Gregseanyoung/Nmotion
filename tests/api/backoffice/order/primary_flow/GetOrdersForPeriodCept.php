<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('get orders for specified period through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'menuCategories' => [
            [
                'name'      => 'Pizzas',
                'menuMeals' => [
                    ['name' => 'Pizza']
                ]
            ]
        ]
    ]
);
$user = $I->addAnonymousUserFixture();

$restaurantId = $restaurant['id'];
$pizzaMeal    = $restaurant['menuCategories'][0]['menuMeals'][0];

for ($i = 0; $i <= 2; $i ++) {
    $orders[$i] = $I->addOrderFixture(
        [
            'restaurant_id' => $restaurantId,
            'user_id'       => $user['id'],
            //  2012-12-20 00:00:00  |  2012-12-21 00:00:00 | 2012-12-22 00:00:00
            'created_at'    => strtotime('2012-12-20') + $i * 24 * 3600,
            'updated_at'    => strtotime('2012-12-20') + $i * 24 * 3600,
            'orderMeals'    => [
                ['meal_id' => $pizzaMeal['id']]
            ]
        ]
    );
}

$filters = [
    'filter' => [
        ['property' => 'dateFrom', 'value' => '2012-12-20'],  // End of the era according to Mayan calendar
        ['property' => 'dateTo', 'value' => '2012-12-21']   // The next day after the end of the era :)
    ]
];
$I->amGoingTo('get all orders done within the period between 2012-12-20 and 2012-12-21 inclusively');
$I->sendGET('/backoffice/orders', $filters);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsNumberOfEntries(2);
