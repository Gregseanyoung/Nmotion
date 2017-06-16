<?php
/**
 * @author mipo
 * @author samva <vas@ciklum.com>
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('callback response from payment system with status = CANCELLED');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'payment.callback.cancelled@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'payment.callback.cancelled@nmotion.pp.ciklum.com'
        ],
        'address'        => [],
        'visible'        => true,
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

$pizzaMealId = $restaurant['menuCategories'][0]['menuMeals'][0]['id'];

$users  = [];
$orders = [];
$tableNumber = rand(1, 100);

for ($i = 0; $i < 2; $i++) {
    $user    = $I->addAnonymousUserFixture();
    $checkin = $I->addRestaurantCheckinFixture(
        [
            'user_id'       => $user['id'],
            'restaurant_id' => $restaurant['id'],
            'table_number'  => $tableNumber
        ]
    );

    $order = $I->addOrderFixture(
        [
            'restaurant_id'   => $restaurant['id'],
            'user_id'         => $user['id'],
            'table_number'    => $checkin['tableNumber'],
            'orderMeals'      => [
                ['meal_id' => $pizzaMealId]
            ],
            'master_id'       => ($i > 0 ) ? $orders[0]['id'] : null,
            'order_status_id' => ORDER_STATUS_PENDING_PAYMENT
        ]
    );
    $users[$i] = $user;
    $orders[$i] = $order;
}

$I->amGoingTo('send payment callback response with status = CANCELLED to the backend server');

$I->haveHttpHeader('Auth', 'DeviceToken ' . $users[0]['deviceIdentity']);
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$params = [
    'MAC'             => "907711a1c3d2a29fb5483324d40beea0ee9764f0f87e81c67d94917fc8868377",
    'acceptreturnurl' => "http:\/\/stage.nmotion.pp.ciklum.com\/paymentconfirmation\/accepted",
    'addFee'          => "1",
    'amount'          => "10688",
    'cancelreturnurl' => "http:\/\/stage.nmotion.pp.ciklum.com\/paymentconfirmation\/cancelled",
    'currency'        => "DKK",
    'language'        => "en_UK",
    'merchant'        => "90150157",
    'orderId'         => $orders[0]['id'],
    'paytype'         => "VISA,MC,DK,MTRO,ELEC,JCB,AMEX",
    'status'          => "CANCELLED",
    'test'            => "1"
];
$I->sendPOST('/paymentconfirmation/', $params);
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeInDatabase(
    'nmtn_payment',
    ['status' => $params['status'], 'order_id' => $orders[0]['id']]
);
$I->seeInDatabase('nmtn_order', ['id' => $orders[0]['id'], 'order_status_id' => ORDER_STATUS_CANCELLED]);
$I->seeInDatabase('nmtn_order', ['id' => $orders[1]['id'], 'order_status_id' => ORDER_STATUS_NEW_ORDER]);
