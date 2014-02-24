<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
	\class HRQuestSchemaType
	\brief Set the form fields, requirement, security, data-transformers if required
*/
class HRQuestSchemaType extends AbstractType {

  /**
	\fn buildForm(FormBuilderInterface, array)
	\brief set the different fields of the form
	@param FormBuilderInterface $builder
	@param array $options
  */
  public function buildForm(FormBuilderInterface $builder, array $options) {
     $builder->add('name', 'text')
             ->add('type', 'choice', array(
                 'choices' => array(
                     \EIP\HRBundle\Entity\HRQuestSchema::GIVE_RESOURCES => 'resources',
                     \EIP\HRBundle\Entity\HRQuestSchema::DESTROY_UNIT => 'destroy' ,
                     \EIP\HRBundle\Entity\HRQuestSchema::BUILD => 'build'
                 )
             ))
             ->add('xpReward')
             ->add('description')
             ->add('img')
             ->add('once', 'checkbox', array('required'=>false))
             ->add('itemReward', 'entity', array(
                 'class' => 'EIPHRBundle:HRItemSchema',
                 'multiple' => true,
                 'property' => 'name',
                 'required' => false
             ))
          ;
  }

  /**
	\fn String getName(void)
	\brief required by Symfony2
	@return String
  */
  public function getName() {
    return 'hrquestschematype';
  }

  /**
	\fn String getDefaultOptions(array)
	\brief required by Symfony2
	@param array $options
	@return array of options
  */
  public function getDefaultOptions(array $options)  {
        return array(
            'data_class' => 'EIP\HRBundle\Entity\HRQuestSchema',
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
            'data_class'      => 'EIP\HRBundle\Entity\HRQuestSchema',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => '22335457',
        ));

    }

}
