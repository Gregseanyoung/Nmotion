<?php
/**
 * @author nami
 */

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantTo('register a few restaurants using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->amGoingTo('send new restaurants data to the backend server');


$user1 = $I->addUserFixture(['email' => 'nami8@ciklum.com']);
$address1 = $I->addRestaurantAddressFixture(
    [
        'latitude'     => 30.426783,
        'longitude'    => 50.459444,
        'addressLine1' => 'Degtarivska 53',
        'city'         => 'Kyiv',
        'postal_Code'  => '10020'
    ]
);
$restaurant1 = $I->addRestaurantFixture(
    [
        'email'                 => 'support1@myrestaurant.com',
        'admin_user_id'         => $user1['id'],
        'restaurant_address_id' => $address1['id'],
        'name'                  => 'Yapona',
        'visible'               => 1
    ]
);


$user2 = $I->addUserFixture(['email' => 'nami9@ciklum.com']);
$address2 = $I->addRestaurantAddressFixture(
    [
        'latitude'     => 30.029554,
        'longitude'    => 50.122123,
        'addressLine1' => 'Titova 162',
        'city'         => 'Fastov',
        'postal_Code'  => '52'
    ]
);
$restaurant2 = $I->addRestaurantFixture(
    [
        'email'                 => 'support1@myrestaurant.com',
        'admin_user_id'         => $user2['id'],
        'restaurant_address_id' => $address2['id'],
        'name'                  => 'Hata magnata',
        'visible'               => 1
    ]
);


$user3 = $I->addUserFixture(['email' => 'nami10@ciklum.com']);
$address3 = $I->addRestaurantAddressFixture(
    [
        'latitude'     => 104.291622,
        'longitude'    => 52.27937,
        'addressLine1' => 'Kyivskaya 27',
        'city'         => 'Irkutsk',
        'postal_Code'  => '05-552'
    ]
);
$restaurant3 = $I->addRestaurantFixture(
    [
        'email'                 => 'support1@myrestaurant.com',
        'admin_user_id'         => $user3['id'],
        'restaurant_address_id' => $address3['id'],
        'name'                  => 'Yaki-maki',
        'visible'               => 1
    ]
);


//Pobedi av. 56a, and set radius of search
$param = [
    'query'   => 'don\'tExist',
    'geocode' => '30.437554,50.456493, 50'
];

//search for restaurants with the same beginning of name with default radius=20km
$I->amGoingTo('find restaurant with name "dontExist"');
$I->dontSeeInDatabase('nmtn_restaurant', ['name' => 'don\'tExist']);
$I->sendGET('/api/v1/restaurants/search.json', $param);
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['entries' => []]);
