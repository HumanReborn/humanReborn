<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class HRHeroSchemaType extends AbstractType {

  public function buildForm(FormBuilderInterface $builder, array $options) {
     $builder->add('name', 'text')
          ->add('description', 'textarea')
          ->add('img');
  }


  public function getName() {
    return 'hrheroschematype';
  }


  public function getDefaultOptions(array $options)  {
        return array(
            'data_class' => 'EIP\HRBundle\Entity\HRHeroSchema',
        );
  }


  public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'EIP\HRBundle\Entity\HRHeroSchema',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => '3409938802',
        ));
    }

}
