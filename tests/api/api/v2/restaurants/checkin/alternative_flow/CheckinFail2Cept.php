<?php
/**
 * @author samva <vas@ciklum.com>
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('fail check-in into restaurant out off operation time using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addAnonymousUserFixture();

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$timeADay = time() % 86400;

$restaurant = $I->addRestaurantFixture(
    [
        'operationTimes' => array_fill(0, 7, ['time_from' => $timeADay - 2, 'time_to' => $timeADay - 1]),
        'visible'        => 1
    ]
);

$I->amGoingTo('send check-in request to the backend server');

$I->dontSeeInDatabase('nmtn_restaurant_checkin', ['restaurant_id' => $restaurant['id']]);

$I->sendPOST(
    '/api/v2/restaurants/' . $restaurant['id'] . '/checkin',
    ['table' => rand(1, 100), 'serviceType' => RESTAURANT_SERVICE_TYPE_IN_HOUSE]
);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
$I->seeResponseContainsJson(
    [
        'success' => false,
        'message' => 'Restaurant is closed.'
    ]
);
