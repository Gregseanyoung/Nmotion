<?php
/**
 * @author samva <vas@ciklum.com>
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('check-in into restaurant\'s table that has outdated checkins with "fresh" order using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'visible'        => 1,
        'menuCategories' => [
            [
                'name'      => 'Pizzas',
                'menuMeals' => [
                    ['name' => 'Pizza standard']
                ]
            ]
        ]
    ]
);

$time          = time();
$outdatedTime  = $time - 3600 * 24;
$tableNumber   = rand(1, 100);
$checkedInUser = $I->addAnonymousUserFixture();

$checkinCheckedInUser = $I->addRestaurantCheckinFixture(
    [
        'user_id'       => $checkedInUser['id'],
        'restaurant_id' => $restaurant['id'],
        'service_type_id' => RESTAURANT_SERVICE_TYPE_IN_HOUSE,
        'table_number'  => $tableNumber,
        'checked_out'   => false,
        'created_at'    => $outdatedTime,
        'updated_at'    => $outdatedTime
    ]
);
$order                = $I->addOrderFixture(
    [
        'restaurant_id' => $restaurant['id'],
        'user_id'       => $checkedInUser['id'],
        'service_type_id' => RESTAURANT_SERVICE_TYPE_IN_HOUSE,
        'table_number'  => $checkinCheckedInUser['tableNumber'],
        'orderMeals'    => [
            ['meal_id' => $restaurant['menuCategories'][0]['menuMeals'][0]['id']]
        ],
        'created_at'    => $time,
        'updated_at'    => $time
    ]
);

$I->seeInDatabase(
    'nmtn_restaurant_checkin',
    ['restaurant_id' => $restaurant['id'], 'user_id' => $checkedInUser['id'], 'checked_out' => false]
);

$user = $I->addAnonymousUserFixture();
$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);

$I->amGoingTo('send request check-in without param "force" into restaurant\'s table that has "fresh" order');

$I->sendPOST(
    '/api/v2/restaurants/' . $restaurant['id'] . '/checkin',
    ['table' => $tableNumber, 'serviceType' => RESTAURANT_SERVICE_TYPE_IN_HOUSE]
);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CONFLICT);
$I->seeResponseContainsJson(
    [
        'success' => false,
        'message' => 'Table ' . $tableNumber . ' is not empty. Join them? Or change table number.'
    ]
);

$I->amGoingTo('send request check-in with param "force" into restaurant\'s table that has "fresh" order');

$I->sendPOST(
    '/api/v2/restaurants/' . $restaurant['id'] . '/checkin?force=1',
    ['table' => $tableNumber, 'serviceType' => RESTAURANT_SERVICE_TYPE_IN_HOUSE]
);
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
