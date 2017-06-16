<?php
$response_entry_api_get_restaurant_menucategories = [
    // used on mobile
    'id',
    'name',

    // just returned
    'discountPercent',
    'position'
];

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('get available menu categories for the restaurant through API');
$I->haveHttpHeader('Content-Type', 'application/json');

$time = (time() + date('Z')) % 86400;

$restaurant1 = $I->addRestaurantFixture(
    [
        'email'     => 'api.rest.menu.categories.getAll.test@nmotion.pp.ciklum.com',
        'visible'   => true,
        'adminUser' => [
            'email' => 'api.rest.menu.categories.getAll.test@nmotion.pp.ciklum.com'
        ],
        'address'   => [],
        'menuCategories' => [
            [
                'name'    => 'Hidden Category',
                'visible' => false
            ],
            [
                'name'      => 'Visible WithIn TimeFrame',
                'visible'   => true,
                // +- because time between jenkins-server and nmotion-server is not in sync
                'time_from' => $time - 30,
                'time_to'   => $time + 30
            ],
            [
                'name'      => 'Visible Not WithIn TimeFrame',
                'visible'   => true,
                'time_from' => 0,
                'time_to'   => 1
            ],
            [
                'name'      => 'Visible WithIn TimeFrame 24h',
                'visible'   => true,
                'time_from' => 0,
                'time_to'   => 0
            ],
        ]
    ]
);

$I->amGoingTo('add an another fixture to test that I get only categories for the restaurant I have requested');

$restaurant2 = $I->addRestaurantFixture(
    [
        'visible'   => true,
        'menuCategories' => [
            ['name' => 'Hidden Category 2', 'visible' => false],
            ['name' => 'Visible WithIn TimeFrame 2', 'visible' => 1, 'time_from' => $time, 'time_to' => $time + 30],
            ['name' => 'Visible Not WithIn TimeFrame 2', 'visible' => true, 'time_from' => 0, 'time_to' => 1],
        ]
    ]
);

$I->amGoingTo('request menu categories for the restaurant 1');
$I->sendGET('/api/v1/restaurants/' . $restaurant1['id'] . '/menucategories.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            ['name' => 'Visible WithIn TimeFrame'],
            ['name' => 'Visible WithIn TimeFrame 24h']
        ]
    ]
);
$I->dontSeeResponseContainsJson(['name' => 'Hidden Category']);
$I->dontSeeResponseContainsJson(['name' => 'Visible Not WithIn TimeFrame']);
$I->dontSeeResponseContainsJson(['name' => 'Hidden Category 2']);
$I->dontSeeResponseContainsJson(['name' => 'Visible WithIn TimeFrame 2']);
$I->dontSeeResponseContainsJson(['name' => 'Visible Not WithIn TimeFrame 2']);
$I->seeResponseEntriesHasFields($response_entry_api_get_restaurant_menucategories);
