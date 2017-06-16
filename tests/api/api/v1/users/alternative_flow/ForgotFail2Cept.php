<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('fail sending forgot password using API - user not found');
$I->haveHttpHeader('Content-Type', 'application/json');

$params = [
    'email' => 'no.such.user@exists.com'
];
$I->sendPOST("/api/v1/users/forgot.json", $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_NOT_FOUND);
$I->seeResponseContainsJson(['success' => false]);
