<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

/**
	\class LCResetHandler
	\brief Manages the create and edit action for the LCReset entities
*/
class LCResetHandler {

  protected $form; /**< form submitted */
  protected $request; /**< http request object */
  protected $em; /**< entity manager */
  protected $lcToken; /**< LCToken object */
  protected $factory; /**< EncoderFactory object */

  /**
	\fn constructor
	@param Form $form
	@param Request $request
	@param LCToken $lcToken
	@param EncoderFactoryInterface $factory
  */
  public function __construct(Form $form, Request $request, EntityManager $em,
                                            \EIP\HRBundle\Entity\LCToken $lcToken,
                                            \Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface $factory)
{
    $this->form = $form;
    $this->request = $request;
    $this->em = $em;
    $this->lcToken = $lcToken;
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
            $this->onSuccess();
            return true;
        }
    }
    return false;
  }

  /**
	\fn onSuccess()
	\brief Set the new password for the user
  */
  public function onSuccess() {
    $lcReset = $this->form->getData();
    if ($lcReset->getPassword() == $lcReset->getConfirmPassword())
    {
        $user = $this->lcToken->getUser();
        $newPassword = $this->factory->getEncoder($user)
                                        ->encodePassword($lcReset->getPassword(), $user->getSalt());
        $user->setPassword($newPassword);
        $this->em->remove($this->lcToken);
        $this->em->flush();
    }
    else
    {
        throw new \HRUserException();
    }
  }

}
