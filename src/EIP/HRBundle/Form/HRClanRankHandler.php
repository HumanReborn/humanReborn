<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use EIP\HRBundle\Entity\HRClanRank;

/**
 * Description of HRClanRankHandler
 *
 * @author Chaveex
 */
class HRClanRankHandler extends HRClanRankType{
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
  
  public function process($repo) {
    if ($this->request->getMethod() == 'POST') {
        $this->form->bind($this->request);
        $res = $repo->isThisRankExist($this->form->getData()->getName(), $this->form->getData()->getIdClan());
        if ($res)
            return 2;
        if ($this->form->isValid()) {
            $this->onSuccess($this->form->getData());
            return 1;
        }
    }
    return 0;
  }

  public function onSuccess(HRClanRank $clan) {
        if ($this->create) {
            $this->em->persist($clan);
        }
        $this->em->flush();
 }
    
}

?>
