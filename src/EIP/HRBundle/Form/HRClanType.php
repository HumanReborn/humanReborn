<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HRClanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text')
            ->add('acronym', 'text')
            ->add('banner', 'text', array('required' => false))
            ->add('privatePresentation', 'textarea', array('required' => false, 'attr' => array("cols" => 90, "rows" => 12)))
            ->add('publicPresentation', 'textarea', array('required' => false, 'attr' => array("cols" => 90, "rows" => 12)))
            ->add('recruitmentStatut', 'checkbox', array('required' => false))
            //->add('createdOn') /* pas dinput, cuurentDate() envoyer en base direct*/
            //->add('idGame') /* pas dinput id de la game en cours recuperé */
        ;
    }
   

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'      => 'EIP\HRBundle\Entity\HRClan',
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'intention'       => 'UNTRUCAUPIF',
        ));
    }

    public function getName()
    {
        return 'eip_hrbundle_hrclantype';
    }
    
}
