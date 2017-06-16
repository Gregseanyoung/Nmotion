<?php
/**
 * @author tiger
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('authenticate in API using device identity');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->haveHttpHeader('Auth', 'DeviceToken 1234567890');

$I->amGoingTo('send request to /api/v2/users/me.json signed with device identity token');
$I->sendGET('/api/v2/users/me.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
