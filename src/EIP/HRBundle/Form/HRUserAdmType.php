<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EIP\HRBundle\Entity\HRUser;

/**
	\class HRUserAdmType
	\brief Set the form fields, requirement, security, data-transformers if required
*/
class HRUserAdmType extends AbstractType {

  /**
	\fn buildForm(FormBuilderInterface, array)
	\brief set the different fields of the form
	@param FormBuilderInterface $builder
	@param array $options	
  */
  public function buildForm(FormBuilderInterface $builder, array $options) {
     $builder->add('email', 'text')
            ->add('username', 'text')
             ->add('status', 'choice', array('choices' => array(HRUser::STATUS_CLOSED => 'closed',
                                                        HRUser::STATUS_PENDING => 'pending',
                                                         HRUser::STATUS_CONFIRMED => 'confirmed')))
            ->add('createdOn', 'date')
            ->add('lastLogin', 'date');
  }

  /**
	\fn String getName(void)
	\brief required by Symfony2
	@return String
  */
  public function getName() {
    return 'hruseradmtype';
  }

  /**
	\fn String getDefaultOptions(array)
	\brief required by Symfony2
	@param array $options
	@return array of options
  */
  public function getDefaultOptions(array $options)  {
        return array(
            'data_class' => 'EIP\HRBundle\Entity\HRUser',
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
            'data_class'      => 'EIP\HRBundle\Entity\HRUser',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => '9183734',
        ));
    }

}
