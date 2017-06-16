<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('failure to add new config parameter through backoffice-API - access denied');
$I->haveHttpHeader('Content-Type', 'application/json');
$I->haveHttpHeader('Auth', 'DeviceToken ' . md5(microtime()));

// adding failed - wrong user role
$I->amGoingTo('send new config parameter data to the backend server: adding failed - access denied');
$params = [
    "name"     => "backoffice.config.addfail1",
    "value" => 28800,
    "description"   => "Just for testing purposes"
];
$I->sendPOST('/backoffice/configs.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_FORBIDDEN);
