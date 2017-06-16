<?php

namespace Nmotion\NmotionBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * NotModifiedException.
 *
 * @author Sergey Shupylo <sshu@ciklum.com>
 */
class NotModifiedException extends HttpException
{
    /**
     * Constructor.
     *
     * @param string     $message  The internal exception message
     * @param \Exception $previous The previous exception
     * @param integer    $code     The internal exception code
     */
    public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(304, $message, $previous, array(), $code);
    }
}
