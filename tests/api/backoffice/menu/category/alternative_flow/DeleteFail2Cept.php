<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('failure when deleting not existing menu category through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'rest.menu.category.deletefail2.test@test123456.com',
        'adminUser' => [
            'email' => 'user.rest.menu.category.deletefail2.test@nmotion.pp.ciklum.com',
            'roles'      => ['ROLE_RESTAURANT_ADMIN']
        ],
        'address'   => []
    ]
);

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

// get failed - requested menu category is not exist
$I->amGoingTo('send delete request for one menu category to the backend server: fail - requested category not found');
$I->sendGET('/backoffice/menucategories/100500.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_NOT_FOUND);
$I->seeResponseContainsJson(['success' => false]);
