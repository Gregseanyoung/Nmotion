<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('get all menu meals for my restaurant\'s category through backoffice-API');

$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.restaurant.menu.meal.getall.restaurant@nmotion.pp.ciklum.com',
        'name'           => 'backoffice.restaurant.menu.meal.getall.restaurant@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email'      => 'backoffice.restaurant.menu.meal.getall.admin.nmotion@nmotion.pp.ciklum.com',
            'roles'      => ['ROLE_RESTAURANT_ADMIN'],
        ],
        'address'        => [],
        'menuCategories' => [
            0 => [
                'name'      => 'Rolls',
                'menuMeals' => [
                    0 => ['name' => 'Green Dragon roll'],
                    1 => ['name' => 'Red Dragon roll'],
                    2 => ['name' => 'California roll']
                ]
            ]
        ]
    ]
);
$menuCategory = $restaurant['menuCategories'][0];

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// successful scenario
$I->amGoingTo('send get request for menu meals data to the backend server: successful scenario');
$I->sendGET('/backoffice/menucategories/' . $menuCategory['id'] . '/meals.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            ['id' => $menuCategory['menuMeals'][0]['id'], 'position' => 0],
            ['id' => $menuCategory['menuMeals'][1]['id'], 'position' => 1],
            ['id' => $menuCategory['menuMeals'][2]['id'], 'position' => 2]
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(3);
