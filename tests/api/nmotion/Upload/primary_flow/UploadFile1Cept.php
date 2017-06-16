<?php
/**
 * @author samva
 */

$I = new ApiGuy($scenario);
$I->am('Guest');

$I->wantTo('upload file');

$I->amGoingTo('send file to the backend server');
$I->sendPOST(
    '/upload/file.json',
    ['name' => TEST_IMAGE_FILE_LABEL],
    ['file' => TEST_IMAGE_FILE_PATH]
);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['success' => true]);
$I->seeResponseCodeIs(HTTP_RESPONSE_CREATED);

$asset = $I->grabDataFromJsonResponse('entries')[0];

$I->amGoingTo('request file from server using returned url in response');
$I->sendGET($asset['url']);
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);

$I->wantTo('upload already uploaded file');

$I->amGoingTo('send uploaded file to the backend server');
$I->sendPOST(
    '/upload/file.json',
    ['name' => TEST_IMAGE_FILE_LABEL],
    ['file' => TEST_IMAGE_FILE_PATH]
);
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(['success' => true]);

$assetUploaded = $I->grabDataFromJsonResponse('entries')[0];

$I->amGoingTo('request file from server using returned url in response');
$I->sendGET($assetUploaded['url']);
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
