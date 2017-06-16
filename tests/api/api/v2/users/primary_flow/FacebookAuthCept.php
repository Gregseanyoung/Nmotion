<?php
/**
 * @author samva
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('sign up using Facebook');

$I->amGoingTo('sign up with wrong facebook token');
$I->haveHttpHeader('Content-Type', 'application/json');
$I->haveHttpHeader('Auth', 'FacebookToken OBVIOUSLY_BAD_TOKEN');
$I->sendGET('/api/v2/users/me.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_UNAUTHORIZED);

$I->amGoingTo('sign in with valid facebook token at first time, so user has to be created in the database.');
$I->haveFacebookTestUserAccount(true);
$accessToken = $I->grabFacebookTestUserAccessToken();
$userEmail = $I->grabFacebookTestUserEmail();
$I->haveHttpHeader('Content-Type', 'application/json');
$I->haveHttpHeader('Auth', 'FacebookToken ' . $accessToken);
$I->dontSeeInDatabase('nmtn_user', ['email' => $userEmail]);
$I->sendGET('/api/v2/users/me.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsJson(['entries' => [['email' => $userEmail]]]);
$I->seeInDatabase('nmtn_user', ['email' => $userEmail, 'registered' => true]);

$I->amGoingTo('sign in with valid facebook token again, so user has to be present in the database.');
$I->haveHttpHeader('Auth', 'FacebookToken ' . $accessToken);
$I->seeInDatabase('nmtn_user', ['email' => $userEmail]);
$I->sendGET('/api/v2/users/me.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsJson(['entries' => [['email' => $userEmail]]]);
$I->seeInDatabase('nmtn_user', ['email' => $userEmail]);

$I->amGoingTo('sign up with expired facebook token.');
$I->haveHttpHeader('Auth', 'FacebookToken ' . FACEBOOK_USER_TOKEN_EXPIRED);
$I->sendGET('/api/v2/users/me.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_UNAUTHORIZED);
$I->seeResponseContainsJson(['success' => false]);
