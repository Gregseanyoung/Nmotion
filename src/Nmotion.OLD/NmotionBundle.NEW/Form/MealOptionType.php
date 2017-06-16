<?php

namespace Nmotion\NmotionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MealOptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'integer', ['disabled' => true])
            ->add('name')
            ->add('price')
            ->add('priceIncludingTax')
            ->add('createdAt', 'integer', ['disabled' => true])
            ->add('updatedAt', 'integer', ['disabled' => true])
            ->add('isDefault', null, ['virtual' => true]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                 'data_class'           => 'Nmotion\NmotionBundle\Entity\MealOption',
                 'csrf_protection'      => false,
                 'cascade_validation'   => true,
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
