<?php

namespace Nmotion\RestaurantPageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\Rest\Util\Codes;
use FOS\RestBundle\Controller\Annotations\Post;
use Nmotion\NmotionBundle\Entity\RestaurantGuest;
use Nmotion\NmotionBundle\Entity\MenuCategory;
use Nmotion\NmotionBundle\Entity\OrderMeal;
use Nmotion\NmotionBundle\Controller\BaseRestController;
use Nmotion\NmotionBundle\Entity\OrderStatus;
use Nmotion\NmotionBundle\Entity\Order;
use Nmotion\NmotionBundle\Entity\Restaurant;
use Nmotion\NmotionBundle\Entity\User;
use Nmotion\NmotionBundle\Entity\Config;
use Doctrine\ORM\EntityManager;

use Nmotion\NmotionBundle\Entity;
use Nmotion\NmotionBundle\Form\PaymentType;

class CartController extends BaseRestController
{
    private function getRestaurantFromRequest(Request $request)
    {
        $currentHost = $request->getHttpHost();
        // Find restaurant or send 404
        $currentRestaurant = $this->getDoctrine()->getRepository('Nmotion\NmotionBundle\Entity\Restaurant')->findOneBy(['nmSiteUrl' => $currentHost]);
        if ($currentRestaurant == null) {
            return new RedirectResponse($this->generateUrl('nmotion_landing_page_index'));
        }
	    return $currentRestaurant;
    }

