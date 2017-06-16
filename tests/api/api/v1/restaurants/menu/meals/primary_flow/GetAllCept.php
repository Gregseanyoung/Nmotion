<?php

$response_entry_api_get_menucategory_meals = [
    // used on mobile
    'id',
    'name',
    'description',
    'price',
    'discountPercent',
    'discountPrice',
    // 'logoAsset.url',                         just groups: [api.entity, backoffice]
    'thumbLogoAsset.url',
    // 'mealOptionDefaultId',                   just groups: [api.entity, backoffice.entity]

    // just returned
    'mealDiscountPercent',
    'position',

    // 'mealOptionDefault.id',                  just groups: [api.entity, backoffice.entity]
    // 'mealOptionDefault.name',                just groups: [api.entity, backoffice.entity]
    // 'mealOptionDefault.price',               just groups: [api.entity, backoffice.entity]
    // 'mealOptionDefault.discountPrice',       just groups: [api.entity, backoffice.entity]

    // 'mealOptions.[].id',                     just groups: [api.entity, backoffice.entity]
    // 'mealOptions.[].name',                   just groups: [api.entity, backoffice.entity]
    // 'mealOptions.[].price',                  just groups: [api.entity, backoffice.entity]
    // 'mealOptions.[].discountPrice',          just groups: [api.entity, backoffice.entity]

    // 'mealExtraIngredients.[].id',            just groups: [api.entity, backoffice.entity]
    // 'mealExtraIngredients.[].name',          just groups: [api.entity, backoffice.entity]
    // 'mealExtraIngredients.[].price',         just groups: [api.entity, backoffice.entity]
    // 'mealExtraIngredients.[].discountPrice'  just groups: [api.entity, backoffice.entity]
];

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('get available meals in category through API');
$I->haveHttpHeader('Content-Type', 'application/json');

$time = (time() + date('Z')) % 86400;

$restaurant = $I->addRestaurantFixture(
    [
        'visible'        => true,
        'menuCategories' => [
            [
                'name'      => 'Target Category',
                'visible'   => true,
                'time_from' => $time - 30,
                'time_to'   => $time + 30,
                'menuMeals' => [
                    [
                        'name'           => 'Visible Meal Without TimeFrame',
                        'logoAsset'      => [],
                        'thumbLogoAsset' => [],
                        'is_default'     => true,
                        'visible'        => true,
                        'time_from'      => null,
                        'time_to'        => null
                    ],
                    [
                        'name'           => 'Hidden Meal Without TimeFrame',
                        'logoAsset'      => [],
                        'thumbLogoAsset' => [],
                        'visible'        => false,
                        'time_from'      => null,
                        'time_to'        => null
                    ],
                    [
                        'name'           => 'Visible Meal With TimeFrame Of Full Availability',
                        'logoAsset'      => [],
                        'thumbLogoAsset' => [],
                        'visible'        => true,
                        'time_from'      => 0,
                        'time_to'        => 0
                    ],
                    [
                        'name'           => 'Hidden Meal With TimeFrame Of Full Availability',
                        'logoAsset'      => [],
                        'thumbLogoAsset' => [],
                        'visible'        => false,
                        'time_from'      => 0,
                        'time_to'        => 0
                    ],
                    [
                        'name'           => 'Visible Meal Within TimeFrame',
                        'visible'        => true,
                        'logoAsset'      => [],
                        'thumbLogoAsset' => [],
                        'time_from'      => $time - 30,
                        'time_to'        => $time + 30
                    ],
                    [
                        'name'           => 'Visible Meal Not Within TimeFrame',
                        'visible'        => true,
                        'logoAsset'      => [],
                        'thumbLogoAsset' => [],
                        'time_from'      => 0,
                        'time_to'        => 1
                    ],
                    [
                        'name'           => 'Hidden Meal Within TimeFrame',
                        'visible'        => false,
                        'logoAsset'      => [],
                        'thumbLogoAsset' => [],
                        'time_from'      => $time - 30,
                        'time_to'        => $time + 30
                    ],
                ]
            ],
            [
                'name'      => 'Other Category',
                'visible'   => true,
                'time_from' => $time - 30,
                'time_to'   => $time + 30,
                'menuMeals' => [
                    [
                        'name'           => 'Visible Meal Without TimeFrame 2',
                        'logoAsset'      => [],
                        'thumbLogoAsset' => [],
                        'is_default'     => true,
                        'visible'        => true
                    ],
                    [
                        'name'           => 'Visible Meal Within TimeFrame 2',
                        'visible'        => true,
                        'logoAsset'      => [],
                        'thumbLogoAsset' => [],
                        'time_from'      => $time - 30,
                        'time_to'        => $time + 30
                    ],
                ]
            ]
        ]
    ]
);

$menuCategory = $restaurant['menuCategories'][0];

// successful scenario
$I->amGoingTo('request menu meals of particular category and restaurant');
$I->sendGET('/api/v1/menucategories/' . $menuCategory['id'] . '/meals.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            ['name' => 'Visible Meal With TimeFrame Of Full Availability'],
            ['name' => 'Visible Meal Within TimeFrame']
        ]
    ]
);
$I->seeResponseEntriesHasFields($response_entry_api_get_menucategory_meals);
$I->dontSeeResponseContainsJson(['name' => ['name' => 'Visible Meal Without TimeFrame']]);
$I->dontSeeResponseContainsJson(['name' => ['name' => 'Visible Meal Not Within TimeFrame']]);
$I->dontSeeResponseContainsJson(['name' => ['name' => 'Hidden Meal Without TimeFrame']]);
$I->dontSeeResponseContainsJson(['name' => ['name' => 'Hidden Meal Within TimeFrame']]);
$I->dontSeeResponseContainsJson(['name' => ['name' => 'Hidden Meal With TimeFrame Of Full Availability']]);
$I->dontSeeResponseContainsJson(['name' => ['name' => 'Visible Meal Without TimeFrame 2']]);
$I->dontSeeResponseContainsJson(['name' => ['name' => 'Visible Meal Within TimeFrame 2']]);
