<?php
/**
 * @author tiger
 */

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('edit restaurant profile through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');
$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$restaurant = $I->addRestaurantFixture(
    [
        'email'     => 'bo.rest.edit.test@nmotion.pp.ciklum.com',
        'adminUser' => ['email' => 'bo.rest.edit.test@nmotion.pp.ciklum.com'],
        'address'   => []
    ]
);

$restaurantFormData = $restaurant->__value();
$restaurantFormData['adminUser'] = [
    'email'     => $restaurantFormData['adminUser']['email'],
    'firstName' => $restaurantFormData['adminUser']['firstName'],
    'lastName'  => $restaurantFormData['adminUser']['lastName']
];

unset($restaurantFormData['adminUserId']);
unset($restaurantFormData['restaurantAddressId']);
unset($restaurantFormData['address']['countryId']);
unset($restaurantFormData['operationTimes']);

$restaurantFormData['name'] = 'Yakitoriya';
$restaurantFormData['email'] = 'info@yaki2000.com.ua';
$restaurantFormData['siteUrl'] = 'http://yaki2000.com.ua/';
$restaurantFormData['address']['postalCode'] = '00001';

$I->amGoingTo('send changed restaurant data to the backend server');
$I->dontSeeInDatabase('nmtn_restaurant', ['email' => $restaurantFormData['email']]);
$I->sendPUT("/backoffice/restaurants/{$restaurant['id']}.json", $restaurantFormData);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_restaurant', ['email' => $restaurantFormData['email']]);
