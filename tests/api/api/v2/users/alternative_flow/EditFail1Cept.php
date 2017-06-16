<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('fail editing other user account using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user1 = $I->addUserFixture(['email' => 'api.v2.user.edit.fail1.user1@nmotion.pp.ciklum.com']);
$user2 = $I->addUserFixture(['email' => 'api.v2.user.edit.fail1.user2@nmotion.pp.ciklum.com']);

$I->willEvaluateAuthorizationToken($user1['email'], $user1['password']);

$params = [
    'firstName' => 'user.edit.test.fail1',
    'password'  => 'passwordedited'
];
$I->sendPUT("/api/v2/users/{$user2['id']}.json", $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_FORBIDDEN);
$I->seeResponseContainsJson(['success' => false]);
