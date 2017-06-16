<?php
/**
 * @author tiger
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('edit user account using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addUserFixture(['email' => 'api.user.edit.test@nmotion.pp.ciklum.com']);

$I->willEvaluateAuthorizationToken($user['username'], $user['password']);

$I->amGoingTo('send changed user data to the backend server');
$params = [
    'email'     => 'api.user.edit.email.edited@nmotion.pp.ciklum.com',
    'firstName' => 'user.edit.test.foo',
    'lastName'  => 'user.edit.test.bar',
    'password'  => 'passwordedited'
];
$I->dontSeeInDatabase('nmtn_user', ['first_name' => $params['firstName']]);
$I->seeInDatabase('nmtn_user', ['email' => $user['email']]);
$I->sendPUT("/api/v1/users/{$user['id']}.json", $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_user', ['email' => $params['email'], 'first_name' => $params['firstName']]);
$I->dontSeeInDatabase('nmtn_user', ['email' => $user['email']]);
