<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('failure deleting system config parameter through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$config = $I->addConfigFixture(
    [
        'name'   => 'backoffice.config.deletefail1@nmotion.pp.ciklum.com',
        'value'  => 'backoffice.config.deletefail1@nmotion.pp.ciklum.com',
        'system' => true
    ]
);

$I->sendDELETE('/backoffice/configs/' . $config['id'] . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_FORBIDDEN);
