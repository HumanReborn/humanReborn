<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EIP\HRBundle\Entity\HRTown;

/**
	\class HRTownType
	\brief Set the form fields, requirement, security, data-transformers if required
*/
class HRTownType extends AbstractType {

  /**
	\fn buildForm(FormBuilderInterface, array)
	\brief set the different fields of the form
	@param FormBuilderInterface $builder
	@param array $options
  */
  public function buildForm(FormBuilderInterface $builder, array $options) {
     $em = $options['em'];
     $userTransformer = new DataTransformers\StringToUserTransformer($em);
     $gameTransformer = new DataTransformers\StringToGameTransformer($em);
     $builder->add('name', 'text')
            ->add('xCoord', 'integer')
             ->add('yCoord', 'integer')
             ->add(
                     $builder->create('game', 'text', array('label'=>'GameName'))
                     ->addModelTransformer($gameTransformer)
                     )
             ->add(
                     $builder->create('owner', 'text',array('label'=>'OwnerName'))
                     ->addModelTransformer($userTransformer)
                     )
             ;
  }

  /**
	\fn String getName(void)
	\brief required by Symfony2
	@return String
  */
  public function getName() {
    return 'hrtowntype';
  }

  /**
	\fn String getDefaultOptions(array)
	\brief required by Symfony2
	@param array $options
	@return array of options
  */
  public function getDefaultOptions(array $options)  {
        return array(
            'data_class' => 'EIP\HRBundle\Entity\HRTown',
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
            'data_class'      => 'EIP\HRBundle\Entity\HRTown',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => '546983535',
        ));
        $resolver->setRequired(array(
            'em',
        ));
        $resolver->setAllowedTypes(array(
            'em' => 'Doctrine\Common\Persistence\ObjectManager',
        ));
    }

}

