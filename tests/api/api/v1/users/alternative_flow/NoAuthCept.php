<?php
/**
 * @author tiger
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('failure in getting user entity data using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->amGoingTo('send request to /api/v1/users/me.json without authorization token');
$I->sendGET('/api/v1/users/me.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_UNAUTHORIZED);
$I->seeResponseContainsJson(['success' => false]);