    // find user if exists
    /**
     *
     * Array
     *   (
     *       [site_id] => 16
     *       [user] => Array
     *           (
     *               [firstname] => Anders
     *               [lastname] => Gamborg
     *               [email] => anders@agamborg.dk
     *               [phone] => 24228858
     *           )
     *
     *       [order] => Array
     *           (
     *               [items] => Array
     *                   (
     *                       [0] => Array
     *                           (
     *                               [qty] => 10
     *                               [sku] => 59
     *                           )
     *
     *                   )
     *
     *           )
     *
     *   )
     *
     **/
    /**
     * /cart/checkout/
     *
     * @return Response json
     */
    public function checkoutAction(Request $request)
    {
        $model = $request->request->get('model');
        $userRepository = $this->getRepository('User');
        $userList = $userRepository->findBy(array('email'=>$model['user']['email']));
        $user = null;
        if (count($userList) > 1) {
            foreach ($userList as $u) {
                if ($u instanceof RestaurantGuest) {
                    $user = $u;
                }
            }
        } else if (isset($userList[0]) && $userList[0] instanceof RestaurantGuest) {
            $user = $userList[0];
        } else {
            $user = (new RestaurantGuest)
                    ->setRegistered(true)
                    ->setRegistrationOrigin(User::REGISTRATION_ORIGIN_NMOTION)
                    ->setEnabled(true)
                    ->setUsername($model['user']['email'])
                    ->setEmail($model['user']['email'])
                    ->setFirstName($model['user']['firstname'])
                    ->setLastName($model['user']['lastname'])
                    ->setRegistrationOrigin('TDC')
                    ->setPassword((new RestaurantGuest())->generatePassword(8));
            $this->get('fos_user.user_manager')->updateUser($user, true);
        }
        if ($user == null) {
            return $this->jsonResponseFailed(
                'Validation failed',
                ['User not found'],
                Codes::HTTP_PRECONDITION_FAILED
            );
        }
        $restaurant  = $this->getRepository('Restaurant')->find((int)$model['site_id']);
        $orderStatus = $this->getRepository('OrderStatus')->find(OrderStatus::NEW_ORDER);
        $serviceType = $this->getRepository('RestaurantServiceType')->find((int)2);
        $order = new Order();
        $order->setUser($user);
        $order->setRestaurant($restaurant);
        $order->setServiceType($serviceType);
        $order->setTableNumber('takeaway');
        $order->setOrderStatus($orderStatus);

        $configRepository = $this->getRepository('Config');
        $configSalesTax   = $configRepository->findOneBy(['name' => 'sales_tax']);
        if (!$configSalesTax instanceof Config) {
            throw new \RuntimeException('Config parameter "sales_tax" not found');
        }

        $salesTaxPercent = (float) $configSalesTax->getValue();
        if ($salesTaxPercent < 0) {
            throw new \RuntimeException('Config parameter "sales_tax" can not be less than 0');
        }

        $order->setTaxPercent($salesTaxPercent);

        $configDiscount= $configRepository->findOneBy(['name' => 'nmotion_discount']);
        if (!$configDiscount instanceof Config) {
            throw new \RuntimeException('Config parameter "nmotion_discount" not found');
        }

        $discountPercent = (float) $configDiscount->getValue();
        if ($discountPercent < 0 || $discountPercent > 100) {
            throw new \RuntimeException('Config parameter "nmotion_discount" must be in range [0..100]');
        }

        $order->setDiscountPercent($discountPercent);

        // Add items to the order
        foreach ($model['order']['items'] as $item) {
            $meal = new OrderMeal();
            $meal->setMeal($this->getRepository('Meal')->find($item['sku']));
            $meal->setQuantity($item['qty']);
            $order->addOrderMeal($meal);
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($order);
        $em->flush();

        $order_id = 'WEB' . $model['site_id'] . '_' . $order->getId();
        $amount = $order->getOrderTotalInCents();
        $currency = 208;

        $dibsCart = new DibsCart($order_id, $amount, $currency);
        header('Content-Type: application/json');
        die(json_encode(array(
            'amount' => $dibsCart->getAmount(),
            'merchant' => $dibsCart->getMerchant(),
            'currency' => $dibsCart->getCurrency(),
            'checksum' => $dibsCart->getChecksum(),
            'order_id' => $dibsCart->getOrderId(),
            'callback_url' => $request->getScheme() . '://' . $request->getHttpHost() . $this->get('router')->generate('nmotion_restaurant_page_paymentconfirmed')
        )));
    }

    const REGISTER_CARD_MARKER = 'registerCard';

    public function paymentconfirmedAction(Request $request) {
        $parameters = $this->getRequest()->request->all();
        if (empty($parameters)) {
            throw new HttpException(Codes::HTTP_BAD_REQUEST, 'No request parameters');
        }

        $payment = new Entity\Payment();
        $form = $this->createForm(new PaymentType(), $payment);
        // saving only those parameters which are declared by the form
        $data = array_intersect_key($parameters, $form->all());
        // all stack of parameters
        $data['allParameters'] = serialize($parameters);
        if (isset($parameters['orderid'])) {
            $parameters['orderid'] = substr($parameters['orderid'], strpos($parameters['orderid'], '_') + 1);
        }

        $data['order'] = isset($parameters['orderid']) ? (int)$parameters['orderid'] : null;
        /** @var Entity\Repositories\OrderRepository $orderRepository */
        $orderRepository = $this->getDoctrine()->getRepository('NmotionNmotionBundle:Order');
        /** @var Entity\Order $order */
        $order = $data['order'] ? $orderRepository->find($data['order']) : null;
        if ($order instanceof Entity\Order) {
            $data['order'] = $order->getId();
        } else {
            $data['order'] = null;
        }

        $payment->setOrder($order);
        $form->bind($data);
        if (! $form->isValid()) {
            $payment->setStatus('FAILED');
            $paymentComment = '';
            foreach ($this->getFormErrorMessages($form) as $field => $messages) {
                foreach ($messages as $message) {
                    $paymentComment .= "$field.$message\n";
                }
            }
            $payment->setPaymentComment($paymentComment);
        }

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        // Update status
        switch($parameters['statuscode']) {
            case 1:
                $payment->setStatus('DECLINED'); break;

            case 2:
                $payment->setStatus('ACCEPTED'); break;

            case 12:
                $payment->setStatus('PENDING'); break;
        }

        // saving payment info even if it's not passed validation
        $em->persist($payment);
        $em->flush();

        if (!$order && $parameters['orderid'] !== self::REGISTER_CARD_MARKER) {
            $payment->setStatus('FAILED');
            $payment->setPaymentComment(
                $payment->getPaymentComment()
                . 'Order with such id not found: '
                . (isset($parameters['orderid']) ? $parameters['orderid'] : '')
            );
            $em->flush();
            throw new NotFoundHttpException(
                'Order with such id not found: '
                . (isset($parameters['orderid']) ? $parameters['orderid'] : '')
            );
        }

        $result['transaction']      = isset($parameters['transaction'])      ? $parameters['transaction']      : '';
        $result['cardNumberMasked'] = isset($parameters['cardnumbermasked']) ? $parameters['cardnumbermasked'] : '';
        $result['ticket']           = isset($parameters['ticket'])           ? $parameters['ticket']           : '';

        // updating order status
        switch ($payment->getStatus()) {
            case 'ACCEPTED':
                if ($order && (! isset($parameters['s_registerCard']) || $parameters['s_registerCard'] == 0)) {
                    // unlink paid slave order - make it master order
                    if ($order->hasMaster()) {
                        $order->setMaster(null);
                    }
                    $orderRepository->setOrderStatus($order, Entity\OrderStatus::PAID);
                }

                if (
                    (isset($parameters['orderId']) && $parameters['orderId'] == self::REGISTER_CARD_MARKER)
                    || (isset($parameters['s_registerCard']) && $parameters['s_registerCard'] == 1)
                ) {
                    $result['status'] = 'Card registered';
                } else {
                    $result['status'] = 'Payment successful';
                }
                break;
            case 'CANCELLED':
                $result['status'] = 'Payment cancelled';
                if ($order) {
                    if ($order->isInPaidStatus() || $order->isInSentToPrinterStatus()) {
                        $result['status'] = 'Payment can not be cancelled as it was already paid';
                    } else {
                        $orderRepository->setOrderStatus($order, Entity\OrderStatus::CANCELLED);
                    }
                }
                break;
            case 'PENDING':
                $result['status'] = 'Payment pending';
                break;
            case 'DECLINED':
                $orderRepository->setOrderStatus($order, Entity\OrderStatus::FAILED);
                $result['status'] = 'Payment declined';
                break;
            case 'FAILED':
                $orderRepository->setOrderStatus($order, Entity\OrderStatus::FAILED);
                $result['status'] = 'Payment failed';
                break;
            default:
                $payment->setStatus('FAILED');
                $payment->setPaymentComment(
                    $payment->getPaymentComment()
                    . 'Unknown payment status: ' . $payment->getStatus()
                );
                $em->flush();
                $orderRepository->setOrderStatus($order, Entity\OrderStatus::FAILED);
                throw new HttpException(
                    Codes::HTTP_PRECONDITION_FAILED,
                    'Unknown payment status: ' . $payment->getStatus()
                );
                break;
        }

        $currentName = strtolower(substr($request->getHttpHost(), 0, strpos($request->getHttpHost(), '.')));
        if ($this->get('templating')->exists('NmotionRestaurantPageBundle:'.$currentName.':payment_response.html.twig')) {
            $template = 'NmotionRestaurantPageBundle:'.$currentName.':payment_response.html.twig';
        } else {
            $template = 'NmotionRestaurantPageBundle::payment_response.html.twig';
        }
        //$template = 'NmotionRestaurantPageBundle:'.$currentName.':payment_response.html.twig';

        return $this->render(
            $template,
            array('restaurant' => $this->getRestaurantFromRequest($request))
        );
    }
}

class DibsCart {
    private static $k1 = 'xlXtAzsw0%YEub%dv0FZ0M*5OQEcyG}%';
    private static $k2 = 'MwfY3L0?kKlY)Du0p9+(r?Z6?}4N_Lsw';

    private $_amount;
    private $_currency;
    private $_order_id;

    public function getAmount() {
        return $this->_amount;
    }

    public function getCurrency() {
        return $this->_currency;
    }

    public function getOrderId() {
        return $this->_order_id;
    }

    public function getMerchant() {
        return '90150157';
    }

    public function __construct($order_id, $amount, $currency) {
        $this->_order_id = $order_id;
        $this->_amount = $amount;
        $this->_currency = $currency;    
    }
    public function getChecksum() {
        // merchant=<merchant>&orderid=<orderid>&currency=<currency>&amount=<amount>
        return md5(self::$k2 . md5(self::$k1 . 'merchant='.$this->getMerchant().'&orderid='.$this->_order_id.'&currency='.$this->_currency.'&amount='.$this->_amount));
    }
}
