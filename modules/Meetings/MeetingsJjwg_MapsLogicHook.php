<?php

if (!defined('sugarEntry') || !sugarEntry) die('Not A Valid Entry Point');

class MeetingsJjwg_MapsLogicHook
{

    var $jjwg_Maps;

    function __construct()
    {
        $this->jjwg_Maps = get_module_info('jjwg_Maps');
    }

    function updateMeetingGeocodeInfo(&$bean, $event, $arguments)
    {
        // after_save
        if ($this->jjwg_Maps->settings['logic_hooks_enabled']) {
            $this->jjwg_Maps->updateMeetingGeocodeInfo($bean);
        }
    }

}
