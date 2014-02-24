<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use EIP\HRBundle\Entity\HRUser;

/**
	\class HRUserAdmHandler
	\brief Manages the create and edit action for the HRUser entities
*/
class HRUserAdmHandler {

  protected $form; /**< form submitted */
  protected $request; /**< http request object */
  protected $em; /**< entity manager */

  /**
	\fn constructor
	@param Form $form
	@param Request $request
	@param EntityManager $em
  */
  public function __construct(Form $form, Request $request, EntityManager $em){
    $this->form = $form;
    $this->request = $request;
    $this->em = $em;
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
            $this->onSuccess();
            return true;
        }
    }
    return false;
  }

  /**
	\fn onSuccess(void)
	\brief Create or edit the entity
  */
  public function onSuccess() {
    $this->em->flush();
  }

}
