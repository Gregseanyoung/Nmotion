<?php
/**
 * @author tiger
 */

namespace Nmotion\NmotionBundle\Validator\Constraints;

use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraint;

class UniqueUserEmail extends Constraint
{
    public $message = 'This value is already used.';
    public $service = 'nmotion.validator.uniqueUserEmail';
    public $em = null;
    public $errorPath = null;
    public $ignoreNull = true;

    /**
     * The validator must be defined as a service with this name.
     *
     * @return string
     */
    public function validatedBy()
    {
        return $this->service;
    }

    /**
     * {@inheritDoc}
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
