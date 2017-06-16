<?php

namespace Nmotion\NmotionBundle\Exception;

/**
 * PreconditionFailedException.
 *
 * @author Sergey Shupylo <sshu@ciklum.com>
 */
class RestaurantCheckinExpiredException extends PreconditionFailedException
{
    /**
     * Constructor.
     *
     * @param string     $message  The internal exception message
     * @param \Exception $previous The previous exception
     */
    public function __construct($message = null, \Exception $previous = null)
    {
        parent::__construct($message, $previous);
    }
}
