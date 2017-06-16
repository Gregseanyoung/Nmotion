<?php

namespace Nmotion\ApiBundle\Controller\V2;

use Nmotion\NmotionBundle\Controller\BaseRestController;
use Nmotion\NmotionBundle\Controller\RestaurantTrait;
use Nmotion\NmotionBundle\Exception\PreconditionFailedException;

use Nmotion\NmotionBundle\Entity\Config;
use Nmotion\NmotionBundle\Entity\Order;
use Nmotion\NmotionBundle\Entity\OrderMeal;
use Nmotion\NmotionBundle\Entity\Meal;
use Nmotion\NmotionBundle\Entity\OrderStatus;
use Nmotion\NmotionBundle\Entity\Payment;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

class PaymentController extends BaseRestController
{
    use RestaurantTrait;
    /**
     * POST /api/v2/payment.json
     *
     * @return Response json
     */

    const CHECKIN_INHOUSE = 1;
    const CHECKIN_TAKEAWAY = 2;
    const CHECKIN_ROOMSERVICE = 3;

    const ORDER_STATUS_NEW = 1;
    const ORDER_STATUS_PENDING_PAYMENT = 2;


    public function postPaymentAction(Request $request)
    {
        $user = $this->getUser();
        $request = $this->get('request');
        $orderId = (int)$request->request->get('orderId');
        $pushToken = $request->request->get('pushToken');
        $order = $this->getUserOrder($user, $orderId);
        $orderStatus = $this->getRepository('OrderStatus')->find(OrderStatus::PAID);

        if($order->getServiceType()->getId() != PaymentController::CHECKIN_ROOMSERVICE) {
            throw new PreconditionFailedException('Invalid order');
        }
        if($order->getOrderStatus()->getId() != 2 && $order->getOrderStatus()->getId() != 1) {
            throw new PreconditionFailedException('Invalid order');
        }

        $payment = new Payment();
        $payment->setStatus('ACCEPTED');
        $payment->setOrder($order);
        // Payments are in coins instead of full krones.
        $payment->setAmount($order->getOrderTotal() * 100);
        $payment->setCurrency('DKK');
        $payment->setAllParameters('');

        $order->setOrderStatus($orderStatus);
        if($pushToken){
            $order->setPushToken($pushToken);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->persist($payment);

        $em->flush();
        

        return $this->jsonResponseSuccessful('', $payment);
    }
}
