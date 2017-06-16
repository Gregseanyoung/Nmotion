<?php
/**
 * @author mipo
 * @author samva <vas@ciklum.com>
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('callback response from payment system with status = ACCEPTED');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'payment.callback.accepted@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email' => 'payment.callback.accepted@nmotion.pp.ciklum.com'
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

$I->amGoingTo('send payment callback response with status = ACCEPTED to the backend server');

$I->haveHttpHeader('Auth', 'DeviceToken ' . $users[0]['deviceIdentity']);
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
    'orderId'             => $orders[0]['id'],
    'paytype'             => "DK,VISA,MTRO,MC,ELEC,JCB,AMEX",
    'status'              => "ACCEPTED",
    'test'                => "1",
    'ticket'              => "705172351",
    'ticketStatus'        => "ACCEPTED",
    'transaction'         => "705172340"
];
$I->sendPOST('/paymentconfirmation/', $params);
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->see('Payment successful', '#status');
$I->see('705172340', '#transaction');
$I->see('705172351', '#ticket');
$I->see('510010XXXXXX0000', '#cardNumberMasked');
$I->seeInDatabase(
    'nmtn_payment',
    ['status' => $params['status'], 'transaction' => $params['transaction'], 'order_id' => $orders[0]['id']]
);
$I->seeInDatabase('nmtn_order', ['id' => $orders[0]['id'], 'order_status_id' => ORDER_STATUS_PAID]);
$I->seeInDatabase('nmtn_order', ['id' => $orders[1]['id'], 'order_status_id' => ORDER_STATUS_PAID]);
