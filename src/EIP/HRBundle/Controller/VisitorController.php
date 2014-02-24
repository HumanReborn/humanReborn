<?php

namespace EIP\HRBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use EIP\HRBundle\Entity\HRUser;
use EIP\HRBundle\Entity\LCUser;
use EIP\HRBundle\Form\LCEmailHandler;
use EIP\HRBundle\Form\LCResetHandler;
use EIP\HRBundle\Form\HRUserType;
use EIP\HRBundle\Form\HRUserHandler;
use EIP\HRBundle\Utils\HRUserException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * \class VisitorController
 * \brief Visitor controller
 */
class VisitorController extends Controller
{
    /**
     * homePageAction
     * \brief display of the home page
	 *
     * @return view
     */
    public function homePageAction() {
        $request = $this->getRequest();
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        return $this->render('EIPHRBundle:Visitor:homePage.html.twig', array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
    }

    /**
     * registerAction
     * \brief display of the register page
	 *
     * @return view
     */
    public function registerAction() {
        if ($this->getRequest()->getMethod() == 'POST' && $this->container->getParameter('register') === false) {
            return  $this->redirect($this->generateUrl('hr_homepage'));
        }

        $user = new HRUser();
        $form = $this->createForm(new HRUserType, $user);
        $handler = new HRUserHandler($form, $this->getRequest(),
                                              $this->getDoctrine()->getManager(),
                                              $this->get('security.encoder_factory'));

        if ($handler->process()) {
            $token = new UsernamePasswordToken($user, null, 'main');
            $this->get('security.context')->setToken($token);
            return $this->redirect($this->generateUrl('hr_games'));
        }

        return $this->render('EIPHRBundle:Visitor:register.html.twig',
            array('form' => $form->createView())
         );
    }

    /**
     * aboutAction
     * \brief display of the information page
	 *
     * @return view
     */
    public function aboutAction() {
        return $this->render('EIPHRBundle:Visitor:about.html.twig');
    }

    /**
     * contactAction
     * \brief display of the contact page
	 *
     * @return view
     */
    public function contactAction() {
        return $this->render('EIPHRBundle:Visitor:contact.html.twig');
    }

    /**
     * faqAction
     * \brief display of the frequently asked questions
	 *
     * @return view
     */
    public function faqAction() {
        return $this->render('EIPHRBundle:Visitor:faq.html.twig');
    }

    /**
     * howToPlayAction
     * \brief display of the guide page
	 *
     * @return view
     */
    public function howToPlayAction() {
        return $this->render('EIPHRBundle:Visitor:howToPlay.html.twig');
    }

    /**
     * lostCredentialsAction
     * \brief display of the lost credentials page
	 *
     * @return view
     */
    public function lostCredentialsAction() {
        $sent = false;
        $user = new LCUser();
        $error  = false;
        $form = $this->createFormBuilder($user)
                ->add('email', 'email')
                ->getForm();
        $handler =  new LCEmailHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(),
                                                         $this->get('translator'), $this->get('mailer'));
        try
        {
            if ($handler->process())
                $sent = true;
        }
        catch (HRUserException $ex)
        {
            $error = "user.does.not.exist";
        }


        return $this->render('EIPHRBundle:Visitor:lostCredentials.html.twig',
                array(
                    'error' => $error,
                    'sent' => $sent,
                    'form' => $form->createView(),
                ));
    }

    /**
     * resetPasswordAction
     * \brief display of the reset password page
	 *
     * @return view
     */
    public function resetPasswordAction() {
        // get the LCToken key
        $token = ($this->getRequest()->getMethod() == 'GET') ? $this->getRequest()->query->get('token') : $this->getRequest()->request->get('token');
        // check if there is a valid token for the selected email
        $lcToken = $this->getDoctrine()->getRepository('EIPHRBundle:LCToken')->findValidToken($token); //findOneBy(array('key' => $token));
        if (!$lcToken)
            throw new \Exception('invalid token');
        $reset = new \EIP\HRBundle\Entity\LCReset();
        $form = $this->createFormBuilder($reset)
                            ->add('password', 'password')
                            ->add('confirmPassword', 'password')
                            ->getForm();
        $handler = new LCResetHandler($form, $this->getRequest(), $this->getDoctrine()->getManager(),
                                                       $lcToken, $this->get('security.encoder_factory'));
        try
        {
            if ($handler->process())
            {
                return $this->render('EIPHRBundle:Visitor:passwordChanged.html.twig');
            }
        }
        catch (\HRUserException $ex)
        {
            $errorMsg = $this->translator->trans('passwords.dont.match');
            $form->addError(new \Symfony\Component\Form\FormError($errorMsg));
        }

        return $this->render('EIPHRBundle:Visitor:resetPassword.html.twig',
                array(
                    'token' => $token,
                    'form' => $form->createView(),
                ));
    }

}

