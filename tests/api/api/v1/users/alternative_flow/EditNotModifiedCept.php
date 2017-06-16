<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('edit user account using API - not modified data');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addUserFixture(['email' => 'api.user.edit.not.modified@nmotion.pp.ciklum.com']);

$I->willEvaluateAuthorizationToken($user['username'], $user['password']);

$I->sendPUT("/api/v1/users/{$user['id']}.json", null);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_NOT_MODIFIED);
