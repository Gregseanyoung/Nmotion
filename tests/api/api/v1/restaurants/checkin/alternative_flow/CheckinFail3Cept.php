<?php
/**
 * @author samva <vas@ciklum.com>
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('fail check-in into invisible restaurant using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addAnonymousUserFixture();

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'api.restaurant.fail3-checkin.restaurant-email@nmotion.pp.ciklum.com',
        'adminUser' => [
            'email' => 'api.restaurant.fail3-checkin.admin-email@nmotion.pp.ciklum.com'
        ],
        'address'   => [],
        'visible'   => 0,
    ]
);

$I->amGoingTo('send check-in request to the backend server');

$I->dontSeeInDatabase('nmtn_restaurant_checkin', ['restaurant_id' => $restaurant['id']]);

$I->sendPOST('/api/v1/restaurants/' . $restaurant['id'] . '/checkin', ['table' => rand(1, 100)]);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_NOT_FOUND);
$I->seeResponseContainsJson(
    [
        'success' => false,
        'message' => 'Restaurant not found.'
    ]
);
