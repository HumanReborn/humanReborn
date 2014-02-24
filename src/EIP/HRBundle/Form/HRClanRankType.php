<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HRClanRankType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('CanFireMember', 'checkbox', array('required' => false))
            ->add('CanAcceptNewMember', 'checkbox', array('required' => false))
            ->add('CanEditText', 'checkbox', array('required' => false))
            ->add('CanDeclareWar', 'checkbox', array('required' => false))
            ->add('CanCreateRank', 'checkbox', array('required' => false))
            ->add('canDeleteClan', 'checkbox', array('required' => false))
        ;
    }
    
    public function getDefaultOptions(array $options)  {
        return array(
            'data_class' => 'EIP\HRBundle\Entity\HRClanRank',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EIP\HRBundle\Entity\HRClanRank'
        ));
    }

    public function getName()
    {
        return 'eip_hrbundle_hrclanranktype';
    }
}
