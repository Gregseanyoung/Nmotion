<?php

namespace Nmotion\NmotionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RestaurantAddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', null, ['disabled' => true])
            ->add('longitude')
            ->add('latitude')
            ->add('addressLine1')
            ->add('city')
            ->add('state')
            ->add('postalCode');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                 'data_class'           => 'Nmotion\NmotionBundle\Entity\RestaurantAddress',
                 'csrf_protection'      => false,
                 'extra_fields_message' => 'This form should not contain extra fields.{{ extra_fields }}'
            )
        );
    }

    public function getName()
    {
        // no name by default
        return '';
    }
}
