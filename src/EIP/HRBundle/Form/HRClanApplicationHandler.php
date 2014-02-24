<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use EIP\HRBundle\Entity\HRClanApplication;

/**
 * Description of HRClanApplicationHandler
 *
 * @author Chaveex
 */
class HRClanApplicationHandler extends HRClanApplicationType{
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
  
  public function process() {
    if ($this->request->getMethod() == 'POST') {
        $this->form->bind($this->request);
        if ($this->form->isValid()) {
            $this->onSuccess($this->form->getData());
            return true;
        }
    }
    return false;
  }

  public function onSuccess(HRClanApplication $clan) {
        if ($this->create) {
            $this->em->persist($clan);
        }
        $this->em->flush();
 }
    
}

?>