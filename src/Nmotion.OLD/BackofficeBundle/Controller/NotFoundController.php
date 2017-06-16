<?php

namespace Nmotion\BackofficeBundle\Controller;

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
            json_encode(['success' => 'false', 'message' => 'Path not found']),
            Codes::HTTP_NOT_FOUND,
            ['Content-Type' => 'application/json']
        );
    }
}
