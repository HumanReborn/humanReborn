<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use EIP\HRBundle\Entity\HRGame;

/**
	\class HRGameHandler
	\brief Manages the create and edit action for the HRGame entities
*/
class HRGameHandler {

  protected $form; /**< form submitted */
  protected $request; /**< http request object */
  protected $em; /**< entity manager */
  protected $create; /**< create flag */

  /**
	\fn constructor
	@param Form $form
	@param Request $request
	@param EntityManager $em
  */
  public function __construct(Form $form, Request $request, EntityManager $em, $create){
    $this->form = $form;
    $this->request = $request;
    $this->em = $em;
    $this->create = $create;
  }

  /**
	\fn process(void)
	\brief check if the form is submitted and if the data contained within is valid
	@return true if the form is correctly submitted, else otherwise
  */
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

  /**
	\fn onSuccess(HRGame)
	\brief Create or edit the entity
  */
  public function onSuccess(HRGame $game) {
    if ($this->create) {
        $this->em->persist($game);
    }
    $this->em->flush();
  }

}
