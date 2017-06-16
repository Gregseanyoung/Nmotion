<?php

$response_entry_api_get_menucategory = [
    // used on mobile
    'id',
    'name',

    // just returned
    'discountPercent',
    'position'
];

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('get the restaurant menu category through API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'api.rest.menu.categories.getOne.test@nmotion.pp.ciklum.com',
        'visible'        => true,
        'adminUser'      => [
            'email' => 'api.rest.menu.categories.getOne.test@nmotion.pp.ciklum.com'
        ],
        'address'        => [],
        'menuCategories' => [
            ['name' => 'Test Foobar Category', 'visible' => true],
            ['name' => 'Test Target Category', 'visible' => true],
            ['name' => 'Test Hidden Category', 'visible' => false],
        ]
    ]
);

// successful scenario
$I->amGoingTo('request menu category for the restaurant');
$I->sendGET(
    '/api/v1/menucategories/' . $restaurant['menuCategories'][1]['id'] . '.json'
);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            ['name' => $restaurant['menuCategories'][1]['name']]
        ]
    ]
);
$I->seeResponseEntriesHasFields($response_entry_api_get_menucategory);
