<?php
/**
 * @author tiger
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantTo('fail to checkin to restaurant by not providing authentication');
$I->haveHttpHeader('Content-Type', 'application/json');

$I->sendPOST('/api/v1/restaurants/1/checkin');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_UNAUTHORIZED);
$I->seeResponseContainsJson(['success' => false]);
