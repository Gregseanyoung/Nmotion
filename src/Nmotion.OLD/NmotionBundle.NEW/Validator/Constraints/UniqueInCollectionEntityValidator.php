<?php

namespace Nmotion\NmotionBundle\Validator\Constraints;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Unique Entity in the Collection Validator checks if one or a set of fields contain unique values.
 *
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @author Vasiliy Samsonnikov <vas@ciklum.com>
 */
class UniqueInCollectionEntityValidator extends ConstraintValidator
{
    /**
     * @var ManagerRegistry
     */
    private $registry;

    private $collectionValues = [];

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param object     $entity
     * @param Constraint $constraint
     */
    public function validate($entity, Constraint $constraint)
    {
        if (!is_array($constraint->fields) && !is_string($constraint->fields)) {
            throw new UnexpectedTypeException($constraint->fields, 'array');
        }

        if (null !== $constraint->errorPath && !is_string($constraint->errorPath)) {
            throw new UnexpectedTypeException($constraint->errorPath, 'string or null');
        }

        $fields = (array) $constraint->fields;

        if (0 === count($fields)) {
            throw new ConstraintDefinitionException('At least one field has to be specified.');
        }

        if ($constraint->em) {
            $em = $this->registry->getManager($constraint->em);
        } else {
            $em = $this->registry->getManagerForClass(get_class($entity));
        }

        $className = $this->context->getCurrentClass();
        $class     = $em->getClassMetadata($className);
        /* @var $class \Doctrine\Common\Persistence\Mapping\ClassMetadata */

        $criteria = array();
        foreach ($fields as $fieldName) {
            if (!$class->hasField($fieldName) && !$class->hasAssociation($fieldName)) {
                throw new ConstraintDefinitionException(
                    sprintf(
                        "The field '%s' is not mapped by Doctrine, so it cannot be validated for uniqueness.",
                        $fieldName
                    )
                );
            }

            $criteria[$fieldName] = $class->reflFields[$fieldName]->getValue($entity);

            if ($constraint->ignoreNull && null === $criteria[$fieldName]) {
                return;
            }

            if ($class->hasAssociation($fieldName)) {
                /* Ensure the Proxy is initialized before using reflection to
                 * read its identifiers. This is necessary because the wrapped
                 * getter methods in the Proxy are being bypassed.
                 */
                $em->initializeObject($criteria[$fieldName]);

                $relatedClass = $em->getClassMetadata($class->getAssociationTargetClass($fieldName));
                $relatedId    = $relatedClass->getIdentifierValues($criteria[$fieldName]);

                if (count($relatedId) > 1) {
                    throw new ConstraintDefinitionException(
                        "Associated entities are not allowed to have more than one identifier field to be " .
                        "part of a unique constraint in: " . $class->getName() . "#" . $fieldName
                    );
                }
                $criteria[$fieldName] = array_pop($relatedId);
            }
        }

        if (in_array($criteria, $this->collectionValues)) {
            $errorPath = null !== $constraint->errorPath ? $constraint->errorPath : $fields[0];
            $this->context->addViolationAt($errorPath, $constraint->message, array(), $criteria[$fields[0]]);
        }

        $entityIdentifier = $class->getIdentifierValues($entity);
        if (!$constraint->justNew || empty($entityIdentifier)) {
            $this->collectionValues[] = $criteria;
        }
    }
}
