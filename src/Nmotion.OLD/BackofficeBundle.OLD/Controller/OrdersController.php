<?php

namespace Nmotion\BackofficeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Nmotion\NmotionBundle\Entity\Order;
use Nmotion\NmotionBundle\Entity\Repositories\OrderRepository;

class OrdersController extends BackofficeController
{
    use RestaurantAssertAccess;

    /**
     * GET /orders
     *
     * @throws AccessDeniedException
     * @return Response json
     */
    public function getOrdersAction()
    {
        $this->setSerializerGroups(['backoffice', 'backoffice.list']);

        if (! $this->get('security.context')->isGranted('ROLE_SOLUTION_ADMIN') && ! $this->get('security.context')->isGranted('ROLE_RESTAURANT_ADMIN') && ! $this->get('security.context')->isGranted('ROLE_RESTAURANT_STAFF')) {
            throw new AccessDeniedException;
        }

        /** @var $repository OrderRepository */
        $repository = $this->getDoctrine()->getRepository('NmotionNmotionBundle:Order');

        $orderBy = null;
        if ($this->getRequest()->get('sort')) {
            $orderBy = [
                $this->getRequest()->get('sort') => ($this->getRequest()->get('order') === 'DESC' ? 'DESC' : 'ASC')
            ];
        }
        
        if ($this->isSolutionAdmin()) {
            $restaurant_id = NULL;
        } else if ($this->isRestaurantAdmin() || $this->isRestaurantStaff()) {
            $user = $this->getUser();
            $restaurant = $user->getRestaurant();
            
            if($restaurant){
                $restaurant_id = $restaurant->getId();
            }
        }
                
        $filters = $this->getRequestFilters();
        $start   = $this->getRequest()->get('start', null);
        $limit   = $this->getRequest()->get('limit', null);

        $extraResponse = array();
        
        if (array_key_exists('dateFrom', $filters) && array_key_exists('dateTo', $filters)) {
            $dateFrom = new \DateTime($filters['dateFrom']);
            $dateTo   = new \DateTime($filters['dateTo']);
            $dateTo->add(new \DateInterval('PT23H59M59S'));
            $total    = $repository->getTotalForPeriod($dateFrom, $dateTo, $restaurant_id);
            $orders   = $repository->findForPeriod($dateFrom, $dateTo, $orderBy, $limit, $start, $restaurant_id);
        } else {
            
            $realtime_filters = array("restaurant" => $restaurant_id);
            if($this->getRequest()->get('orderStatusType')){
                $orderStatuses = array("new"=>array(6,7),"completed"=>array(8,9));
                $realtime_filters['orderStatus'] = $orderStatuses[$this->getRequest()->get('orderStatusType')];
            }
            
            if($this->getRequest()->get('period')){
                switch($this->getRequest()->get('period')){
                    case 'd': $realtime_filters['minUpdatedAt'] = strtotime(date("Y-m-d 00:00:00")); break;
                    case 'w': $realtime_filters['minUpdatedAt'] = strtotime(date("Y-m-d 00:00:00",  strtotime("-7 days"))); break;
                    case '2w': $realtime_filters['minUpdatedAt'] = strtotime(date("Y-m-d 00:00:00",  strtotime("-15 days"))); break;
                    case 'm': $realtime_filters['minUpdatedAt'] = strtotime(date("Y-m-d 00:00:00",  strtotime("-1 months"))); break;
                    case 'y': $realtime_filters['minUpdatedAt'] = strtotime(date("Y-m-d 00:00:00",  strtotime("-1 years"))); break;
                }
            }
            
            if((int)$restaurant_id) {
                $orderByField = "o.".$this->getRequest()->get('sort');
                $orderByDir = ($this->getRequest()->get('order') === 'DESC' ? 'DESC' : 'ASC');
                $orders = $repository->findByFilters($realtime_filters, $orderByField, $orderByDir, $limit, $start);
            } else {
                $orders = $repository->findBy([], $orderBy, $limit, $start);
            }
            
            $total  = $repository->getTotalOrders($realtime_filters);
            
            if($this->getRequest()->get('newOrdersLastSyncTime')){
                $realtime_filters["minUpdatedAt"] = $this->getRequest()->get('newOrdersLastSyncTime');
                $realtime_filters['orderStatus'] = array(6);
                $extraResponse['newSynced'] = $repository->getTotalOrders($realtime_filters);
                $extraResponse['newOrdersLastSyncTime'] = time();
            }
            
        }

        return $this->entriesResponse($orders, $total, $extraResponse);
    }

