<?php
/**
 * @author tiger
 */

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest(
    'being restaurant admin i\'m not allowed to delete staff through backoffice-API for restaurant not own by me'
);
$I->haveHttpHeader('Content-Type', 'application/json');

// some other restaurant
$restaurant1 = $I->addRestaurantFixture();

// the restaurant that is being owned by current radmin
$restaurant2 = $I->addRestaurantFixture();

// add restaurant staff to the other restaurant
$staffUser = $I->addRestaurantStaffFixture($restaurant1['id']);

$I->willEvaluateAuthorizationToken($restaurant2['adminUser']['email'], $restaurant2['adminUser']['password']);

$I->amGoingTo('send request to the backend server to delete staff user');
$I->sendDELETE('/backoffice/staff/' . $staffUser['id']);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_FORBIDDEN);
$I->seeResponseContainsJson(['success' => false]);
$I->seeInDatabase('nmtn_user', ['email' => $staffUser['email']]);
