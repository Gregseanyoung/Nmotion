<?php
/**
 * @author tiger
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('register a new account using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->amGoingTo('send new user data to the backend server');
$params = [
    'email'      => 'user.regist.test@test123456.com',
    'password'   => 'qwerty',
    'firstName' => 'foo',
    'lastName'  => 'bar'
];
$I->sendPOST('/api/v1/users.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_user', ['email' => $params['email'], 'registered' => 1]);
