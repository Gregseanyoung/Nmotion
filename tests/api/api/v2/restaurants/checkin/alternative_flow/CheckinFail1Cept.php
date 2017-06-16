<?php
/**
 * @author tiger
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('fail to checkin restaurant when providing absent or invalid data');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->haveHttpHeader('Auth', 'DeviceToken ' . md5(time()));

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'api.v2.checkin.bad.params.test@nmotion.pp.ciklum.com',
        'visible'        => 1,
        'adminUser'      => [
            'email' => 'api.v2.checkin.bad.params.test@nmotion.pp.ciklum.com'
        ],
        'address'        => []
    ]
);

$I->sendPOST('/api/v2/restaurants/' . $restaurant['id'] . '/checkin');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
$I->seeResponseContainsJson(['success' => false]);

$I->sendPOST(
    '/api/v2/restaurants/' . $restaurant['id'] . '/checkin',
    ['serviceType' => RESTAURANT_SERVICE_TYPE_IN_HOUSE]
);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
$I->seeResponseContainsJson(['success' => false]);

$I->sendPOST('/api/v2/restaurants/' . $restaurant['id'] . '/checkin', ['table' => 'abc', 'serviceType' => 'abc']);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
$I->seeResponseContainsJson(['success' => false]);
