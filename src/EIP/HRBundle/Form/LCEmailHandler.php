<?php

namespace EIP\HRBundle\Form;

use Symfony\Component\Form\Form;
use EIP\HRBundle\Entity\LCToken;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use EIP\HRBundle\Utils\HRUserException;

/**
	\class LCEmailHandler
	\brief Manages the create and edit action for the LCEMail entities
*/
class LCEmailHandler {

  protected $form; /**< form submitted */
  protected $request; /**< http request object */
  protected $em; /**< entity manager */
  protected $translator; /**< Translator object */
  protected $mailer; /**< Mailer object */

  /**
	\fn constructor
	@param Form $form
	@param Request $request
	@param EntityManager $em
	@param Translator $translator
	@Param Mailer $mailer
  */
  public function __construct(Form $form, Request $request, EntityManager $em,  $translator, $mailer) {
    $this->form = $form;
    $this->request = $request;
    $this->em = $em;
    $this->translator = $translator;
    $this->mailer = $mailer;
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
	\brief Send a mail the user if it exists or throw an exception
  */
  public function onSuccess() {
         // check if acount exists, else error msg
        $tmpUser = $this->form->getData();
        $res = $this->em->getRepository('EIPHRBundle:HRUser')->findOneBy(array('email' => $tmpUser->getEmail()));
        if (!$res) { // user doesnt exist
            throw new HRUserException();
        }
        else { // user exists
            // creating token valid for one hour
            $token = new LCToken();
            $token->setUser($res);
            $this->em->persist($token);
            $this->em->flush();
            // sending the link via email
            $subject =  $this->translator->trans('credentials.lost.subject');

            $body =  $this->translator->trans('credentials.lost.body', array('{{ link }}' => "http://127.0.0.1:8080/eip/web/app_dev.php/resetPassword?token=".$token->getKey())); // TOCHANGE -- depending dev / prod / local

            $message = \Swift_Message::newInstance()
                                ->setSubject($subject)
                                ->setFrom('noreply@HumanReborn-theGame.com')
                                ->setTo($res->getEmail())
                                ->setBody($body);
           // $this->mailer->send($message); // EXCEPTION ALLO
        }
  }

}
