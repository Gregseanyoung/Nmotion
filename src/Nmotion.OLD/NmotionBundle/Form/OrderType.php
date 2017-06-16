<?php

namespace Nmotion\NmotionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'integer', ['disabled' => true])
            ->add('pushToken', 'text', ['required' => false])
            ->add('contactPhoneNumber', 'text', ['required' => false])
            ->add('takeawayPickupTime', 'integer', ['disabled' => true])
            ->add(
                'orderMeals',
                new Type\IdBasedCollectionType(),
                [
                    'type'         => new OrderMealType(),
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'by_reference' => false
                ]
            )
            ->add(
                'tips',
                'number',
                [
                    'required'   => false,
                    'precision'  => 2
                ]
            )
            ->add('createdAt', 'integer', ['disabled' => true])
            ->add('updatedAt', 'integer', ['disabled' => true]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'           => 'Nmotion\NmotionBundle\Entity\Order',
                'csrf_protection'      => false,
                'cascade_validation'   => true,
                'extra_fields_message' => 'This form should not contain extra fields.{{ extra_fields }}'
            ]
        );
    }

    public function getName()
    {
        // no name by default
        return '';
    }
}
