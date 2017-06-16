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
        'visible'        => 1,
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
$params = ['table' => 1, 'serviceType' => RESTAURANT_SERVICE_TYPE_IN_HOUSE];
$I->sendPOST('/api/v2/restaurants/' . $restaurant['id'] . '/checkin', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
$I->seeResponseContainsJson(['success' => false]);
$I->dontSeeInDatabase('nmtn_restaurant_checkin', ['restaurant_id' => $restaurant['id']]);
