<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use EIP\HRBundle\Entity\HRTechnologySchema;

/**
	\class HRTechnologySchemaHandler
	\brief Manages the create and edit action for the HRTechnologySchema entities
*/
class HRTechnologySchemaHandler {

  protected $form; /**< form submitted */
  protected $request; /**< http request object */
  protected $em; /**< entity manager */
  protected $create; /**< true if the entity is to be created */

  /**
	\fn constructor
	@param Form $form
	@param Request $request
	@param EntityManager $em
	@param bool $createFlag
  */
  public function __construct(Form $form, Request $request, EntityManager $em, $create = false) {
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
	\fn onSuccess(HRTechnologySchema)
	\brief Create or edit the entity
  */
  public function onSuccess(HRTechnologySchema $techno) {
    if ($this->create) {
        $this->em->persist($techno);
    }
    $this->em->flush();
  }

}
