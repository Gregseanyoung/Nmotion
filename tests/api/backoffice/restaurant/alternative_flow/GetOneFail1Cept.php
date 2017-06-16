<?php

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('fail to get restaurant as other RA through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant1 = $I->addRestaurantFixture();
$restaurant2 = $I->addRestaurantFixture();

$I->willEvaluateAuthorizationToken($restaurant1['adminUser']['email'], $restaurant1['adminUser']['password']);

// fail - other RA
$I->amGoingTo('as other RA send get request for one restaurant to the backend server: fail - other RA');
$I->sendGET('/backoffice/restaurants/' . $restaurant2['id'] . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_FORBIDDEN);
$I->seeResponseContainsJson(['success' => false]);
