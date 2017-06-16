<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('delete restaurant through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.restaurant.delete@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email'      => 'backoffice.restaurant.delete@nmotion.pp.ciklum.com',
        ],
        'address'        => []
    ]
);

// successful scenario
$I->amGoingTo('send delete request for one restaurant to the backend server: successful scenario');
$I->seeInDatabase('nmtn_restaurant', ['id' => $restaurant['id']]);
$I->sendDELETE('/backoffice/restaurants/' . $restaurant['id'] . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->dontSeeInDatabase('nmtn_restaurant', ['id' => $restaurant['id']]);
