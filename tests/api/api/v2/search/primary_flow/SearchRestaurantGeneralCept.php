<?php
/**
 * @author vas
 */

$response_entry_api_get_search = [
    'id',
    'name',
    'fullDescription',
    'inHouse',
    'takeaway',
    'roomService',
    'phone',
    'siteUrl',
    'feedbackUrl',
    'checkOutTime',
    'address.longitude',
    'address.latitude',
    'address.addressLine1',
    'address.postalCode',
    'isOpen',
    'distance'
];

$I = new ApiGuy($scenario);
$I->am('Anonymous');
$I->wantTo('search restaurants by name, meal\'s name and meal\'s description');

$position_latitude  = -42.879612; // Hobart, Tasmanian island, Australia
$position_longitude = 147.323399; // Hobart, Tasmanian island, Australia

$names = [
    0 => 'RestaurantAAA',
    1 => 'RestaurantBBB',
    2 => 'RestaurantCCC',
    3 => 'RestaurantDDD',
    4 => 'RestEEE',
    5 => 'RestFFF',
    6 => 'RestGGG',
    7 => 'RestIII',
    8 => 'RestFarther20km'
];

for ($i = 0; $i < 9; $i++) {
    $email = 'api.v2.vas.restaurant.search.' . $i . '@ciklum.com';

    $delta = ((int) ($i / 4) + 1) * 0.08;

    $restaurant = $I->addRestaurantFixture(
        [
            'email'          => 'api.v2.support@restaurant' . $i . '.fake.com',
            'name'           => $names[$i],
            'visible'        => in_array($i, [0, 1, 2, 3, 5, 8, 9]),
            'adminUser'      => [
                'email'      => $email,
                'first_name' => 'Firstname' . $i,
                'last_name'  => 'Lastname' . $i,
                'password'   => 'test',
                'roles'      => ['ROLE_RESTAURANT_ADMIN'],
            ],
            'address'        => [
                'latitude'      => $position_latitude + (($i + 1) % 2) * ($i % 4 == 0 ? 1 : -1) * $delta,
                'longitude'     => $position_longitude + ($i % 2) * ($i % 4 == 1 ? 1 : -1) * $delta,
                'address_line1' => 'Random address line #' . $i,
                'city'          => 'Kyiv',
                'postal_code'   => '1000' . $i
            ],
            'menuCategories' => [
                [
                    'name'      => 'Drinks',
                    'visible'   => in_array($i, [0, 2, 4, 6, 8]),
                    'menuMeals' => [
                        ['name' => 'Pepsi 0.3L', 'visible' => true],
                        ['name' => 'Pepsi 0.5L', 'visible' => true],
                        ['name' => 'Pepsi 1.0L', 'visible' => true],
                        ['name' => 'Beer Leffe Blonde 0.3', 'visible' => true],
                        ['name' => 'Beer Leffe Blonde 0.5', 'visible' => true],
                        ['name' => 'Beer Leffe Brune 0.3', 'visible' => true],
                        ['name' => 'Beer Leffe Brune 0.5', 'visible' => true],
                        ['name' => 'Coca-Cola Light 2.0L', 'visible' => $i == 0], // visible only for 0
                    ]
                ],
                [
                    'name'      => 'Rolls',
                    'visible'   => in_array($i, [1, 2, 4, 5, 7, 8]),
                    'menuMeals' => [
                        ['name' => 'Green Dragon roll', 'visible' => in_array($i, [0, 2, 3, 4, 6, 7])],
                        ['name' => 'Red Dragon roll', 'visible' => in_array($i, [1, 3, 5, 7])],
                        ['name' => 'California roll', 'visible' => true]
                    ]
                ]
            ]
        ]
    );
}

$I->amGoingTo('find closest restaurants');

$I->sendGET(
    '/api/v2/restaurants/search.json',
    ['geocode' => $position_latitude . ',' . $position_longitude]
);

$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsJson(
    [
        'entries' => [
            ['name' => 'RestaurantBBB'],
            ['name' => 'RestaurantAAA'],
            ['name' => 'RestaurantCCC'],
            ['name' => 'RestFFF']
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(5);
$I->seeResponseEntriesHasFields($response_entry_api_get_search);

$I->amGoingTo('Find closest restaurants with DISTANCE = 50');

$I->sendGET(
    '/api/v2/restaurants/search.json',
    ['geocode' => $position_latitude . ',' . $position_longitude . ',50']
);

$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsJson(
    [
        'entries' => [
            ['name' => 'RestaurantBBB'],
            ['name' => 'RestaurantAAA'],
            ['name' => 'RestaurantCCC'],
            ['name' => 'RestFFF'],
            ['name' => 'RestFarther20km']
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(5);
$I->seeResponseEntriesHasFields($response_entry_api_get_search);

$I->amGoingTo('Find closest restaurants with name like RestFFF');

$I->sendGET(
    '/api/v2/restaurants/search.json',
    ['query' => 'RestFFF', 'geocode' => $position_latitude . ',' . $position_longitude]
);

$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsJson(
    [
        'entries' => [
            ['name' => 'RestFFF']
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(1);
$I->seeResponseEntriesHasFields($response_entry_api_get_search);

$I->amGoingTo('Find closest restaurants with meal like Coca-Cola Light 2.0L');

$I->sendGET(
    '/api/v2/restaurants/search.json',
    ['query' => 'Coca-Cola Light 2.0L', 'geocode' => $position_latitude . ',' . $position_longitude]
);

$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsJson(
    [
        'entries' => [
            ['name' => 'RestaurantAAA']
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(1);

$I->amGoingTo('Find closest restaurants with meal like Red Dragon');
$I->sendGET(
    '/api/v2/restaurants/search.json',
    ['query' => 'Red Dragon', 'geocode' => $position_latitude . ',' . $position_longitude]
);

$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsJson(
    [
        'entries' => [
            ['name' => 'RestaurantBBB'],
            ['name' => 'RestFFF']
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(2);
$I->seeResponseEntriesHasFields($response_entry_api_get_search);

$I->amGoingTo('Find closest restaurants with meal like Green Dragon');
$I->sendGET(
    '/api/v2/restaurants/search.json',
    ['query' => 'Green Dragon', 'geocode' => $position_latitude . ',' . $position_longitude]
);

$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsJson(
    [
        'entries' => [
            ['name' => 'RestaurantCCC']
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(1);
$I->seeResponseEntriesHasFields($response_entry_api_get_search);
