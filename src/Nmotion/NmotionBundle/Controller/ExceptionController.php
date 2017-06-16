<?php
/**
 * @author tiger
 */

namespace Nmotion\NmotionBundle\Controller;

use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\HttpKernel\Exception\FlattenException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\ExceptionController as RestBundleExceptionController;

use Nmotion\NmotionBundle\Util\ExceptionWrapper;

/**
 * Custom ExceptionController that uses the view layer and supports HTTP response status code mapping
 */
class ExceptionController extends RestBundleExceptionController
{
    /**
     * @inheritDoc
     */
    protected function createExceptionWrapper(array $parameters)
    {
        return new ExceptionWrapper($parameters);
    }

    /**
     * @inheritDoc
     */
    protected function getParameters(
        ViewHandler $viewHandler,
        $currentContent,
        $code,
        FlattenException $exception,
        DebugLoggerInterface $logger = null,
        $format = 'html'
    ) {
        $parameters = parent::getParameters($viewHandler, $currentContent, $code, $exception, $logger, $format);

        if ($this->container->getParameter('kernel.debug') === true && $exception->getTrace()) {
            $parameters['trace'] = $exception->getTrace();
        }

        if ($exception->getCode()) {
            $parameters['exceptionCode'] = $exception->getCode();
        }

        return $parameters;
    }

    /**
     * @inheritDoc
     */
    public function showAction(
        Request $request,
        FlattenException $exception,
        DebugLoggerInterface $logger = null,
        $format = 'json'
    ) {
        return parent::showAction($request, $exception, $logger, $format);
    }
}
