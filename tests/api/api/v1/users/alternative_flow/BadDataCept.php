<?php
/**
 * @author tiger
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('failure in register a new account using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->amGoingTo('send invalid data for new user to the backend server');
$params = [
    'email'     => 'incorrectemail',
    'password'  => '1234',
    'firstName' => 'foo',
    'lastName'  => 'bar'
];
$I->sendPOST('/api/v1/users.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
$I->seeResponseContainsJson(['success' => false]);
$I->dontSeeInDatabase('nmtn_user', ['email' => $params['email']]);
