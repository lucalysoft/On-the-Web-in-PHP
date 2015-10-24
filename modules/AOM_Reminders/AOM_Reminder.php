<?php
class AOM_Reminder extends Basic {
    var $name;

    var $new_schema = true;
    var $module_dir = 'AOM_Reminders';
    var $object_name = 'AOM_Reminder';
    var $table_name = 'aom_reminders';
    var $importable = false;
    var $disable_row_level_security = true;

    var $popup;
//    var $popup_sent = false;
//    var $popup_read = false;
    var $email;
    var $email_sent = false;
//    var $email_read = false;
    var $duration;
    var $related_event_module;
    var $related_event_module_id;

    public function __construct() {
        parent::Basic();
    }

    public function bean_implements($interface){
        switch($interface){
            case 'ACL': return true;
        }
        return false;
    }

	// ---- save and load remainders on EditViews
	
    public static function saveRemindersDataJson($eventModule, $eventModuleId, $remindersDataJson) {
        $reminderData = json_decode($remindersDataJson);
        if(!json_last_error()) {
            AOM_Reminder::saveRemindersData($eventModule, $eventModuleId, $reminderData);
        }
        else {
            throw new Exception(json_last_error_msg());
        }
    }

    private static function saveRemindersData($eventModule, $eventModuleId, $remindersData) {
        $savedReminderIds = array();
        foreach($remindersData as $reminderData) {
            $reminderBean = BeanFactory::getBean('AOM_Reminders', $reminderData->id);
            $reminderBean->popup = $reminderData->popup;
            $reminderBean->email = $reminderData->email;
            $reminderBean->duration = $reminderData->duration;
            $reminderBean->related_event_module = $eventModule;
            $reminderBean->related_event_module_id = $eventModuleId;
            $reminderBean->save();
            $savedReminderIds[] = $reminderBean->id;
            $reminderId = $reminderBean->id;
            AOM_Reminder_Invitee::saveRemindersInviteesData($reminderId, $reminderData->invitees);
        }
        $reminders = BeanFactory::getBean('AOM_Reminders')->get_full_list("", "aom_reminders.related_event_module = '$eventModule' AND aom_reminders.related_event_module_id = '$eventModuleId'");
        if($reminders) {
            foreach ($reminders as $reminder) {
                if (!in_array($reminder->id, $savedReminderIds)) {
                    AOM_Reminder_Invitee::deleteRemindersInviteesMultiple($reminder->id);
                    $reminder->mark_deleted($reminder->id);
                    $reminder->save();
                }
            }
        }
    }

    public static function loadRemindersDataJson($eventModule, $eventModuleId) {
        $remindersData = self::loadRemindersData($eventModule, $eventModuleId);
        $remindersDataJson = json_encode($remindersData);
        if(!$remindersDataJson && json_last_error()) {
            throw new Exception(json_last_error_msg());
        }
        return $remindersDataJson;
    }

	private static function loadRemindersData($eventModule, $eventModuleId) {
		$ret = array();
		$reminders = BeanFactory::getBean('AOM_Reminders')->get_full_list("aom_reminders.date_entered", "aom_reminders.related_event_module = '$eventModule' AND aom_reminders.related_event_module_id = '$eventModuleId'");
        if($reminders) {
            foreach ($reminders as $reminder) {
                $ret[] = array(
                    'id' => $reminder->id,
                    'popup' => $reminder->popup,
                    'email' => $reminder->email,
                    'duration' => $reminder->duration,
                    'invitees' => AOM_Reminder_Invitee::loadRemindersInviteesData($reminder->id),
                );
            }
        }
		return $ret;
	}
	
	// ---- sending email reminders
	
	public static function sendEmailReminders(EmailReminder $emailReminder, Administration $admin) {
        if($reminders = self::getUnsentEmailReminders()) {
            foreach($reminders as $reminderId => $reminder) {
				$recipients = self::getEmailReminderInviteesRecipients($reminderId);
				$eventBean = BeanFactory::getBean($reminder->related_event_module, $reminder->related_event_module_id);
				if ( $emailReminder->sendReminders($eventBean, $admin, $recipients) ) {
					$reminder->email_sent = 1;
					$reminder->save();
				}
            }
        }
    }
	
