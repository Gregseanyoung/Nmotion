<?php

namespace Nmotion\NmotionBundle\Form\RestaurantImport;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AssetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', null, ['disabled' => true])
            ->add('url', null, ['disabled' => true])
            ->add('mimeType', null, ['required' => false])
            ->add('name', null, ['required' => false])
            ->add('originalFilename', null, ['required' => false])
            ->add('width', null, ['required' => false])
            ->add('height', null, ['required' => false])
            ->add('md5', null, ['required' => true])
            ->add('createdAt', null, ['required' => false])
            ->add('updatedAt', null, ['required' => false]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nmotion\NmotionBundle\Entity\Asset'
        ));
    }

    public function getName()
    {
        return 'nmotion_nmotionbundle_assettype';
    }
}
