<?php


namespace EIP\HRBundle\Entity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * \class LCReset
 * \brief This class is used in forms when a user whishes to reset its password
 */
class LCReset {
    /**
     * \cond @Assert\NotBlank \endcond
     * \cond @Assert\Length(min="4", max="128") \endcond
     */
    private $password; /**< string of the lost credentials*/

    /**
     * \cond @Assert\NotBlank \endcond
     * \cond @Assert\Length(min="4", max="128") \endcond
     */
    private $confirmPassword; /**< string of the lost credentials*/

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     * Get confirmPassword
     *
     * @return string
     */
    public function getConfirmPassword() {
        return $this->confirmPassword;
    }

    /**
     * Set confirmPassword
     *
     * @param string $confirmPassword
     */
    public function setConfirmPassword($confirmPassword) {
        $this->confirmPassword = $confirmPassword;
    }

}
