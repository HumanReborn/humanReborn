<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
	\class HRBuildingSchemaAdmType
	\brief Set the form fields, requirement, security, data-transformers if required	
*/
class HRBuildingSchemaAdmType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
     $builder->add('name', 'text')
             ->add('buildingTime', 'integer')
             ->add('buildingRequirement', 'integer')
             ->add('technologyRequirement', 'integer')
             ->add('waterCost', 'integer')
             ->add('pureWaterCost', 'integer')
             ->add('steelCost', 'integer')
             ->add('fuelCost', 'integer')
             ->add('waterCollectRate', 'integer')
             ->add('pureWaterCollectRate', 'integer')
             ->add('steelCollectRate', 'integer')
             ->add('fuelCollectRate', 'integer')
             ->add('rValue','integer')
          ;
  }

  /**
	\fn String getName(void)
	\brief required by Symfony2
	@return String
  */
  public function getName() {
    return 'hrbuildingschemaadmtype';
  }

  /**
	\fn String getDefaultOptions(array)
	\brief required by Symfony2
	@param array $options
	@return array of options
  */
  public function getDefaultOptions(array $options)  {
        return array(
            'data_class' => 'EIP\HRBundle\Entity\HRBuildingSchema',
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
            'data_class'      => 'EIP\HRBundle\Entity\HRBuildingSchema',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => '56696843',
        ));
    }

}
