<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('fail sending forgot password using API - validation failed');
$I->haveHttpHeader('Content-Type', 'application/json');

$params = [
    'email' => 'incorrect'
];
$I->sendPOST("/api/v1/users/forgot.json", $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
$I->seeResponseContainsJson(['success' => false]);
