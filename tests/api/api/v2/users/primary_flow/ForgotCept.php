<?php

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('test forgot password functionality using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addUserFixture(
    [
        'email'              => 'api.v2.user.forgot.test@nmotion.pp.ciklum.com',
        'confirmation_token' => null
    ]
);

$params = [
    'email'     => $user['email']
];
$I->seeInDatabase('nmtn_user', ['email' => $user['email'], 'confirmation_token' => null]);
$I->sendPOST("/api/v2/users/forgot.json", $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_user', ['email' => $user['email']]);
$I->dontSeeInDatabase('nmtn_user', ['email' => $user['email'], 'confirmation_token' => null]);
