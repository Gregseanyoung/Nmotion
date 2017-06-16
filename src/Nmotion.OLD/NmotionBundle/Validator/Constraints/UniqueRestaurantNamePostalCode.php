<?php

namespace Nmotion\NmotionBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint for the Unique restaurant name + address postal code validator
 *
 * @author samva <vas@ciklum.com>
 */
class UniqueRestaurantNamePostalCode extends Constraint
{
    public $message = 'Restaurant with this name and postal code already exists.';
    public $service = 'nmotion.validator.uniqueRestaurantNameAndPostalCode';
    public $em = null;

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
