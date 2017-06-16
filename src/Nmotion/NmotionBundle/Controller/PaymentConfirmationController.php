<?php

namespace Nmotion\NmotionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use FOS\Rest\Util\Codes;
use Doctrine\ORM\EntityManager;

use Nmotion\NmotionBundle\Entity;
use Nmotion\NmotionBundle\Form\PaymentType;
use Nmotion\NmotionBundle\Controller\FormTrait;

class PaymentConfirmationController extends Controller
{
    use FormTrait;

    const REGISTER_CARD_MARKER = 'registerCard';

    public function indexAction()
    {
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
        $data['order'] = isset($parameters['orderId']) ? (int)$parameters['orderId'] : null;

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
        // saving payment info even if it's not passed validation
        $em->persist($payment);
        $em->flush();

        if (!$order && $parameters['orderId'] !== self::REGISTER_CARD_MARKER) {
            $payment->setStatus('FAILED');
            $payment->setPaymentComment(
                $payment->getPaymentComment()
                . 'Order with such id not found: '
                . (isset($parameters['orderId']) ? $parameters['orderId'] : '')
            );
            $em->flush();
            throw new NotFoundHttpException(
                'Order with such id not found: '
                . (isset($parameters['orderId']) ? $parameters['orderId'] : '')
            );
        }

        $result['transaction']      = isset($parameters['transaction'])      ? $parameters['transaction']      : '';
        $result['cardNumberMasked'] = isset($parameters['cardNumberMasked']) ? $parameters['cardNumberMasked'] : '';
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

        return $this->render(
            'NmotionNmotionBundle:PaymentConfirmation:payment_confirmation_response.html.twig',
            ['result' => $result]
        );
    }
}
