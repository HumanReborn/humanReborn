<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EIP\HRBundle\Entity\HRTechnologySchema;

/**
	\class HRTechnologySchemaType
	\brief Set the form fields, requirement, security, data-transformers if required
*/
class HRTechnologySchemaType extends AbstractType {

  /**
	\fn buildForm(FormBuilderInterface, array)
	\brief set the different fields of the form
	@param FormBuilderInterface $builder
	@param array $options	
  */
  public function buildForm(FormBuilderInterface $builder, array $options) {
     $builder->add('name', 'text')
             ->add('buildingTime', 'integer')
             ->add('waterCost', 'integer')
             ->add('pureWaterCost', 'integer')
             ->add('steelCost', 'integer')
             ->add('fuelCost', 'integer')
             ->add('technologyRequirement', 'integer')
             ->add('buildingRequirement', 'integer')
             ->add('rValue', 'integer')
             ->add('type', 'integer');
  }

  /**
	\fn String getName(void)
	\brief required by Symfony2
	@return String
  */
  public function getName() {
    return 'hrtechnologyschematype';
  }

  /**
	\fn String getDefaultOptions(array)
	\brief required by Symfony2
	@param array $options
	@return array of options
  */
  public function getDefaultOptions(array $options)  {
        return array(
            'data_class' => 'EIP\HRBundle\Entity\HRTechnologySchema',
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
            'data_class'      => 'EIP\HRBundle\Entity\HRTechnologySchema',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => '758655894',
        ));
    }

}
