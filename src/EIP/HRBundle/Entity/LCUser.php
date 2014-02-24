<?php

namespace EIP\HRBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * \class LCUser
 * \brief Used to validate a form in the 'retrieve credentials' process
 */
class LCUser {
    /**
     * \cond @Assert\Email() \endcond
     * \cond @Assert\NotBlank() \endcond
     */
    private $email; /**< email of the user's lost credential*/


    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

}
