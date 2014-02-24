<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use EIP\HRBundle\Entity\HRClanMembers;

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of HRClanMembersHandler
 *
 * @author Xalarrito
 */
class HRClanMembersHandler extends HRClanMembersType {
 protected $form;
  protected $request;
  protected $em;
  protected $create;
    
  public function __construct(Form $form, Request $request, EntityManager $em, $create = false){
    $this->form = $form;
    $this->request = $request;
    $this->em = $em;
    $this->create = $create;
  }
  
  public function process($member) {
    if ($this->request->getMethod() == 'POST') {
        $this->form->bind($this->request);
        if ($this->form->isValid()) {
            $member->setIdUser($this->form->getData()->getIdUser()->getId());
            $member->setIdRank($this->form->getData()->getIdRank()->getId());
            //$this->onSuccess($this->form->getData());
            return $member;
        }
    }
    return false;
  }

  public function onSuccess(HRClanMembers $clan) {
        if ($this->create) {
            $this->em->persist($clan);
        }
        $this->em->flush();
 }
    
}

?>
