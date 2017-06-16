<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('add new meal for selected menu category for my restaurant through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'rest.menu.meal.addmealoptions.test@nmotion.pp.ciklum.com',
        'adminUser' => ['email' => 'user.rest.menu.meal.addmealoptions.test@nmotion.pp.ciklum.com'],
        'address'   => []
    ]
);
$menuCategory = $I->addMenuCategoryFixture(
    [
        'name'          => 'Add Meal and mealoptions Test Successful',
        'restaurant_id' => $restaurant['id']
    ]
);

// successful scenario
$I->amGoingTo('make sure that meal option default id is set when adding meal options');
$params = [
    'name'                 => 'Meal6',
    'description'          => 'Meal6 description',
    'price'                => 1,
    'discountPercent'      => 10,
    'visible'              => true,
    'mealOptions'          => [
      [
         'id'        => '',
         'name'      => 'not default option for meal6',
         'price'     => 30
      ],
      [
         'id'        => '',
         'name'      => 'default option for meal6',
         'price'     => 60,
         'isDefault' => true
      ]
   ],
   'mealOptionDefaultId'   => null
];
$I->dontSeeInDatabase('nmtn_meal', ['name' => $params['name']]);
$I->dontSeeInDatabase('nmtn_meal_option', ['name' => $params['mealOptions'][0]['name']]);
$I->sendPOST('/backoffice/menucategories/' . $menuCategory['id'] . '/meals.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$meal = $I->grabDataFromJsonResponse('entries')[0];
$I->seeInDatabase(
    'nmtn_meal_option',
    [
        'id' => $meal['mealOptionDefaultId'],
        'name' => $params['mealOptions'][1]['name']
    ]
);
$I->seeInDatabase(
    'nmtn_meal',
    [
        'name' => $params['name'],
        'meal_option_default_id' => $meal['mealOptionDefaultId']
    ]
);
