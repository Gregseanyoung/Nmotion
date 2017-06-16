<?php
/**
 * @author samva
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('get sorted list of user orders using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addAnonymousUserFixture();

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'api.order.list2.restaurant-email@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'api.order.list2.admin-email@nmotion.pp.ciklum.com'
        ],
        'address'        => [],
        'menuCategories' => [
            [
                'name'      => 'Pizzas',
                'menuMeals' => [
                    ['name' => 'Pizza standard']
                ]
            ]
        ]
    ]
);

$checkin = $I->addRestaurantCheckinFixture(
    [
        'user_id'       => $user['id'],
        'restaurant_id' => $restaurant['id'],
        'table_number'  => rand(1, 100)
    ]
);

$time   = time();
$count  = 4;
$orders = [];

for ($i = 1; $i <= $count; $i++) {
    $params     = [
        'restaurant_id'   => $restaurant['id'],
        'user_id'         => $user['id'],
        'table_number'    => $checkin['tableNumber'],
        'order_status_id' => 3,
        'created_at'      => $time + $i,
        'updated_at'      => $time + $i,
        'order_total'     => 1000 + ($i % 2) * $i * 50,
        'orderMeals'      => [
            ['meal_id' => $restaurant['menuCategories'][0]['menuMeals'][0]['id']]
        ]
    ];
    $orders[$i] = $I->addOrderFixture($params);
}

// default sort should be like sort[updatedAt]=desc
foreach (['', '?sort[updatedAt]=desc'] as $add) {
    $I->sendGET('/api/v1/users/me/orders' . $add);
    $I->seeResponseIsJson();
    $I->seeResponseCodeIs(HTTP_RESPONSE_OK);
    $I->seeResponseContainsJson(
        [
            'success' => true,
            'entries' => [
                ['id' => $orders[4]['id']],
                ['id' => $orders[3]['id']],
                ['id' => $orders[2]['id']],
                ['id' => $orders[1]['id']]
            ]
        ]
    );
    $I->seeResponseContainsNumberOfEntries($count);
}

$I->sendGET('/api/v1/users/me/orders?sort[orderTotal]=desc');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            ['id' => $orders[3]['id']],
            ['id' => $orders[1]['id']],
            ['id' => $orders[4]['id']],
            ['id' => $orders[2]['id']]
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries($count);

$allowedSortProperties = [
    'tableNumber',
    'productTotal',
    'discount',
    'tips',
    //'orderTotal', see above
    'createdAt',
    //'updatedAt' see above
];

foreach ($allowedSortProperties as $allowedSortProperty) {
    $randOrder = (rand(1, 2) == 1 ? 'desc' : 'asc');
    $I->sendGET(sprintf('/api/v1/users/me/orders?sort[%s]=desc&sort[updatedAt]=%s', $allowedSortProperty, $randOrder));
    $I->seeResponseIsJson();
    $I->seeResponseCodeIs(HTTP_RESPONSE_OK);
    $I->seeResponseContainsNumberOfEntries($count);
}
