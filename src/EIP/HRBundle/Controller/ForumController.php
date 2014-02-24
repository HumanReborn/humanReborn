<?php
namespace EIP\HRBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * \class ForumController
 * \brief Forum controller
 */
class ForumController extends Controller
{

   public function loginAction() {
        $request = $this->getRequest();
        $session = $request->getSession();

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }
        return $this->render('EIPHRBundle:Forum:login.html.twig', array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
   }

   public function topicAction($topicid)
    {
        $topic = $this->getDoctrine()->getRepository('EIPHRBundle:HRForumTopic')->getTopic($topicid);
        if (!$topic->userCanRead($this->getUser(), $this->getDoctrine()->getRepository('EIPHRBundle:HRForumTopic')))
            throw new \Exception("Permission denied");
        return $this->render("EIPHRBundle:Forum:topic.html.twig", array(
            'topic' => $topic,
        ));
    }

    public function sectionAction($sectionid)
    {
        $section = $this->getDoctrine()->getRepository('EIPHRBundle:HRForumSection')->find($sectionid);
        return $this->render('EIPHRBundle:Forum:section.html.twig', array(
            'section' => $section
        ));
    }

    public function homeAction()
    {
        $sections = $this->getDoctrine()->getRepository('EIPHRBundle:HRForumSection')->findAll();
        return $this->render('EIPHRBundle:Forum:home.html.twig', array(
            'sections' => $sections
        ));
    }

    public function postAction()
    {
        $content = $this->getRequest()->request->get('content');
        $topicID = $this->getRequest()->request->get('topicID');
        $user = $this->getUser();
        $topic = $this->getDoctrine()->getRepository('EIPHRBundle:HRForumTopic')->find($topicID);
        if (!$topic->userCanPost($user, $this->getDoctrine()->getRepository('EIPHRBundle:HRClanMembers')))
        {
            throw new \Exception("Permission denied.");
        }
        $post = new \EIP\HRBundle\Entity\HRForumPost();
        $post->setContent($content);
        $post->setTopic($topic);
        $post->setUser($user);
        $topic->getPosts()->add($post);
        $topic->setLastPostOn($post->getPostedOn());
        $em = $this->getDoctrine()->getManager();
        $em->persist($post);
        $em->flush();
        return $this->redirect($this->generateUrl('hr_forum_topic', array('topicid' => $topic->getId())));
    }

    public function addTopicAction()
    {
        $user = $this->getUser();
        $sectionID = $this->getRequest()->request->get('sectionID');
        $content = $this->getRequest()->request->get('content');
        $title = $this->getRequest()->request->get('title');
        $section = $this->getDoctrine()->getRepository('EIPHRBundle:HRForumSection')->find($sectionID);
        $topic = new \EIP\HRBundle\Entity\HRForumTopic();
        $post = new \EIP\HRBundle\Entity\HRForumPost();
        $post->setContent($content);
        $post->setTopic($topic);
        $post->setUser($user);
        $topic->setSticky(false);
        $topic->setTitle($title);
        $topic->setUser($user);
        $section->getTopics()->add($topic);
        $topic->setSection($section);
        $topic->getPosts()->add($post);
        $post->setTopic($topic);
        $em = $this->getDoctrine()->getManager();
        $em->persist($topic);
        $em->persist($post);
        $em->flush();
        return $this->redirect($this->generateUrl('hr_forum_topic', array('topicid' => $topic->getId())));
    }

    public function searchTopicAction()
    {
        $needle = $this->getRequest()->request->get('needle');
        $topics = $this->getDoctrine()->getRepository('EIPHRBundle:HRForumTopic')->searchFor($needle);
        return $this->render('EIPHRBundle:Forum:searchResult.html.twig', array(
            'topics' => $topics,
            'needle' => $needle
        ));
    }

}

?>

