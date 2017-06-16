<?php
/**
 * @author samva
 */

$I = new ApiGuy($scenario);
$I->am('Guest');

$I->wantTo('upload without file');

$I->amGoingTo('send request without file to the backend server');

$I->sendPOST(
    '/upload/file.json',
    ['name' => TEST_IMAGE_FILE_LABEL]
);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['success' => false]);
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
