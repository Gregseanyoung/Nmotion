<?php

namespace Nmotion\NmotionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RestaurantUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', null, ['disabled' => true])
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('plainPassword', null, ['disabled' => true])
            ->add('roles', null, ['disabled' => true])
            ->add('registered', null, ['disabled' => true]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'           => 'Nmotion\NmotionBundle\Entity\User',
                'csrf_protection'      => false,
                'extra_fields_message' => 'This form should not contain extra fields.{{ extra_fields }}',
                'validation_groups'    => ['registration']
            ]
        );
    }

    public function getName()
    {
        // no name by default
        return '';
    }
}
