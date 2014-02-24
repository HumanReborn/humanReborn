<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use EIP\HRBundle\Entity\HRHeroSchema;


class HRHeroSchemaHandler {

  protected $form; /**< form submitted */
  protected $request; /**< http request object */
  protected $em; /**< entity manager */
  protected $create;  /**< true if the entity is to be created */

  public function __construct(Form $form, Request $request, EntityManager $em, $createFlag){
    $this->form = $form;
    $this->request = $request;
    $this->em = $em;
    $this->create = $createFlag;
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

  public function onSuccess(HRHeroSchema $town) {
    if ($this->create) {
        $this->em->persist($town);
    }
    $this->em->flush();
  }

}
