<?php
/**
 * @author seka
 */

$I = new ApiGuy($scenario);

$I->am('Guest');
$I->wantToTest('auto check-in to restaurant FB place using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->haveFacebookTestUserAccount();
$accessToken = $I->grabFacebookTestUserAccessToken();

$I->haveHttpHeader('Auth', 'FacebookToken ' . $accessToken);

$restaurant = $I->addRestaurantFixture(
    [
        'visible'   => 1,
        'facebook_place_id' => '167724369950862',
        'address'   => [
            'latitude' => 56.827902061825,
            'longitude' => 9.8182046961853
        ],
        'operationTimes' => [
            // per day from 00:00:00 to 23:59:59 :)
            ['time_from' => 0, 'time_to' => 86399],
            ['time_from' => 0, 'time_to' => 86399],
            ['time_from' => 0, 'time_to' => 86399],
            ['time_from' => 0, 'time_to' => 86399],
            ['time_from' => 0, 'time_to' => 86399],
            ['time_from' => 0, 'time_to' => 86399],
            ['time_from' => 0, 'time_to' => 86399]
        ]
    ]
);

$I->amGoingTo('send check-in request with autocheckin to FB restaurant place to the backend server');

$I->dontSeeInDatabase('nmtn_restaurant_checkin', ['restaurant_id' => $restaurant['id']]);
$params = ['table' => 1];
$I->sendPOST('/api/v1/restaurants/' . $restaurant['id'] . '/checkin?fbRestaurantCheckin=1', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_restaurant_checkin', ['restaurant_id' => $restaurant['id'], 'checked_out' => 0]);
$I->seePostOnFacebookWithAttachedPlace('167724369950862');
