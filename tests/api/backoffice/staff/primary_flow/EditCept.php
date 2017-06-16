<?php
/**
 * @author tiger
 */

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest('edit restaurant staff account using backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture();
$staffUser = $I->addRestaurantStaffFixture($restaurant['id']);

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

$I->amGoingTo('send changed user data to the backend server');
$params = [
    'email'     => $staffUser['email'],
    'firstName' => 'staffChangedFirstName',
    'lastName'  => 'staffChangedLastName'
];
$I->dontSeeInDatabase('nmtn_user', ['first_name' => $params['firstName']]);
$I->seeInDatabase('nmtn_user', ['email' => $staffUser['email']]);
$I->sendPUT('/backoffice/staff/' . $staffUser['id'], $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_user', ['email' => $staffUser['email'], 'first_name' => $params['firstName']]);
