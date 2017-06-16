<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('failure to add new config parameter through backoffice-API - validation failed');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

// validation failed - incorrect data
$I->amGoingTo('send new config parameter data to the backend server: validation failed - incorrect data');
$params = [
    "name"     => "backoffice.config.addfail2",
    "value" => null,
    "description"   => "Just for testing purposes",
    "extra_field" => "incorrect"
];
$I->sendPOST('/backoffice/configs.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
