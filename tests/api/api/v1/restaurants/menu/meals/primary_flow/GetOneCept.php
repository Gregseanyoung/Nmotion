<?php

$response_entry_api_get_meal = [
    // used on mobile
    'id',
    'name',
    'description',
    'price',
    'discountPercent',
    'discountPrice',
    'logoAsset.url',
    // 'thumbLogoAsset.url',        just groups: [api.list, backoffice]
    'mealOptionDefaultId',

    // just returned
    'mealDiscountPercent',
    // 'position',                  just groups: [api.list, backoffice]

    'mealOptionDefault.id',
    'mealOptionDefault.name',
    'mealOptionDefault.price',
    'mealOptionDefault.discountPrice',

    'mealOptions.[].id',
    'mealOptions.[].name',
    'mealOptions.[].price',
    'mealOptions.[].discountPrice',

    'mealExtraIngredients.[].id',
    'mealExtraIngredients.[].name',
    'mealExtraIngredients.[].price',
    'mealExtraIngredients.[].discountPrice'
];

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('get one menu meal for the given menu category through API');
$I->haveHttpHeader('Content-Type', 'application/json');

$time = (time() + date('Z')) % 86400;

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'api.rest.menu.meal.getOne.test@nmotion.pp.ciklum.com',
        'visible'        => true,
        'adminUser'      => [
            'email' => 'api.rest.menu.meal.getOne.test@nmotion.pp.ciklum.com'
        ],
        'address'        => [],
        'logoAsset'      => [],
        'menuCategories' => [
            [
                'name'      => 'Test Category',
                'visible'   => true,
                'time_from' => $time - 10,
                'time_to'   => $time + 10,
                'menuMeals' => [
                    [
                        'name'                 => 'Target Meal',
                        'visible'              => true,
                        'logoAsset'            => [],
                        'thumbLogoAsset'       => [],
                        'mealOptions'          => [
                            [
                                'name' => 'not default option'
                            ],
                            [
                                'name'       => 'default option',
                                'is_default' => true
                            ],
                        ],
                        'mealExtraIngredients' => [
                            ['name' => 'Extra ingredient 1'],
                            ['name' => 'Extra ingredient 2']
                        ]
                    ]
                ]
            ]
        ]
    ]
);

$menuCategory = $restaurant['menuCategories'][0];
$menuMeal     = $menuCategory['menuMeals'][0];

// successful scenario
$I->amGoingTo('request menu meal for the category');
$I->sendGET(
    '/api/v1/meals/' . $menuMeal['id']
);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            [
                'name'                 => 'Target Meal',
                'mealExtraIngredients' => [
                    ['name' => 'Extra ingredient 1'],
                    ['name' => 'Extra ingredient 2']
                ]
            ]
        ]
    ]
);

$I->seeResponseEntriesHasFields($response_entry_api_get_meal);
