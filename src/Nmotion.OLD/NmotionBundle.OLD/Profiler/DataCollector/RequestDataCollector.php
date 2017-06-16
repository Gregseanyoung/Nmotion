<?php
/**
 * @author tiger
 */

namespace Nmotion\NmotionBundle\Profiler\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\RequestDataCollector as BaseRequestDataCollector;

class RequestDataCollector extends BaseRequestDataCollector
{
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        parent::collect($request, $response, $exception); // TODO: Change the autogenerated stub

        $responseContent = null;
        try {
            $responseContent = $response->getContent();
        } catch (\LogicException $e) {
            // the user already got the response content as a resource
            $responseContent = false;
        }

        $this->data['response_content'] = $responseContent;
    }

    public function getResponseContent()
    {
        return $this->data['response_content'];
    }
}