    /**
     * GET /orders/1;5;8;9;12
     *
     * @param string $ids
     *
     * @return Response json
     *
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    public function getOrderAction($ids)
    {
        $this->setSerializerGroups(['backoffice', 'backoffice.entity']);

        $idsArray = explode(';', $ids);

        $this->assertUserHasAccessToOrders($ids);

        /** @var $repository OrderRepository */
        $repository = $this->getDoctrine()->getRepository('NmotionNmotionBundle:Order');

        $total = 0;
        $orders = [];
        foreach ($idsArray as $id) {
            $order = $repository->find((int)$id);
            if (!$order instanceof Order) {
                throw new NotFoundHttpException('Order not found with id: ' . $id);
            }
            $orders[] = $order;
            $total++;
        }

        return $this->entriesResponse($orders, $total);
    }
    
    public function patchOrderAction($id){
        $this->assertUserHasAccessToOrders($id);
        $repository = $this->getDoctrine()->getRepository('NmotionNmotionBundle:Order');
        $order = $repository->find((int)$id);
        if($order){
            if($this->getRequest()->get('patchType', null) == "statusChange")
            {
                $statusToChange = $this->getRequest()->get('orderStatus', null);
                
                $statusCodes = array("accepted" => 7, "readyForPickup" => 8, "readyForServe" => 9, "acceptAndClose" => 9);
                if($statusCodes[$statusToChange['name']]){
                    $repository->setOrderStatus($order,$statusCodes[$statusToChange['name']]);
                    
                    $pushToken = $order->getPushToken();
                    
                    if($pushToken) {
                        $pushMessages = array(
                            "accepted" => array(
                                'message' 	=> 'Thank you for your order, we will send an update asap',
                                'title'		=> 'nMotion',
                                //'subtitle'	=> 'nMotion Push',
                                'tickerText'	=> 'Thank you for your order, we will send an update asap',
                                'vibrate'	=> 1,
                                'sound'		=> 1
                                ),
                            "readyForPickup" => array(
                                'message' 	=> 'Your order is now ready so please come to the bar to get it',
                                'title'		=> 'nMotion',
                                //'subtitle'	=> 'nMotion Push',
                                'tickerText'	=> 'Your order is now ready so please come to the bar to get it',
                                'vibrate'	=> 1,
                                'sound'		=> 1
                                ),
                            "readyForServe" => array(
                                'message' 	=> 'Your order is coming to you in just a while',
                                'title'		=> 'nMotion',
                                //'subtitle'	=> 'nMotion Push',
                                'tickerText'	=> 'Your order is coming to you in just a while',
                                'vibrate'	=> 1,
                                'sound'		=> 1
                                ),
                            "acceptAndClose" => array(
                                'message' 	=> 'Thank you for your order, we will serve you asap',
                                'title'		=> 'nMotion',
                                'tickerText'	=> 'Thank you for your order, we will serve you asap',
                                'vibrate'	=> 1,
                                'sound'		=> 1
                                )
                        );

                        $pushNotification = $this->get('nmotion.google.pushNotification');
                        $pushNotification->sendNotification(array($pushToken),$pushMessages[$statusToChange['name']]);
                        
                    }
                    exit;
                }
                return $this->jsonResponseSuccessful("",[$order]);
            }
        }
    }
}
