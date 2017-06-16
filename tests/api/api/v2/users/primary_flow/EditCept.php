<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('edit user account using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addUserFixture(['email' => 'api.v2.user.edit.test@nmotion.pp.ciklum.com']);

$I->willEvaluateAuthorizationToken($user['username'], $user['password']);

$I->amGoingTo('send changed user data to the backend server');
$params = [
    'email'     => 'api.v2.user.edit.email.edited@nmotion.pp.ciklum.com',
    'firstName' => 'api.v2.user.edit.test.foo',
    'lastName'  => 'api.v2.user.edit.test.bar',
    'password'  => 'passwordedited'
];
$I->dontSeeInDatabase('nmtn_user', ['first_name' => $params['firstName']]);
$I->seeInDatabase('nmtn_user', ['email' => $user['email']]);
$I->sendPUT("/api/v2/users/{$user['id']}.json", $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_user', ['email' => $params['email'], 'first_name' => $params['firstName']]);
$I->dontSeeInDatabase('nmtn_user', ['email' => $user['email']]);

$I->amGoingTo('send changed user data without password to the backend server');
$I->willEvaluateAuthorizationToken($params['email'], $params['password']);
$params2 = [
    'email'     => 'api.v2.user.edit.email.edited2@nmotion.pp.ciklum.com',
    'firstName' => 'api.v2.user.edit.first2',
    'lastName'  => 'api.v2.user.edit.last2',
    'password'  => ''
];
$I->seeInDatabase('nmtn_user', ['email' => $params['email']]);
$I->sendPUT("/api/v2/users/{$user['id']}.json", $params2);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_user', ['email' => $params2['email'], 'first_name' => $params2['firstName']]);
$I->dontSeeInDatabase('nmtn_user', ['email' => $params['email']]);
