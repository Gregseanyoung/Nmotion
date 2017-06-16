<?php
/**
 * @author tiger
 */

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('register new restaurant through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

// successful registration
$I->amGoingTo('send new restaurant data to the backend server: successful registration');
$params = [
    "name" => "MyRestaurant",
    "fullDescription" => "full",
    "facebookPlaceId" => "119974038079794",
    "feedbackUrl" => "http://myrestaurant.com/feedback",
    "videoUrl" => "http://myrestaurant.com/feedback",
    "checkOutTime" => 120,
    "email" => "support@myrestaurant.com",
    "phone" => "222-5-222",
    "siteUrl" => "http://myrestaurant.com",
    "contactPersonName" => "Contact Person",
    "contactPersonEmail" => "contact@person.com",
    "contactPersonPhone" => "777-8-999",
    "legalEntity" => "",
    "invoicingPeriod" => "monthly",
    "vatNo" => "00000008",
    "regNo" => "0004",
    "kontoNo"  => "02",
    "inHouse" => true,
    "address" => [
        "longitude" => 180.666666,
        "latitude" => 180.666666,
        "addressLine1" => "Address, 1",
        "city" => "City",
        "postalCode" => "05-552"
    ],
    "adminUser" => [
        "email" => "new@email.com",
        "firstName" => "Admin",
        "lastName" => "Adminovich"
    ],
    "operationTimes" => [
        ["dayOfTheWeek" => 1, "timeFrom" => 0, "timeTo" => 86340],
        ["dayOfTheWeek" => 2, "timeFrom" => 28800, "timeTo" => 64800],
        ["dayOfTheWeek" => 3, "timeFrom" => 32400, "timeTo" => 64800],
        ["dayOfTheWeek" => 4, "timeFrom" => 32400, "timeTo" => 64800],
        ["dayOfTheWeek" => 5, "timeFrom" => 32400, "timeTo" => 64800],
        ["dayOfTheWeek" => 6, "timeFrom" => 32400, "timeTo" => 64800],
        ["dayOfTheWeek" => 7, "timeFrom" => 32400, "timeTo" => 64800]
    ]
];
$I->dontSeeInDatabase('nmtn_user', ['email' => $params['adminUser']['email']]);
$I->sendPOST('/backoffice/restaurants.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_restaurant', ['email' => $params['email']]);
$I->seeInDatabase('nmtn_user', ['email' => $params['adminUser']['email']]);

// check that restaurant mailbox for printer was successfully created
$mailboxAddress = 'restaurant' . $I->grabDataFromJsonResponse('entries.0.id') . '@printer.nmotion.dk';
$I->seeInSecondDatabase('mailbox', ['username' => $mailboxAddress]);
$I->seeInSecondDatabase('alias', ['address' => $mailboxAddress]);
$I->seeInSecondDatabase('log', ['data' => $mailboxAddress]);

// validation failed - restaurant with this name and postal code already exists
$I->amGoingTo('send new restaurant data: validation failed - restaurant name is already used with this postal code');
$I->sendPOST('/backoffice/restaurants.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
$I->seeResponseContainsJson(
    [
        'errors' => [
            [
                'children' => [
                    'name' => [
                        'errors' => ["Restaurant with this name and postal code already exists."]
                    ]
                ]
            ]
        ]
    ]
);

// validation failed - admin user already exists
$I->amGoingTo('send new restaurant data to the backend server: validation failed - admin user already exists');
$I->sendPOST('/backoffice/restaurants.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);

// validation failed - required fields are missing
$I->amGoingTo('send new restaurant data to the backend server: validation failed - required fields are missing');
$params2 = [];
$I->sendPOST('/backoffice/restaurants.json', $params2);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);

// validation failed - incorrect fields
$I->amGoingTo('send new restaurant data to the backend server: validation failed - incorrect fields');
$params3 = [
    "name" => "name",
    "fullDescription" => "l",
    "checkOutTime" => 100500,
    "email" => "incorrect",
    "phone" => "no",
    "contactPersonName" => "name",
    "contactPersonEmail" => "incorrect",
    "contactPersonPhone" => "no",
    "invoicingPeriod" => "period",
    "vatNo" => 888888889,
    "regNo" => 44445,
    "kontoNo"  => 1,
    "address" => [
        "addressLine1" => "address",
        "city" => "city",
        "postalCode" => "no"
    ],
    "adminUser" => [
        "email" => "incorrect",
        "firstName" => "name",
        "lastName" => "last"
    ]
];
$I->sendPOST('/backoffice/restaurants.json', $params3);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
