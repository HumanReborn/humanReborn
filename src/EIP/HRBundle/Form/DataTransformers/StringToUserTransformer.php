<?php

namespace EIP\HRBundle\Form\DataTransformers;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

/**
	\class StringToUserTransformer
	\brief Transforms a string into a HRUser, the string must match the user's username
	
*/
class StringToUserTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($issue)
    {
        if (null === $issue) {
            return "";
        }

        return $issue->getUsername();
    }

    /**
     * Transforms a string (text) to an object (user).
     *
     * @param  string $text
     * @return HRUser|null
     * @throws TransformationFailedException if object (user) is not found.
     */
    public function reverseTransform($text)
    {
        if (!$text) {
            return null;
        }

        $user = $this->om
            ->getRepository('EIPHRBundle:HRUser')
            ->findOneBy(array('username' => $text))
        ;

        if (null === $user) {
            throw new TransformationFailedException(sprintf(
                'l\'utilisateur  "%s" n\'existe pas !',
                $text
            ));
        }
        return $user;
    }
}