<?php
/**
 * @author samva <vas@ciklum.com>
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('fail with check-out to create order using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addAnonymousUserFixture();

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$restaurant = $I->addRestaurantFixture();
$restaurantId = $restaurant['id'];

$oldCheckin = $I->addRestaurantCheckinFixture(
    [
        'user_id'       => $user['id'],
        'restaurant_id' => $restaurantId,
        'table_number'  => rand(1, 100)
    ]
);

$newCheckin = $I->addRestaurantCheckinFixture(
    [
        'user_id'       => $user['id'],
        'restaurant_id' => $restaurantId,
        'table_number'  => $oldCheckin['tableNumber'],
        'checked_out'   => true
    ]
);

$I->amGoingTo('send request to the server with order');

$I->sendPOST('/api/v2/restaurants/' . $restaurantId . '/orders', []);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
$I->seeResponseContainsJson(
    [
        'success' => false,
        'message' => 'Restaurant checkin is expired.'
    ]
);
