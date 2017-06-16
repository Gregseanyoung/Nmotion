<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('get list of config parameters using API');
$I->haveHttpHeader('Content-Type', 'application/json');
$I->haveHttpHeader('Auth', 'DeviceToken ' . md5(microtime()));

$I->addConfigFixture(
    [
        'name'  => 'api.config.list@nmotion.pp.ciklum.com',
        'value' => 'api.config.list@nmotion.pp.ciklum.com'
    ]
);

$I->sendGET('/api/v1/config.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
