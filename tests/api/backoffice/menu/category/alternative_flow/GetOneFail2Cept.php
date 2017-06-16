<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest(
    'failure when getting one menu category for my restaurant through backoffice-API'
    . ' - requested menu category is not exist'
);
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

// get failed - requested menu category is not exist
$I->amGoingTo('send get request for one menu category data to the backend server: requested category is not exist');
$I->sendGET('/backoffice/menucategories/100500.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_NOT_FOUND);
$I->seeResponseContainsJson(['success' => false]);
