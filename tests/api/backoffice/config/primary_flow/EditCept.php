<?php

$I = new ApiGuy($scenario);
$I->am('Solution Admin');
$I->wantToTest('edit config parameter through backoffice-API');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->willEvaluateAuthorizationToken(SOLUTION_ADMIN_EMAIL, SOLUTION_ADMIN_PASSWORD);

$config = $I->addConfigFixture(
    [
        'name'  => 'backoffice.config.edit@nmotion.pp.ciklum.com',
        'value' => 'backoffice.config.edit@nmotion.pp.ciklum.com'
    ]
);

// successful editing
$I->amGoingTo('send edited config parameter data to the backend server: successful editing');
$params = [
    "name"        => "backoffice.config.edited.param",
    "value"       => 100500,
    "description" => "backoffice.config.edited.param"
];
$I->sendPUT('/backoffice/configs/' . $config['id'] . '.json', $params);
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseContainsJson(['success' => true]);
$I->seeInDatabase('nmtn_config', ['name' => $params['name']]);
