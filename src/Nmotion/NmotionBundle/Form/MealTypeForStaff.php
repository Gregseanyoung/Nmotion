<?php

namespace Nmotion\NmotionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MealTypeForStaff extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            // fields that restaurant staff is allowed to change
            ->add('visible')
            // fields that restaurant staff is not allowed to change
            ->add('id', null, ['disabled' => true])
            ->add('name', null, ['disabled' => true])
            ->add('description', null, ['disabled' => true])
            ->add('timeFrom', null, ['disabled' => true])
            ->add('timeTo', null, ['disabled' => true])
            ->add('logoAsset', null, ['disabled' => true])
            ->add('thumbLogoAsset', null, ['disabled' => true])
            ->add('price', null, ['disabled' => true])
            ->add('priceIncludingTax', null, ['disabled' => true])
            ->add('discountPercent', null, ['disabled' => true])
            ->add('mealOptions', null, ['disabled' => true])
            ->add('mealExtraIngredients', null, ['disabled' => true])
            ->add('mealOptionDefaultId', null, ['disabled' => true])
            ->add('position', null, ['disabled' => true])
            ->add('createdAt', null, ['disabled' => true])
            ->add('updatedAt', null, ['disabled' => true]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                 'data_class'           => 'Nmotion\NmotionBundle\Entity\Meal',
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
