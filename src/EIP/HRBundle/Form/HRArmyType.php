<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
	\class HRArmyType
	\brief Set the form fields, requirement, security, data-transformers if required
*/
class HRArmyType extends AbstractType {

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
    $townTransformer = new DataTransformers\StringToTownTransformer($em);
     $builder
    ->add(
            $builder->create('user', 'text')
            ->addModelTransformer($userTransformer)
            )
    ->add(
            $builder->create('game', 'text')
            ->addModelTransformer($gameTransformer)
             )
      ->add(
              $builder->create('town', 'text')
            ->addModelTransformer($townTransformer)
              )
      ->add('garrison', 'choice', array(
        'choices' => array(false => 'false', true => 'true')
        ))
      ->add('moving', 'choice', array(
          'choices' => array(false => 'false', true => 'true')
      ));
  }

  /**
	\fn String getName(void)
	\brief required by Symfony2
	@return String
  */
  public function getName() {
    return 'hrarmytype';
  }

  /**
	\fn String getDefaultOptions(array)
	\brief required by Symfony2
	@param array $options
	@return array of options
  */
  public function getDefaultOptions(array $options)  {
        return array(
            'data_class' => 'EIP\HRBundle\Entity\HRArmy',
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
            'data_class'      => 'EIP\HRBundle\Entity\HRArmy',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => '78959234',
        ));
        $resolver->setRequired(array(
            'em',
        ));
        $resolver->setAllowedTypes(array(
            'em' => 'Doctrine\Common\Persistence\ObjectManager',
        ));
    }

}
