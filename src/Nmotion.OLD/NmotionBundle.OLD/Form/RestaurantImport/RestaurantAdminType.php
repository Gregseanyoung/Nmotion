<?php

namespace Nmotion\NmotionBundle\Form\RestaurantImport;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RestaurantAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', null, ['disabled' => true])
            ->add('email')
            ->add('username')
            ->add('firstName')
            ->add('lastName')
            ->add('password')
            ->add('roles')
            ->add('registered');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                 'data_class'           => 'Nmotion\NmotionBundle\Entity\User',
                 'csrf_protection'      => false,
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
