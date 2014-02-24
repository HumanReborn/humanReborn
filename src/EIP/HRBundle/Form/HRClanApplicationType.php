<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HRClanApplicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message', 'textarea', array('attr' => array("cols" => 150, "rows" => 50)))
           //->add('idUser')
           //->add('idClan')
           // ->add('pending')
        ;
    }
    
    public function getDefaultOptions(array $options)  {
        return array(
            'data_class' => 'EIP\HRBundle\Entity\HRClanApplication',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EIP\HRBundle\Entity\HRClanApplication'
        ));
    }

    public function getName()
    {
        return 'eip_hrbundle_hrclanapplicationtype';
    }
}
