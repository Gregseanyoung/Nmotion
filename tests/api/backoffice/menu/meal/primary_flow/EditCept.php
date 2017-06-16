<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('edit existing meal in menu category for my restaurant through backoffice-API');

$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.restaurant.menu.meal.edit@nmotion.pp.ciklum.com',
        'name'           => 'backoffice.restaurant.menu.meal.edit@nmotion.pp.ciklum.com',
        'visible'        => true,
        'adminUser'      => [
            'email'      => 'backoffice.restaurant.menu.meal.edit@nmotion.pp.ciklum.com',
            'roles'      => ['ROLE_RESTAURANT_ADMIN'],
        ],
        'address'        => [],
        'menuCategories' => [
            0 => [
                'name'      => 'Rolls',
                'visible'   => true,
                'menuMeals' => [
                    0 => ['name' => 'Green Dragon roll', 'position' => 0],
                    1 => ['name' => 'Red Dragon roll', 'position' => 1],
                    2 => ['name' => 'California roll', 'position' => 2]
                ]
            ]
        ]
    ]
);

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

$I->amGoingTo('check that after editing meal position other meals positions are appropriately reordered');

$params = [
    "name"        => 'ChangedName',
    "description" => 'ChangedDescription',
    "price"       => 100500,       // should be replaced with net price calculated from priceIncludingTax
    "priceIncludingTax" => 125.03,
    "discountPercent" => 0,
    "position"    => 2,
    "visible"     => true
];
$I->sendPUT('/backoffice/meals/' . $restaurant['menuCategories'][0]['menuMeals'][1]['id'] . '.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_meal', ['name' => 'Green Dragon roll', 'position' => 0]);
$I->seeInDatabase('nmtn_meal', ['name' => 'California roll', 'position' => 1]);
$I->seeInDatabase('nmtn_meal', ['name' => $params['name'], 'position' => 2, 'price' => 100.024]);
