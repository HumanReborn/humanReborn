<?php

namespace EIP\HRBundle\Form\DataTransformers;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;

/**
	\class StringToTownTransformer
	\brief Transforms a string into a HRTown, the string must match the name of the town
*/
class StringToTownTransformer implements DataTransformerInterface
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
     * Transforms an object (town) to a string .
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($town)
    {
        if (null === $town) {
            return "";
        }

        return $town->getName();
    }

    /**
     * Transforms a string (text) to an object ($town).
     *
     * @param  string $text
     * @return HRTown|null
     * @throws TransformationFailedException if object (town) is not found.
     */
    public function reverseTransform($text)
    {
        if (!$text) {
            return null;
        }

        if (!is_numeric($text))
            throw new \Exception("numeric value expected for 'town'");

        $town = $this->om
            ->getRepository('EIPHRBundle:HRTown')
            ->findOneBy(array('id' => $text));

        if (null === $town) {
            throw new TransformationFailedException(sprintf(
                'l\'la ville id = "%s" n\'existe pas !',
                $text
            ));
        }
        return $town;
    }
}