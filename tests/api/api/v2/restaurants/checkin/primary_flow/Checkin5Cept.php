<?php
/**
 * @author samva <vas@ciklum.com>
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('update check-in in restaurant\'s table being checked in before friend using API');
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

$time        = time() - 10;
$tableNumber = rand(1, 100);
$user        = $I->addAnonymousUserFixture();
$friend      = $I->addAnonymousUserFixture();

foreach ([$user, $friend] as $i => $guest) {
    $I->addRestaurantCheckinFixture(
        [
            'user_id'       => $guest['id'],
            'first_name'    => 'name aka ' . ($i == 0 ? 'user' : 'friend'),
            'restaurant_id' => $restaurant['id'],
            'service_type_id' => RESTAURANT_SERVICE_TYPE_IN_HOUSE,
            'table_number'  => $tableNumber,
            'checked_out'   => false,
            'created_at'    => $time + $i * 10, //VERY IMPORTANT for test, i.e. user aka earlier logged in that friend
            'updated_at'    => $time + $i * 10  //VERY IMPORTANT for test, i.e. user aka earlier logged in that friend
        ]
    );
}

// it is very important to see exactly 1 checked in user (by 'id') in the restaurant
$I->seeInDatabaseNumberOfRows(
    'nmtn_restaurant_checkin',
    1,
    ['restaurant_id' => $restaurant['id'],
        'user_id' => $user['id'],
        'service_type_id' => RESTAURANT_SERVICE_TYPE_IN_HOUSE,
        'table_number' => $tableNumber,
        'checked_out' => false
    ]
);
// it is very important to see exactly 1 checked in friend (by 'id') in the restaurant
$I->seeInDatabaseNumberOfRows(
    'nmtn_restaurant_checkin',
    1,
    [
        'restaurant_id' => $restaurant['id'],
        'user_id' => $friend['id'],
        'service_type_id' => RESTAURANT_SERVICE_TYPE_IN_HOUSE,
        'table_number' => $tableNumber,
        'checked_out' => false
    ]
);


$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);
$I->amGoingTo('send request check-in being already checked in before friend without param "force"');

$I->sendPOST(
    '/api/v2/restaurants/' . $restaurant['id'] . '/checkin',
    ['table' => $tableNumber, 'serviceType' => RESTAURANT_SERVICE_TYPE_IN_HOUSE]
);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);

// it is very important to see exactly 1 checked in user (by 'id') in the restaurant
$I->seeInDatabaseNumberOfRows(
    'nmtn_restaurant_checkin',
    1,
    [
        'restaurant_id' => $restaurant['id'],
        'user_id' => $user['id'],
        'service_type_id' => RESTAURANT_SERVICE_TYPE_IN_HOUSE,
        'table_number' => $tableNumber,
        'checked_out' => false
    ]
);
// it is very important to see exactly 1 checked in friend (by 'id') in the restaurant
$I->seeInDatabaseNumberOfRows(
    'nmtn_restaurant_checkin',
    1,
    [
        'restaurant_id' => $restaurant['id'],
        'user_id' => $friend['id'],
        'service_type_id' => RESTAURANT_SERVICE_TYPE_IN_HOUSE,
        'table_number' => $tableNumber,
        'checked_out' => false
    ]
);
