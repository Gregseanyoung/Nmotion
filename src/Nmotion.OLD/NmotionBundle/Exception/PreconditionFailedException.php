<?php

namespace Nmotion\NmotionBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * PreconditionFailedException.
 *
 * @author Sergey Shupylo <sshu@ciklum.com>
 */
class PreconditionFailedException extends HttpException
{
    const ORDER_PAYING_BY_OTHER = 1005001;
    const ORDER_PAID_BY_OTHER = 1005002;

    /**
     * Constructor.
     *
     * @param string     $message  The internal exception message
     * @param \Exception $previous The previous exception
     * @param integer    $code     The internal exception code
     */
    public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(412, $message, $previous, array(), $code);
    }
}
