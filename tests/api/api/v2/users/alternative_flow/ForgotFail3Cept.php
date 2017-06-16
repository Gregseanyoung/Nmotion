<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('fail sending forgot password using API - Facebook user cannot change password');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addUserFixture(
    [
        'email'               => 'api.v2.user.forgot.fail3@nmotion.pp.ciklum.com',
        'registered'          => true,
        'registration_origin' => 'Facebook'
    ]
);

$params = [
    'email' => $user['email']
];
$I->sendPOST("/api/v2/users/forgot.json", $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_FORBIDDEN);
$I->seeResponseContainsJson(['success' => false]);
