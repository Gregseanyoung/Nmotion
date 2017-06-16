<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantTo('get one menu meal for the given menu category through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.restaurant.menu.meal.getone.restaurant.nmotion@ciklum.com',
        'adminUser'      => [
            'email'      => 'backoffice.restaurant.menu.meal.getone.admin.nmotion@ciklum.com',
            'password'   => 'test1234',
            'roles'      => ['ROLE_RESTAURANT_ADMIN'],
        ],
        'address'        => [],
        'menuCategories' => [
            [
                'name'      => 'Rolls',
                'menuMeals' => [
                    ['name' => 'Green Dragon roll']
                ]
            ]
        ]
    ]
);
$menuMeal = $restaurant['menuCategories'][0]['menuMeals'][0];

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// successful scenario
$I->amGoingTo('request one menu meal for the my restaurant\'s category');
$I->sendGET('/backoffice/meals/' . $menuMeal['id'] . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            ['id' => $menuMeal['id'], 'name' => 'Green Dragon roll']
        ]
    ]
);
$I->seeResponseContainsNumberOfEntries(1);
