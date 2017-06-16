<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('get one config parameter through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$config = $I->addConfigFixture(
    [
        'name'  => 'backoffice.config.one@nmotion.pp.ciklum.com',
        'value' => 'backoffice.config.one@nmotion.pp.ciklum.com'
    ]
);

// successful scenario
$I->amGoingTo('send get request for one config parameter to the backend server: successful scenario');
$I->seeInDatabase('nmtn_config', ['id' => $config['id']]);
$I->sendGET('/backoffice/configs/' . $config['id'] . '.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseContainsJson(['entries' => [['id' => $config['id']]]]);
