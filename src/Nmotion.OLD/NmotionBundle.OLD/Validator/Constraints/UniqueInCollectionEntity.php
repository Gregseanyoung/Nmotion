<?php

namespace Nmotion\NmotionBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Constraint for the Unique in the Collection Entity validator
 *
 * @Annotation
 * @author Vasiliy Samsonnikov <vas@ciklum.com>
 */
class UniqueInCollectionEntity extends Constraint
{
    public $message = 'This value is already used.';
    public $service = 'nmotion.validator.uniqueInCollectionEntity';
    public $em      = null;

    public $fields     = array();
    public $errorPath  = null;
    public $ignoreNull = false;
    public $justNew    = false;

    public function getRequiredOptions()
    {
        return array('fields');
    }

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

    public function getDefaultOption()
    {
        return 'fields';
    }
}
