<?php
/**
 * @author tiger
 */

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('delete restaurant staff account using backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture();
$staffUser = $I->addRestaurantStaffFixture($restaurant['id']);

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

$I->amGoingTo('send request to the backend server to delete staff user');
$I->sendDELETE('/backoffice/staff/' . $staffUser['id']);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->dontSeeInDatabase('nmtn_user', ['email' => $staffUser['email']]);
