<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('failed callback response from payment system - unknown status');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'payment.callback.failed4@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'payment.callback.failed4@nmotion.pp.ciklum.com'
        ],
        'visible'        => true,
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

$pizzaMealId = $restaurant['menuCategories'][0]['menuMeals'][0]['id'];

$user    = $I->addAnonymousUserFixture();
$checkin = $I->addRestaurantCheckinFixture(
    [
        'user_id'       => $user['id'],
        'restaurant_id' => $restaurant['id'],
        'table_number'  => rand(1, 100)
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
        'order_status_id' => ORDER_STATUS_PENDING_PAYMENT
    ]
);

$I->amGoingTo('send payment callback response with unknown status to the backend server');

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$params = [
    'MAC'                 => "377bb884cf02cbd54ac0d974e0970008ebf3fa83c08ed15c1683fbce1777900b",
    'acceptReturnUrl'     => "http:\/\/stage.nmotion.pp.ciklum.com\/paymentconfirmation\/accepted",
    'acquirer'            => "test",
    'actionCode'          => "d100",
    'amount'              => "2375",
    'callbackUrl'         => "http:\/\/stage.nmotion.pp.ciklum.com\/paymentconfirmation\/",
    'cancelreturnurl'     => "http:\/\/stage.nmotion.pp.ciklum.com\/paymentconfirmation\/cancelled",
    'cardNumberMasked'    => "510010XXXXXX0000",
    'cardTypeName'        => "MasterCard",
    'createTicketAndAuth' => "1",
    'currency'            => "DKK",
    'expMonth'            => "06",
    'expYear'             => "24",
    'merchant'            => "90150157",
    'orderId'             => $order['id'],
    'paytype'             => "DK,VISA,MTRO,MC,ELEC,JCB,AMEX",
    'status'              => "UNKNOWN",
    'test'                => "1",
    'ticket'              => "705172351",
    'ticketStatus'        => "ACCEPTED",
    'transaction'         => "705172340"
];
$I->sendPOST('/paymentconfirmation/', $params);
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
$I->seeInDatabase('nmtn_payment', ['order_id' => $order['id'], 'status' => 'FAILED']);
$I->seeInDatabase('nmtn_order', ['id' => $order['id'], 'order_status_id' => ORDER_STATUS_FAILED]);
