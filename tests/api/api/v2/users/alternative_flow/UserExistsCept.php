<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('fail registering a new account using API - email already exists');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->addUserFixture(['email' => 'api.v2.user.regist.fail.email.exists@nmotion.pp.ciklum.com']);

$params = [
    'email'      => 'api.v2.user.regist.fail.email.exists@nmotion.pp.ciklum.com',
    'password'   => 'qwerty',
    'firstName' => 'foo',
    'lastName'  => 'bar'
];
$I->seeInDatabase('nmtn_user', ['email' => $params['email']]);
$I->sendPOST('/api/v2/users.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
$I->seeResponseContainsJson(['success' => false]);
