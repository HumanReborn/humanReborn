<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HRClanMembersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $idClan = $options['data']->getIdClan();
        $builder
            ->add('id', 'hidden')
            ->add('idClan', 'hidden')
            ->add('idUser', 'entity', array (
                'class' => 'EIP\HRBundle\Entity\HRUser',
                'query_builder' => function (\EIP\HRBundle\Entity\HRUserRepository $repository) use($idClan) {
                    return $repository->createQueryBuilder('u')
                            ->leftJoin("EIPHRBundle:HRClanMembers", "m", "WITH", "m.idUser = u.id")
                            ->where("m.idClan = :clanid")->setParameter(':clanid', $idClan);
                },
                'property' => 'username'
            ))
            ->add('idRank', 'entity', array (
                'class' => 'EIP\HRBundle\Entity\HRClanRank',
                'query_builder' => function (\EIP\HRBundle\Entity\HRClanRankRepository $repository)use($idClan) {
                   return $repository->createQueryBuilder('u')
                        ->where('u.idClan = :idClan')->setParameter(':idClan', $idClan);

                 },
                 'property' => 'name',
            ));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EIP\HRBundle\Entity\HRClanMembers'
        ));
    }
    
    public function getName()
    {
        return 'eip_hrbundle_hrclanmemberstype';
    }
}

/*
 *                 'query_builder' => function (\EIP\HRBundle\Entity\HRClanRankRepository $repository)use($idClan) {
                    $res = $repository->getAllRankFromClanForForm($idClan);
                    var_dump($res);
                    return $res;
                 },
 */