	private static function getEmailReminderInviteesRecipients($reminderId) {
		$emails = array();
		$reminder = BeanFactory::getBean('AOM_Reminders', $reminderId);		
		$eventModule = $reminder->related_event_module;
		$eventModuleId = $reminder->related_event_module_id;		
		$event = BeanFactory::getBean($eventModule, $eventModuleId);
		if(!isset($event->status) || $event->status != 'Held') {
			$invitees = BeanFactory::getBean('AOM_Reminders_Invitees')->get_full_list('', "aom_reminders_invitees.reminder_id = '$reminderId'");
			foreach($invitees as $invitee) {
				$inviteeModule = $invitee->related_invitee_module;
				$inviteeModuleId = $invitee->related_invitee_module_id;
				$personBean = BeanFactory::getBean($inviteeModule, $inviteeModuleId);
				if ( !empty($personBean->email1) ) {
					$arr = array(
						'type' => $inviteeModule,
						'name' => $personBean->full_name,
						'email' => $personBean->email1,
					);
					$emails[] = $arr;
				}
			}
		}		
		return $emails;
	}

    private static function getUnsentEmailReminders() {
        global $db;
		// TODO: The original email remainders check the accept_status field in related users/leads/contacts etc. and filtered these users who not decline this event.
		$reminderBeans = BeanFactory::getBean('AOM_Reminders')->get_full_list('', "aom_reminders.email = 1 AND aom_reminders.email_sent = 0");
		foreach($reminderBeans as $reminderBean) {
			$eventBean = BeanFactory::getBean($reminderBean->related_event_module, $reminderBean->related_event_module_id);
			$dateStart = $eventBean->date_start;
			$time = strtotime($db->fromConvert($dateStart,'datetime'));
			$dateStart = date(TimeDate::DB_DATETIME_FORMAT, $time);
			$remind_ts = $GLOBALS['timedate']->fromDb($db->fromConvert($dateStart,'datetime'))->modify("-{$reminderBean->duration} seconds")->ts;
            $now_ts = $GLOBALS['timedate']->getNow()->ts;
            if ( $now_ts >= $remind_ts ) {
                $reminders[$reminderBean->id] = $reminderBean;
            }
		}
        return $reminders;
    }
	
	// ---- popup and alert reminers
	
