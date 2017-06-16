<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('delete config parameter through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$config = $I->addConfigFixture(
    [
        'name'   => 'backoffice.config.delete@nmotion.pp.ciklum.com',
        'value'  => 'backoffice.config.delete@nmotion.pp.ciklum.com',
        'system' => false
    ]
);

$I->sendDELETE('/backoffice/configs/' . $config['id'] . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
