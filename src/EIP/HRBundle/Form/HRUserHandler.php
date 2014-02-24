<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use EIP\HRBundle\Entity\HRUser;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

/**
	\class HRUserHandler
	\brief Manages the create and edit action for the HRUser entities
*/
class HRUserHandler {

  protected $form; /**< form submitted */
  protected $request; /**< http request object */
  protected $em;  /**< entity manager */
  protected $factory; /**< EncoderFactory for the password */

  /**
	\fn constructor
	@param Form $form
	@param Request $request
	@param EntityManager $em
	@param EncoderFactory $factory
  */
  public function __construct(Form $form, Request $request, EntityManager $em, EncoderFactory $factory){
    $this->form = $form;
    $this->request = $request;
    $this->em = $em;
    $this->factory = $factory;
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
	\fn onSuccess(HRUser)
	\brief Create or edit the entity
  */
  public function onSuccess(HRUser $user) {
    $hashedPassword = $this->factory
            ->getEncoder($user)
            ->encodePassword($user->getPassword(), $user->getSalt());
    $user->setPassword($hashedPassword);
    $this->em->persist($user);
    $this->em->flush();
  }

}