	public static function addNotifications(jsAlerts $alert) {
		global $current_user, $timedate, $app_list_strings, $db, $sugar_config, $app_strings;

		if (empty($current_user->id)) {
            return;
		}
		
		//Create separate variable to hold timedate value
        $alertDateTimeNow = $timedate->nowDb();

		// cn: get a boundary limiter
		$dateTimeMax = $timedate->getNow()->modify("+{$app_list_strings['reminder_max_time']} seconds")->asDb();
		$dateTimeNow = $timedate->nowDb();

		$dateTimeNow = $db->convert($db->quoted($dateTimeNow), 'datetime');
		$dateTimeMax = $db->convert($db->quoted($dateTimeMax), 'datetime');
		
		// Original jsAlert used to a meeting integration.
		
		///////////////////////////////////////////////////////////////////////
		////	MEETING INTEGRATION
		$meetingIntegration = null;
		if(isset($sugar_config['meeting_integration']) && !empty($sugar_config['meeting_integration'])) {
			if(!class_exists($sugar_config['meeting_integration'])) {
				require_once("modules/{$sugar_config['meeting_integration']}/{$sugar_config['meeting_integration']}.php");
			}
			$meetingIntegration = new $sugar_config['meeting_integration']();
		}
		////	END MEETING INTEGRATION
		///////////////////////////////////////////////////////////////////////
		
		$popupReminders = BeanFactory::getBean('AOM_Reminders')->get_full_list('', "aom_reminders.popup = 1");
		
		if($popupReminders) {
			foreach($popupReminders as $popupReminder) {
				$relatedEvent = BeanFactory::getBean($popupReminder->related_event_module, $popupReminder->related_event_module_id);
				if(
					(!isset($relatedEvent->status) || $relatedEvent->status == 'Planed') && 
					(!isset($relatedEvent->date_start) || ($relatedEvent->date_start >= $dateTimeNow && $relatedEvent->date_start <= $dateTimeMax) )
				) {
					// TODO: The original popup/alert remainders check the accept_status field in related users/leads/contacts etc. and filtered these users who not decline this event.
					$iniviees = BeanFactory::getBean('AOM_Reminders_Invitees')->get_full_list('', "aom_reminders_invitees.reminder_id = '{$popupReminder->id}' AND aom_reminders_invitees.related_invitee_module_id = '{$current_user->id}'");
					if($invitees) {
						foreach($invitees as $invitee) {
							// need to concatenate since GMT times can bridge two local days
							$timeStart = strtotime($db->fromConvert(isset($relatedEvent->date_start) ? $relatedEvent->date_start : date(TimeDate::DB_DATETIME_FORMAT), 'datetime'));
							$timeRemind = $popupReminders->duration;
							$timeStart -= $timeRemind;

							$url = 'index.php?action=DetailView&module=' . $popupReminder->related_event_module . '&record=' . $popupReminder->related_event_module_id;
							$instructions = $app_strings['MSG_JS_ALERT_MTG_REMINDER_MEETING_MSG'];

							if($popupReminder->related_event_module == 'Meetings') {
								///////////////////////////////////////////////////////////////////
								////	MEETING INTEGRATION
								if(!empty($meetingIntegration) && $meetingIntegration->isIntegratedMeeting($popupReminder->related_event_module_id)) {
									$url = $meetingIntegration->miUrlGetJsAlert($this->toArray($popupReminder));
									$instructions = $meetingIntegration->miGetJsAlertInstructions();
								}
								////	END MEETING INTEGRATION
								///////////////////////////////////////////////////////////////////								
							}
							
							// TODO: language file!!
							$meetingName = from_html(isset($relatedEvent->name) ? $relatedEvent->name : $app_strings['MSG_JS_ALERT_MTG_REMINDER_NO_EVENT_NAME']);
							$desc1 = from_html(isset($relatedEvent->description) ? $relatedEvent->description : $app_strings['MSG_JS_ALERT_MTG_REMINDER_NO_DESCRIPTION']);
							$location = from_html(isset($relatedEvent->location) ? $relatedEvent->location : $app_strings['MSG_JS_ALERT_MTG_REMINDER_NO_LOCATION']);
							
							$relatedToMeeting = $alert->getRelatedName($popupReminder->related_event_module, $popupReminder->related_event_module_id);
							
							$description = empty($desc1) ? '' : $app_strings['MSG_JS_ALERT_MTG_REMINDER_AGENDA'].$desc1."\n";
							$description = $description  ."\n" .$app_strings['MSG_JS_ALERT_MTG_REMINDER_STATUS'] . (isset($relatedEvent->status) ? $relatedEvent->status : '') ."\n". $app_strings['MSG_JS_ALERT_MTG_REMINDER_RELATED_TO']. $relatedToMeeting;
							
							// standard functionality
							$alert->addAlert($app_strings['MSG_JS_ALERT_MTG_REMINDER_MEETING'], $meetingName,
								$app_strings['MSG_JS_ALERT_MTG_REMINDER_TIME'].$timedate->to_display_date_time($db->fromConvert(  (isset($relatedEvent->date_start) ? $relatedEvent->date_start : $app_strings['MSG_JS_ALERT_MTG_REMINDER_NO_START_DATE'])  , 'datetime')),
								$app_strings['MSG_JS_ALERT_MTG_REMINDER_LOC'].$location.
								$description.
								$instructions,
								$timeStart - strtotime($alertDateTimeNow),
								$url
							);
						}
					}
				}
			}
		}
	}
	
}
?>