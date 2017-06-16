<?php

namespace Nmotion\NmotionBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConfigType extends AbstractType
{
    /**
     * @var string|null
     */
    private $valueType;

    public function __construct($valueType = null)
    {
        $this->valueType = $valueType;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', 'integer', ['disabled' => true])
            ->add('name')
            ->add('value', $this->valueType)
            ->add('description', 'text', ['required' => false])
            ->add('system')
            ->add('type', 'text')
            ->add('createdAt', 'integer', ['disabled' => true])
            ->add('updatedAt', 'integer', ['disabled' => true]);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            array(
                 'data_class'           => 'Nmotion\NmotionBundle\Entity\Config',
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
