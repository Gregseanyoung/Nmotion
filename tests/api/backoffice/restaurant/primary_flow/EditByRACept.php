<?php
/**
 * @author tiger
 */

$I = new ApiGuy($scenario);
$I->am('Restaurant Admin');
$I->wantTo('edit restaurant profile through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$restaurant = $I->addRestaurantFixture();

$I->willEvaluateAuthorizationToken($restaurant['adminUser']['email'], $restaurant['adminUser']['password']);

$restaurantDataAfter = $restaurant->__value(); // get array instead of Maybe
$restaurantDataBefore = $restaurant;

$restaurantDataAfter['adminUser'] = [
    'email'     => $restaurantDataAfter['adminUser']['email'],
    'firstName' => $restaurantDataAfter['adminUser']['firstName'],
    'lastName'  => $restaurantDataAfter['adminUser']['lastName']
];

unset($restaurantDataAfter['adminUserId']);
unset($restaurantDataAfter['restaurantAddressId']);
unset($restaurantDataAfter['operationTimes']);
unset($restaurantDataAfter['address']['countryId']);

// fields allowed to be changed by radmin
$restaurantDataAfter['name']    = 'Yakitooooriya';
$restaurantDataAfter['email']   = 'info@yaki3000.com.ua';
$restaurantDataAfter['siteUrl'] = 'http://yaki3000.com.ua/';
$restaurantDataAfter['address']['postalCode'] = '00011';
$restaurantDataAfter['contactPersonName']  = 'trololo';
$restaurantDataAfter['contactPersonPhone'] = '102102102';
$restaurantDataAfter['contactPersonEmail'] = 'trololo@gmail.com';

// fields denied to be changed by radmin
$restaurantDataAfter['taMember']        = true;
$restaurantDataAfter['invoicingPeriod'] = 'weekly';
$restaurantDataAfter['vatNo']           = '66666666';
$restaurantDataAfter['regNo']           = '6666';
$restaurantDataAfter['kontoNo']         = '666';


$I->amGoingTo('send changed restaurant data to the backend server');
$I->dontSeeInDatabase('nmtn_restaurant', ['email' => $restaurantDataAfter['email']]);
$I->sendPUT('/backoffice/restaurants/' . $restaurant['id'], $restaurantDataAfter);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase(
    'nmtn_restaurant',
    [
        // data that has to be changed
        'name'                 => $restaurantDataAfter['name'],
        'email'                => $restaurantDataAfter['email'],
        'site_url'             => $restaurantDataAfter['siteUrl'],
        'contact_person_name'  => $restaurantDataAfter['contactPersonName'],
        'contact_person_phone' => $restaurantDataAfter['contactPersonPhone'],
        'contact_person_email' => $restaurantDataAfter['contactPersonEmail'],
        // data that has to be remained the same
        'ta_member'            => $restaurantDataBefore['taMember'],
        'invoicing_period'     => $restaurantDataBefore['invoicingPeriod'],
        'vat_no'               => $restaurantDataBefore['vatNo'],
        'reg_no'               => $restaurantDataBefore['regNo'],
        'konto_no'             => $restaurantDataBefore['kontoNo'],
    ]
);
