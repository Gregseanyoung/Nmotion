<?php

namespace Nmotion\NmotionBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * ConflictException.
 *
 * @author Samsonnikov Vasiliy <vas@ciklum.com>
 */
class ConflictException extends HttpException
{
    const MEAL_NOT_AVAILABLE_MEAL_NOT_VISIBLE          = 10050021;
    const MEAL_NOT_AVAILABLE_CATEGORY_NOT_VISIBLE      = 10050022;
    const MEAL_NOT_AVAILABLE_MEAL_TIME_UNAVAILABLE     = 10050023;
    const MEAL_NOT_AVAILABLE_CATEGORY_TIME_UNAVAILABLE = 10050024;

    /**
     * Constructor.
     *
     * @param string     $message  The internal exception message
     * @param \Exception $previous The previous exception
     * @param integer    $code     The internal exception code
     */
    public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct(409, $message, $previous, array(), $code);
    }
}
