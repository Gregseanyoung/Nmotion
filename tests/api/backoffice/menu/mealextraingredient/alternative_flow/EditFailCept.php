<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('failure when editing not existing meal extra ingredient through backoffice-API');

$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'          => 'backoffice.meal.extraingredient.editfail1@nmotion.pp.ciklum.com',
        'adminUser'      => [
            'email'      => 'backoffice.meal.extraingredient.editfail1@nmotion.pp.ciklum.com',
        ],
        'address'        => []
    ]
);

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// editing failed - requested meal extraingredient is not exist
$I->amGoingTo('send put request for not existing meal extra ingredient to the backend server');
$I->sendPUT('/backoffice/mealextraingredients/100500.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_NOT_FOUND);
$I->seeResponseContainsJson(['success' => false]);
