<?php

namespace Nmotion\BackofficeBundle\Controller;

use FOS\Rest\Util\Codes;
use Nmotion\NmotionBundle\Controller\BaseRestController;

abstract class BackofficeController extends BaseRestController
{
    /**
     * @return boolean
     */
    protected function isSolutionAdmin()
    {
        return $this->get('security.context')->isGranted('ROLE_SOLUTION_ADMIN');
    }

    /**
     * @return boolean
     */
    protected function isRestaurantAdmin()
    {
        return $this->get('security.context')->isGranted('ROLE_RESTAURANT_ADMIN');
    }

    /**
     * @return boolean
     */
    protected function isRestaurantStaff()
    {
        return $this->get('security.context')->isGranted('ROLE_RESTAURANT_STAFF');
    }

    protected function getRequestFilters()
    {
        $filters   = [];
        $filterSet = $this->getRequest()->get('filter');
        if ($filterSet && is_array($filterSet)) {
            foreach ($filterSet as $filter) {
                $filters[$filter['property']] = $filter['value'];
            }
        }

        return $filters;
    }

    public function entriesResponse($entries, $total = null, $extraResponse = array())
    {
        $responsePayload = [
            'success' => true,
            'entries' => $entries
        ];

        if ($total) {
            $responsePayload['total'] = $total;
        }
        
        $responsePayload = array_merge($responsePayload, $extraResponse);

        $view = $this->view($responsePayload, Codes::HTTP_OK);

        return $this->handleView($view);
    }
}
