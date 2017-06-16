<?php
/**
 * @author tiger
 */

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantToTest(
    'being restaurant admin i\'m not allowed to register staff through backoffice-API for restaurant not own by me'
);
$I->haveHttpHeader('Content-Type', 'application/json');

// some other restaurant
$restaurant1 = $I->addRestaurantFixture();

// the restaurant that is being owned by current radmin
$restaurant2 = $I->addRestaurantFixture();

$I->willEvaluateAuthorizationToken($restaurant2['adminUser']['email'], $restaurant2['adminUser']['password']);

$I->amGoingTo('send new user data to the backend server');
$params = [
    'email'     => 'restaurant.staff.reg.ra2.test@nmotion.pp.ciklum.com',
    'firstName' => 'foo',
    'lastName'  => 'bar'
];
$I->sendPOST('/backoffice/restaurants/' . $restaurant1['id'] . '/staff', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_FORBIDDEN);
$I->seeResponseContainsJson(['success' => false]);
$I->dontSeeInDatabase('nmtn_user', ['email' => $params['email']]);
