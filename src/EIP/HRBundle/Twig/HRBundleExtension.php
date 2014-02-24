<?php

namespace EIP\HRBundle\Twig;

/**
	\class HRBundleExtension
	\brief Class used to add functions and filters to twig templates
*/
class HRBundleExtension extends \Twig_Extension
{
	/**
		\fn array getFilters(void)
		@return array twigFilters
	*/
    public function getFilters()
    {
        return array(
        );
    }

	/**
		\fn array getFunctions(void)
		@return array twigFunctions
	*/
    public function getFunctions() {
        return array(
            'remainingTime' => new \Twig_Function_Method($this, 'remainingTime'),
            'remainingTimeFormat' => new \Twig_Function_Method($this, 'remainingTimeFormat'),
            'check_file_exists' => new \Twig_Function_Method($this, 'check_file_exists'),
            'getNotificationClass' => new \Twig_Function_Method($this, 'getNotificationClass'),
            'getNotificationIcon' =>new \Twig_Function_Method($this, 'getNotificationIcon')
        );
    }

    /**
        \fn int remainingTime(int)
        \brief translate a timestamp to hours:minutes:seconds
        @return int
        @param int remainingTime
    */
    public function remainingTime($rtime)
    {
        $time = '';
        $hours = floor($rtime / 3600);
		if ($hours < 10) $time .= '0'.$hours.':';
		else $time .= $hours.':';
        $minutes = floor($rtime / 60) - $hours * 60;
		if ($minutes < 10) $time .= '0'.$minutes.':';
		else $time .= $minutes.':';
        $seconds = $rtime % 60;
		if ($seconds < 10) $time .= '0'.$seconds;
		else $time .= $seconds;
        return $time;
    }

	/**
            \fn string remainingTimeFormat(int)
            \brief translate a timestamp to a string of the following format : '3h20min20sec'
            @return string
            @param int remainingTime
	*/
	public function remainingTimeFormat($rtime)
	{
            $time_format = '';
            $hours = floor($rtime / 3600);
            if ($hours > 0)
                $time_format .= $hours.'h ';
            $minutes = floor($rtime / 60) - $hours * 60;
            if ($hours > 0 || $minutes > 0)
            {
                if ($hours > 0 && $minutes < 10)
                    $time_format .= '0'.$minutes.'min ';
                else
                    $time_format .= $minutes.'min ';
            }
            $seconds = $rtime % 60;
            if ($hours > 0 || $minutes > 0 || $seconds > 0)
            {
                if ($minutes > 0 && $seconds < 10)
                    $time_format .= '0'.$seconds.'s ';
                else
                    $time_format .= $seconds.'s ';
            }
            return $time_format;
	}

	/**
            \fn boolean check_file_exists(string)
            \brief check if a file exists
            @return boolean
            @param string path
	*/
	public function check_file_exists($name)
	{
		if (file_exists($name))
                    return true;
		else
                    return false;
	}

	/**
		\fn string getName(void)
		\brief required by Symfony2
		@return string $classname
	*/
    public function getName()
    {
        return 'hrbundle_extension';
    }

    /**
     * \brief return a css class name depending of the type of the notification
     * @param \EIP\HRBundle\Entity\HRNotification $notif
     */
    public function getNotificationClass(\EIP\HRBundle\Entity\HRNotification $notif)
    {
        if ($notif->getType() == \EIP\HRBundle\Entity\HRNotification::SUCCESS)
            return 'success';
        elseif ($notif->getType() == \EIP\HRBundle\Entity\HRNotification::INFO)
            return 'info';
        else
            return 'error';
    }

    /**
     * \brief return an icon class name depending of the type of the notification
     * @param \EIP\HRBundle\Entity\HRNotification $notif
     */
     public function getNotificationIcon(\EIP\HRBundle\Entity\HRNotification $notif)
    {
        if ($notif->getType() == \EIP\HRBundle\Entity\HRNotification::SUCCESS)
            return 'fa-check';
        elseif ($notif->getType() == \EIP\HRBundle\Entity\HRNotification::INFO)
            return 'fa-exclamation';
        else
            return 'fa-times';
    }


}
