<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('failure when adding new menu category to restaurant with incorrect data through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'rest.menu.category.add.bad3test@nmotion.pp.ciklum.com',
        'adminUser' => ['email' => 'user.rest.menu.category.add.bad3test@nmotion.pp.ciklum.com'],
        'address'   => []
    ]
);

// validation failed - incorrect data
$params = [
    "name"       => 'Incorrect Data',
    "timeFrom"   => -8,
    "timeTo"     => 100500100,
    "visible"    => 'incorrect',
    "extraField" => true
];
$I->amGoingTo('send new menu category data to the backend server: validation failed - incorrect data');
$I->sendPOST('/backoffice/restaurants/' . $restaurant['id'] . '/menucategories.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
