<?php
/**
 * @author mipo
 * @author samva <vas@ciklum.com>
 */

$I = new ApiGuy($scenario);
$I->am('Guest');
$I->wantToTest('callback response from payment system when register card spike is being performed');

$user = $I->addAnonymousUserFixture();

$I->amGoingTo('send payment callback response with status=ACCEPTED and orderId=registerCard to the backend server');

$I->haveHttpHeader('Auth', 'DeviceToken ' . $user['deviceIdentity']);
$I->haveHttpHeader('Content-Type', 'application/x-www-form-urlencoded');
$params = [
    'MAC'                 => '365f8d21ca53b4ee61a91623b4f9d882ef08884e745c581e8992ecb1d46a1581',
    'acceptReturnUrl'     => 'http:\/\/stage.nmotion.pp.ciklum.com\/paymentconfirmation\/',
    'acquirer'            => 'test',
    'actionCode'          => 'd100',
    'amount'              => '1',
    'callbackUrl'         => 'http:\/\/stage.nmotion.pp.ciklum.com\/paymentconfirmation\/',
    'cancelreturnurl'     => 'http:\/\/stage.nmotion.pp.ciklum.com\/paymentconfirmation\/',
    'cardNumberMasked'    => '471110XXXXXX0000',
    'cardTypeName'        => 'VISA',
    'createTicketAndAuth' => '1',
    'currency'            => 'DKK',
    'expMonth'            => '06',
    'expYear'             => '24',
    'merchant'            => '90150157',
    'orderId'             => 'registerCard',
    'paytype'             => "DK,VISA,MTRO,MC,ELEC,JCB,AMEX",
    'status'              => 'ACCEPTED',
    'test'                => '1',
    'ticket'              => '706450975',
    'ticketStatus'        => 'ACCEPTED'
];
$I->sendPOST('/paymentconfirmation/', $params);
$I->seeResponseCodeIs(HTTP_RESPONSE_OK);
$I->see('Card registered', '#status');
$I->see('706450975', '#ticket');
$I->see('471110XXXXXX0000', '#cardNumberMasked');
$I->seeInDatabase('nmtn_payment', ['status' => $params['status'], 'ticket' => $params['ticket']]);
