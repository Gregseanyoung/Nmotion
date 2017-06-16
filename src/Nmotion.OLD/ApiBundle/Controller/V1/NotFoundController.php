<?php

namespace Nmotion\ApiBundle\Controller\V1;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\Rest\Util\Codes;

class NotFoundController extends Controller
{
    /**
     * Returns Not found error in json format
     * @param string $path
     * @return \Symfony\Component\HttpFoundation\Response json
     */
    public function jsonAction($path)
    {
        return new Response(
            json_encode(['success' => 'false', 'message' => sprintf('Path %s not found', $path)]),
            Codes::HTTP_NOT_FOUND,
            ['Content-Type' => 'application/json']
        );
    }
}
