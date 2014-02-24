<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
	\class HRSchemaType
	\brief Set the form fields, requirement, security, data-transformers if required
*/
class HRItemSchemaType extends AbstractType {

  /**
	\fn buildForm(FormBuilderInterface, array)
	\brief set the different fields of the form
	@param FormBuilderInterface $builder
	@param array $options
  */
  public function buildForm(FormBuilderInterface $builder, array $options) {
     $builder
    ->add('name')
    ->add('image')
    ->add('buffSchema', 'entity', array(
            'class' => 'EIPHRBundle:HRBuffSchema',
            'property' => 'name',
            'empty_value' => 'null',
            'required' => false,
        ))
    ->add('resourceName','text',  array('required' => false))
    ->add('unitSchema', 'entity', array(
            'class' => 'EIPHRBundle:HRUnitSchema',
            'property' => 'name',
            'empty_value' => 'null',
            'required' => false
        ))
    ->add('value', 'text', array('required' => false))
    ->add('description', 'textarea', array('required' => false));

  }

  /**
	\fn String getName(void)
	\brief required by Symfony2
	@return String
  */
  public function getName() {
    return 'hritemschematype';
  }

  /**
	\fn String getDefaultOptions(array)
	\brief required by Symfony2
	@param array $options
	@return array of options
  */
  public function getDefaultOptions(array $options)  {
        return array(
            'data_class' => 'EIP\HRBundle\Entity\HRItemSchema',
        );
  }

  /**
	\fn String setDefaultOptions(OptionsResolverInterface)
	\brief required by Symfony2
	@param OptionsResolverInterface $resolver
  */
  public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'EIP\HRBundle\Entity\HRItemSchema',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => '336966998785',
        ));
    }

}
