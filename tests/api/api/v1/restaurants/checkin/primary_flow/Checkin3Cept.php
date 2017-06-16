<?php
/**
 * @author samva <vas@ciklum.com>
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('check-in into restaurant\'s table that has outdated checkins using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'api.restaurant.checkin3.restaurant-email@nmotion.pp.ciklum.com',
        'adminUser' => [
            'email' => 'api.restaurant.checkin3.admin-email@nmotion.pp.ciklum.com'
        ],
        'address'   => [],
        'visible'   => 1
    ]
);

$outdatedTime  = time() - 3600 * 24;
$tableNumber   = rand(1, 100);
$checkedInUser = $I->addAnonymousUserFixture();
$I->addRestaurantCheckinFixture(
    [
        'user_id'       => $checkedInUser['id'],
        'restaurant_id' => $restaurant['id'],
        'table_number'  => $tableNumber,
        'checked_out'   => false,
        'created_at'    => $outdatedTime,
        'updated_at'    => $outdatedTime
    ]
);

$I->seeInDatabase(
    'nmtn_restaurant_checkin',
    ['restaurant_id' => $restaurant['id'], 'user_id' => $checkedInUser['id'], 'checked_out' => false]
);

$user = $I->addAnonymousUserFixture();
$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$I->amGoingTo('send request check-in without param "empty" into restaurant\'s table that has outdated checkins');

$I->sendPOST('/api/v1/restaurants/' . $restaurant['id'] . '/checkin', ['table' => $tableNumber]);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
$I->seeResponseContainsJson(
    [
        'success'        => false,
        'exception_code' => EXCEPTION_CODE_TABLE_MAYBE_EMPTY,
        'message'        => 'Submit check-in if table ' . $tableNumber . ' is empty.'
    ]
);

$I->amGoingTo('send request check-in with param "empty" into restaurant\'s table that has outdated checkins');

$I->sendPOST('/api/v1/restaurants/' . $restaurant['id'] . '/checkin?empty=1', ['table' => $tableNumber]);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);

$I->seeInDatabase(
    'nmtn_restaurant_checkin',
    ['restaurant_id' => $restaurant['id'], 'user_id' => $checkedInUser['id'], 'checked_out' => true]
);
$I->seeInDatabase(
    'nmtn_restaurant_checkin',
    ['restaurant_id' => $restaurant['id'], 'user_id' => $user['id'], 'checked_out' => false]
);
