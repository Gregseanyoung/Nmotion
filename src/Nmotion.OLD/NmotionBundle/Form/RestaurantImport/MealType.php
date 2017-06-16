<?php

namespace Nmotion\NmotionBundle\Form\RestaurantImport;

use Nmotion\NmotionBundle\Form\MealExtraIngredientType;
use Nmotion\NmotionBundle\Form\MealOptionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MealType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'integer', ['disabled' => true])
            ->add('name')
            ->add('description')
            ->add('timeFrom')
            ->add('timeTo')
            ->add('logoAsset', new AssetType)
            ->add('thumbLogoAsset', new AssetType)
            ->add('price')
            ->add('priceIncludingTax')
            ->add('discountPercent', 'integer', ['error_bubbling' => true])
            ->add('visible')
            ->add(
                'mealOptions',
                'collection',
                [
                    'type'         => new MealOptionType,
                    'allow_add'    => true,
                    'by_reference' => false
                ]
            )
            ->add(
                'mealExtraIngredients',
                'collection',
                [
                    'type'         => new MealExtraIngredientType,
                    'allow_add'    => true,
                    'by_reference' => false
                ]
            )
            ->add('mealOptionDefaultId', 'integer', ['required' => false])
            ->add('position', 'integer')
            ->add('createdAt', 'integer')
            ->add('updatedAt', 'integer');
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
