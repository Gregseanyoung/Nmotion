<?php
/**
 * @author nami
 */

$I = new ApiGuy($scenario);
$I->wantTo('fail perform search when not being given required parameters');

//incorrect input data, no geotag at all
$I->amGoingTo('request search without giving required geocode parameter');
$I->sendGET('/api/v2/restaurants/search.json');
$I->seeResponseIsJson();
$I->seeResponseCodeIs(HTTP_RESPONSE_PRECONDITION_FAILED);
