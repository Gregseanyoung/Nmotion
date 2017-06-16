<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('add new config parameter through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

// successful adding
$I->amGoingTo('send new config parameter data to the backend server: successful adding');
$params = [
    'name'        => 'backoffice.config.add',
    'value'       => 28800,
    'type'        => 'integer',
    'description' => 'Just for testing purposes',
    'system'      => false
];
$I->dontSeeInDatabase('nmtn_config', ['name' => $params['name']]);
$I->sendPOST('/backoffice/configs.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_config', ['name' => $params['name']]);
