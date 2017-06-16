<?php

namespace Nmotion\NmotionBundle\Controller;

use DateTime;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\Common\Collections\ArrayCollection;
use Nmotion\NmotionBundle\Entity\Order;

class OrdersController extends BaseRestController
{
    /**
     * Fill in Excel doc
     *
     * @param Order[]|ArrayCollection $orders
     * @param \PHPExcel_Worksheet     $activeSheet
     */
    protected function fillExcel($orders, \PHPExcel_Worksheet $activeSheet)
    {
        $activeSheet->setCellValue('A1', 'Order');
        $activeSheet->setCellValue('B1', 'Restaurant');
        $activeSheet->setCellValue('C1', 'Postal code');
        $activeSheet->setCellValue('D1', 'Product total');
        $activeSheet->setCellValue('E1', 'Sales tax');
        $activeSheet->setCellValue('F1', 'Discount');
        $activeSheet->setCellValue('G1', 'Tips');
        $activeSheet->setCellValue('H1', 'Order total');
        $activeSheet->setCellValue('I1', 'Status');
        $activeSheet->setCellValue('J1', 'Date');

        $activeSheet->getStyle("A1:J1")->getFont()->setBold(true);

        $activeSheet->getColumnDimension('B')->setWidth(25);
        $activeSheet->getColumnDimension('C')->setWidth(12);
        $activeSheet->getColumnDimension('D')->setWidth(14);
        $activeSheet->getColumnDimension('H')->setWidth(14);
        $activeSheet->getColumnDimension('I')->setWidth(20);
        $activeSheet->getColumnDimension('J')->setWidth(12);

        if (empty($orders)) {
            $activeSheet->setCellValue('A2', 'No orders for requested period');
            return;
        }

        $i = 2;
        foreach ($orders as $order) {
            $activeSheet->setCellValue('A' . $i, $order->getId());
            $activeSheet->setCellValue('B' . $i, $order->getRestaurant()->getName());
            $activeSheet->setCellValue('C' . $i, $order->getRestaurant()->getAddress()->getPostalCode());
            $activeSheet->setCellValue('D' . $i, $order->getProductTotal());
            $activeSheet->setCellValue('E' . $i, $order->getSalesTax());
            $activeSheet->setCellValue('F' . $i, $order->getDiscount());
            $activeSheet->setCellValue('G' . $i, $order->getTips());
            $activeSheet->setCellValue('H' . $i, $order->getOrderTotal());
            $activeSheet->setCellValue('I' . $i, $order->getOrderStatus()->getName());
            $activeSheet->setCellValue('J' . $i, date('Y-m-d', $order->getUpdatedAt()));
            $i++;
        }

        $activeSheet->setTitle('Report');
    }

    /**
     * Kind of null-endpoint to let user get authentication cookie based on his Auth-header authentication
     */
    public function oblivionAction()
    {
        return new Response('Ok');
    }

    /**
     * Generate orders report for given dates in excel format
     *
     * @param string $dateFrom
     * @param string $dateTo
     *
     * @throws AccessDeniedException
     * @return Response
     */
    public function excelAction($dateFrom = null, $dateTo = null)
    {
        if (! $this->get('security.context')->isGranted('ROLE_SOLUTION_ADMIN')) {
            throw new AccessDeniedException;
        }

        $dateFrom = $dateFrom ? new DateTime($dateFrom) : new DateTime();
        $dateTo   = $dateTo   ? new DateTime($dateTo)   : new DateTime();
        $dateTo->add(new \DateInterval('PT23H59M59S'));

        $orders = $this->getRepository('Order')
            ->findForPeriod($dateFrom, $dateTo);

        // ask the service for a Excel5
        $excelService = $this->get('xls.service_xls5');
        // or $this->get('xls.service_pdf');
        // or create your own is easy just modify services.yml

        // create the object see http://phpexcel.codeplex.com documentation
        $excelObj = $excelService->excelObj;
        $excelObj->getProperties()
            ->setTitle('Orders from ' . $dateFrom->format('Y-m-d') . ' to ' . $dateTo->format('Y-m-d'));
        $excelObj->setActiveSheetIndex(0);

        $activeSheet = $excelObj->getActiveSheet();

        $this->fillExcel($orders, $activeSheet);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $excelObj->setActiveSheetIndex(0);

        //create the response
        $response = $excelService->getResponse();
        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
        $response->headers->set(
            'Content-Disposition',
            'attachment;filename=orders_' . $dateFrom->format('Y-m-d') . '_' . $dateTo->format('Y-m-d') . '.xls'
        );

        // If you are using a https connection, you have to set those two headers for compatibility with IE <9
        $response->headers->set('Pragma', 'public');
        $response->headers->set('Cache-Control', 'maxage=1');

        return $response;
    }
}
