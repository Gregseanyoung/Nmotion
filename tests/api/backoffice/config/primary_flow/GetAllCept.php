<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('get list of config parameters through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$I->addConfigFixture(
    [
        'name'  => 'backoffice.config.list@nmotion.pp.ciklum.com',
        'value' => 'backoffice.config.list@nmotion.pp.ciklum.com'
    ]
);

$I->sendGET('/backoffice/configs.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
