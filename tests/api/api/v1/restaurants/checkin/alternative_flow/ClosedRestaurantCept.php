<?php
/**
 * @author tiger
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('fail to checkin to closed restaurant using API');
$I->haveHttpHeader('Content-Type', 'application/json');

//$I->markTestIncomplete('backend is not yet implemented');
$I->haveHttpHeader('Auth', 'DeviceToken ' . md5(time()));

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'checkin.to.closed.rest.test@nmotion.pp.ciklum.com',
        'visible'        => 1,
        'adminUser'      => [
            'email' => 'checkin.to.closed.rest.test@nmotion.pp.ciklum.com'
        ],
        'address'        => [],
        'operationTimes' => [
            // per day from 00:00:00 to 00:00:01 :)
            ['time_from' => 0, 'time_to' => 1],
            ['time_from' => 0, 'time_to' => 1],
            ['time_from' => 0, 'time_to' => 1],
            ['time_from' => 0, 'time_to' => 1],
            ['time_from' => 0, 'time_to' => 1],
            ['time_from' => 0, 'time_to' => 1],
            ['time_from' => 0, 'time_to' => 1]
        ]
    ]
);

$I->dontSeeInDatabase('nmtn_restaurant_checkin', ['restaurant_id' => $restaurant['id']]);
$I->amGoingTo('send check-in request to the backend server');
$I->sendPOST('/api/v1/restaurants/' . $restaurant['id'] . '/checkin', ['table' => 1]);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
$I->seeResponseContainsJson(['success' => false, 'message' => 'Restaurant is closed.']);
$I->dontSeeInDatabase('nmtn_restaurant_checkin', ['restaurant_id' => $restaurant['id']]);
