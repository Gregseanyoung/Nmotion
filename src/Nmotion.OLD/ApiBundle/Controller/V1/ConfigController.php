<?php

namespace Nmotion\ApiBundle\Controller\V1;

use Nmotion\NmotionBundle\Controller\BaseRestController;

class ConfigController extends BaseRestController
{
    /**
     * GET /api/v1/config.json
     *
     * @return Response json
     */
    public function getConfigAction()
    {
        $this->setSerializerGroups(['api']);

        $config = $this->getDoctrine()->getRepository('NmotionNmotionBundle:Config')->findAll();

        return $this->jsonResponseSuccessful('', $config);
    }
}
