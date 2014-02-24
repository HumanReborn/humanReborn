<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EIP\HRBundle\Entity\HRMessage;

/**
	\class HRMessageType
	\brief Set the form fields, requirement, security, data-transformers if required
*/
class HRMessageType extends AbstractType {

  /**
	\fn buildForm(FormBuilderInterface, array)
	\brief set the different fields of the form
	@param FormBuilderInterface $builder
	@param array $options	
  */
  public function buildForm(FormBuilderInterface $builder, array $options) {
      $em = $options['em'];
      $userTransformer = new DataTransformers\StringToUserTransformer($em);

     $builder->add('title', 'text')
             ->add(
                    $builder->create('receiver', 'text')
                    ->addModelTransformer($userTransformer)
                    );
     if ($this->displaySender)
     {
             $builder->add(
                     $builder->create('sender', 'text')
                     ->addModelTransformer($userTransformer)
                     );
     }
     $builder->add('content', 'textarea', array('attr' => array("cols" => 50, "rows" => 6)));

  }

  /**
	\fn String getName(void)
	\brief required by Symfony2
	@return String
  */
  public function getName() {
    return 'hrmessagetype';
  }

  /**
	\fn String getDefaultOptions(array)
	\brief required by Symfony2
	@param array $options
	@return array of options
  */
  public function getDefaultOptions(array $options)  {
        return array(
            'data_class' => 'EIP\HRBundle\Entity\HRMessage',
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
            'data_class'      => 'EIP\HRBundle\Entity\HRMessage',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => '758655894',
        ));
        $resolver->setRequired(array(
            'em',
        ));
        $resolver->setAllowedTypes(array(
            'em' => 'Doctrine\Common\Persistence\ObjectManager',
        ));
    }

  /**
	\fn Constructor	
	@param boolean $displaySender
  */
	public function __construct($displaySender) {
		$this->displaySender = $displaySender;
	}

}
