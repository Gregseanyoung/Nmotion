<?php
/**
 * @author samva <vas@ciklum.com>
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('check-in into restaurant using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'api.restaurant.checkin1.restaurant-email@nmotion.pp.ciklum.com',
        'adminUser' => [
            'email' => 'api.restaurant.checkin1.admin-email@nmotion.pp.ciklum.com'
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
        'checked_out'   => true
    ]
);

$user = $I->addAnonymousUserFixture();

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$I->amGoingTo('send check-in request to the backend server');

$I->sendPOST('/api/v1/restaurants/' . $restaurant['id'] . '/checkin', ['table' => $tableNumber]);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase(
    'nmtn_restaurant_checkin',
    [
        'restaurant_id' => $restaurant['id'],
        'user_id'       => $user['id'],
        'checked_out'   => false
    ]
);
