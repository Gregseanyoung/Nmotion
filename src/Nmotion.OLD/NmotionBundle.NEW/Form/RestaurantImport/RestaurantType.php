<?php

namespace Nmotion\NmotionBundle\Form\RestaurantImport;

use Nmotion\NmotionBundle\Form\RestaurantAddressType;
use Nmotion\NmotionBundle\Form\RestaurantOperationTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'integer', ['disabled' => true])
            ->add('name', 'text')
            ->add('fullDescription', 'text')
            ->add('logoAsset', null, ['disabled' => true, 'mapped' => false])
            ->add('facebookPlaceId')
            ->add('feedbackUrl')
            ->add('timeZone', null, ['disabled' => true])
            ->add('checkOutTime')
            ->add('visible', null, ['required' => false])
            ->add('takeaway', null, ['required' => false])
            ->add('taMember', null, ['required' => false])
            ->add('email')
            ->add('phone')
            ->add('siteUrl')
            ->add('contactPersonName')
            ->add('contactPersonEmail')
            ->add('contactPersonPhone')
            ->add('legalEntity', 'text')
            ->add(
                'invoicingPeriod',
                'choice',
                [
                    'choices'  => [
                       'monthly' => 'monthly',
                       'weekly' => 'weekly',
                       '14 days' => '14 days',
                    ],
                    'required' => false,
                ]
            )
            ->add('vatNo', 'text')
            ->add('regNo', 'text')
            ->add('kontoNo', 'text')
            ->add('address', new RestaurantAddressType)
            ->add('adminUser', new RestaurantAdminType)
            ->add(
                'operationTimes',
                'collection',
                [
                    'type'         => new RestaurantOperationTimeType,
                    'allow_add'    => true,
                    'by_reference' => false
                ]
            )
            ->add(
                'menuCategories',
                'collection',
                [
                    'type'         => new MenuCategoryType,
                    'allow_add'    => true,
                    'by_reference' => false
                ]
            )
            ->add('createdAt', 'integer')
            ->add('updatedAt', 'integer');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                 'data_class'           => 'Nmotion\NmotionBundle\Entity\Restaurant',
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
