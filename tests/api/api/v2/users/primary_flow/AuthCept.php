<?php
/**
 * @author tiger
 * @author samva
 */

$response_entry_api_get_users_me = [
    // used on mobile
    'id',
    'firstName',
    'lastName',
    'email',
    // just returned
    'roles',
    'registered'
];

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('get user entity data using API');
$I->haveHttpHeader('Content-Type', 'application/json');

$user = $I->addUserFixture(['email' => 'api.v2.user.auth.test@nmotion.pp.ciklum.com', 'password' => 'tiger']);

$I->willEvaluateAuthorizationToken($user['username'], $user['password']);

$I->amGoingTo('send request to /api/v2/users/me.json signed with authorization token');
$I->sendGET('/api/v2/users/me.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(
    [
        'success' => true,
        'entries' => [
            ['email' => $user['email']]
        ]
    ]
);

$I->seeResponseEntriesHasFields($response_entry_api_get_users_me);
