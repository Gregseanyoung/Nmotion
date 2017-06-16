<?php
/**
 * @author samva <vas@ciklum.com>
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('check-in into restaurant\'s table that is actually not empty using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'api.restaurant.checkin2.restaurant-email@nmotion.pp.ciklum.com',
        'adminUser' => [
            'email' => 'api.restaurant.checkin2.admin-email@nmotion.pp.ciklum.com'
        ],
        'address'   => [],
        'visible'   => 1
    ]
);

$tableNumber   = rand(1, 100);
$checkedInUser = $I->addAnonymousUserFixture();
$I->addRestaurantCheckinFixture(
    [
        'user_id'       => $checkedInUser['id'],
        'restaurant_id' => $restaurant['id'],
        'table_number'  => $tableNumber,
        'checked_out'   => false
    ]
);

$I->seeInDatabase(
    'nmtn_restaurant_checkin',
    [
        'restaurant_id' => $restaurant['id'],
        'user_id'       => $checkedInUser['id'],
        'checked_out'   => false
    ]
);

$user = $I->addAnonymousUserFixture();
$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$I->amGoingTo('send request check-in without param "force" into restaurant\'s table that is actually not empty');

$I->sendPOST('/api/v1/restaurants/' . $restaurant['id'] . '/checkin', ['table' => $tableNumber]);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CONFLICT);
$I->seeResponseContainsJson(
    [
        'success' => false,
        'message' => 'Table ' . $tableNumber . ' is not empty. Join them? Or change table number.'
    ]
);

$I->amGoingTo('send request check-in with param "force" into restaurant\'s table that is actually not empty');

$I->sendPOST('/api/v1/restaurants/' . $restaurant['id'] . '/checkin?force=1', ['table' => $tableNumber]);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);

$I->seeInDatabase(
    'nmtn_restaurant_checkin',
    ['restaurant_id' => $restaurant['id'], 'user_id' => $checkedInUser['id'], 'checked_out' => false]
);
$I->seeInDatabase(
    'nmtn_restaurant_checkin',
    ['restaurant_id' => $restaurant['id'], 'user_id' => $user['id'], 'checked_out' => false]
);
