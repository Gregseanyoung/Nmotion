<?php
/**
 * @author tiger
 */

$I = new ApiGuy($scenario);
$I->am('Anonymous');
$I->wantToTest('fail register new restaurant without providing authorization through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addUserFixture(['email' => 'bo.rest.register.test@nmotion.pp.ciklum.com']);

$I->willEvaluateAuthorizationToken($user['username'], $user['password']);

// successful registration
$I->amGoingTo('send new restaurant data to the backend server: successful registration');

$I->sendPOST('/backoffice/restaurants.json', []);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_FORBIDDEN);
$I->seeResponseContainsJson(['success' => false]);
