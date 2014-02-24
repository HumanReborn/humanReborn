<?php

namespace EIP\HRBundle\Form\DataTransformers;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

class StringToGameTransformer implements DataTransformerInterface
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
    public function transform($game)
    {
        if (null === $game) {
            return "";
        }

        return $game->getName();
    }

    /**
     * Transforms a string (text) to an object ($game).
     *
     * @param  string $text
     * @return HRGame|null
     * @throws TransformationFailedException if object (game) is not found.
     */
    public function reverseTransform($text)
    {
        if (!$text) {
            return null;
        }

        $game = $this->om
            ->getRepository('EIPHRBundle:HRGame')
            ->findOneBy(array('name' => $text))
        ;

        if (null === $game) {
            throw new TransformationFailedException(sprintf(
                'l\'la partie  "%s" n\'existe pas !',
                $text
            ));
        }
        return $game;
    }
}