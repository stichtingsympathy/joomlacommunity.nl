<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

class rseventsproEmails
{	
	/*
	*	Replace placeholders
	*/
	
	public static function placeholders($text, $ide, $name, $optionals = null, $ids = null) {
		$placeholders	= rseventsproEmails::getPlaceholders($ide, $name);
		$search			= $placeholders['search'];
		$replace		= $placeholders['replace'];
		$optionalsPlace = array('{TicketInfo}', '{TicketsTotal}', '{Discount}', '{Tax}', '{LateFee}', '{EarlyDiscount}', '{Gateway}', '{IP}', '{Coupon}');
		
		if (!is_null($ids)) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true)->select($db->qn('hash'))->from($db->qn('#__rseventspro_users'))->where($db->qn('id').' = '.$db->q($ids));
			$db->setQuery($query);
			if ($hash = $db->loadResult()) {
				$unsubscribe = JURI::getInstance()->toString(array('scheme','host')).rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.unsubscribe&hash='.$hash.'-'.$ide);
				
				if (JFactory::getApplication()->isClient('administrator')) {
					$unsubscribe = str_replace('/administrator','',$unsubscribe);
				}
				
				array_push($search, '{UnsubscribeURL}');
				array_push($replace, $unsubscribe);
			}
		}
		
		if (is_array($text)) {
			foreach($text as $name => $value) {
				$text[$name] = str_replace($search,$replace,$value);
			}
			
			if (!is_null($optionals) && is_array($optionals)) {
				$text['body'] = str_replace($optionalsPlace, $optionals, $text['body']);
			}
		} else {
			$text = str_replace($search,$replace,$text);
			
			if (!is_null($optionals) && is_array($optionals)) {
				$text = str_replace($optionalsPlace, $optionals, $text);
			}
		}
		
		return $text;
	}
	
	/*
	*	Get available placeholders
	*/
	
	public static function getPlaceholders($id, $name) {
		static $cache = array();
		$hash = md5($id.$name);
		
		if (!isset($cache[$hash])) {
			// Get the site root
			$u		= JURI::getInstance();	
			$root	= $u->toString(array('scheme','host'));
			
			// Load language
			JFactory::getLanguage()->load('com_rseventspro');
			
			$details	= rseventsproHelper::details($id);
			$event		= $details['event'];
			$categories	= $details['categories'];
			$tags		= $details['tags'];
			
			// The event link
			$eventlink = $root.rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),false,rseventsproHelper::itemid($event->id));
			
			// The location link
			$locationlink = $root.rseventsproHelper::route('index.php?option=com_rseventspro&layout=location&id='.rseventsproHelper::sef($event->locationid,$event->location));
			
			if (JFactory::getApplication()->isClient('administrator')) {
				$eventlink = str_replace('/administrator','',$eventlink);
				$locationlink = str_replace('/administrator','',$locationlink);
			}

			// Event times
			$startdate	= $event->allday ? rseventsproHelper::showdate($event->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::showdate($event->start,null,true);
			$sdate		= rseventsproHelper::showdate($event->start,rseventsproHelper::getConfig('global_date'));
			$sdatetime	= $event->allday ? '' : rseventsproHelper::showdate($event->start,rseventsproHelper::getConfig('global_time'));
			$enddate	= $event->allday ? '' : rseventsproHelper::showdate($event->end,null,true);
			$edate		= $event->allday ? '' : rseventsproHelper::showdate($event->end,rseventsproHelper::getConfig('global_date'));
			$edatetime	= $event->allday ? '' : rseventsproHelper::showdate($event->end,rseventsproHelper::getConfig('global_time'));
			
			$owner		= JFactory::getUser($event->owner);
			$message	= JFactory::getApplication()->input->getHtml('message');
			$timezone	= rseventsproHelper::getTimezone();
			
			$search = array('{EventName}','{EventLink}','{EventDescription}','{EventStartDate}','{EventStartDateOnly}','{EventStartTime}','{EventEndDate}','{EventEndDateOnly}','{EventEndTime}','{Owner}','{OwnerUsername}','{OwnerName}','{OwnerEmail}','{EventURL}','{EventPhone}','{EventEmail}','{LocationName}','{LocationLink}','{LocationDescription}','{LocationURL}','{LocationAddress}','{EventCategories}','{EventTags}','{EventIconSmall}','{EventIconBig}', '{Message}', '{message}', '{User}', '{user}', '{timezone}', '{EventSmallDescription}');
			$replace = array($event->name, $eventlink, $event->description, $startdate, $sdate, $sdatetime, $enddate, $edate, $edatetime, $event->ownername, $owner->get('username'), $owner->get('name'), $owner->get('email'), $event->URL, $event->phone, $event->email, $event->location, $locationlink, $event->ldescription, $event->locationlink, $event->address, $categories, $tags, $details['image_s'], $details['image_b'], $message, $message, $name, $name, $timezone, $event->small_description);
			
			$cache[$hash] = array('search' => $search, 'replace' => $replace);
		}
		
		return $cache[$hash];
	}
	
	
	/*
	*	Invite e-mail
	*/
	
	public static function invite($from, $fromName, $to, $ide, $lang = 'en-GB') {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('invite', null, null, $lang);
		
		if (empty($email) || !$email->enable)
			return false;
		
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text				= rseventsproEmails::placeholders($replacer, $ide, $to);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		$text['body']		= str_replace(array('{from}','{fromname}'),array($from, $fromName), $text['body']);
		
		try {
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($from , $fromName , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {}
		
		return true;
	}
	
	
	/*
	*	Registration e-mail
	*/
	
	public static function registration($to, $ide, $name, $optionals, $ids = null) {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('registration', $to, $ide);
		
		if (empty($email) || !$email->enable)
			return false;
			
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		$paymentURL = '';
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		if ($ids) {
			$db = JFactory::getDbo();
			
			$query = $db->getQuery(true)->select($db->qn('URL'))
					->from($db->qn('#__rseventspro_users'))
					->where($db->qn('id').' = '.$db->q($ids));
			$db->setQuery($query);
			if ($url = $db->loadResult()) {
				$paymentURL = base64_decode($url);
				$root = JUri::getInstance()->toString(array('scheme','host'));
				if (strpos($paymentURL,$root) === false) {
					$paymentURL = $root.$paymentURL;
				}
			}
		}
		
		JFactory::getApplication()->triggerEvent('onrseproRegistrationEmail', array(array('ids' => $ids, 'ide' => $ide, 'data' => &$replacer)));
		
		$text				= rseventsproEmails::placeholders($replacer, $ide, $name, $optionals, $ids);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		$text['body']		= str_replace('{PaymentURL}',$paymentURL,$text['body']);
		
		try {
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {}
		
		return true;
	}
	
	
	/*
	*	Activation e-mail
	*/
	
	public static function activation($to, $ide, $name, $optionals, $ids = null) {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('activation', $to, $ide);
		
		if (empty($email) || !$email->enable)
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		JFactory::getApplication()->triggerEvent('onrseproActivationEmail', array(array('ids' => $ids, 'ide' => $ide, 'data' => &$replacer)));
		
		$text		 		= rseventsproEmails::placeholders($replacer, $ide, $name, $optionals, $ids);
		$text['cc']	 		= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']	 	= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		$attachments 		= rseventsproEmails::pdfAttachement($to,$ide,$name,$optionals,$ids);
		
		try {
			$mailer	= JFactory::getMailer();
			if ($mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , $attachments , $text['replyto'], $text['replyname'])) {
				JFactory::getApplication()->triggerEvent('onrsepro_activationEmailCleanup',array(array('id'=>&$ide)));
			}
		} catch(Exception $e) {}
		
		return true;
	}
	
	
	/*
	*	Unsubscribe e-mail
	*/
	
	public static function unsubscribe($to, $ide, $name, $lang = 'en-GB', $ids = null) {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('unsubscribe', null, null, $lang);
		
		if (empty($email) || !$email->enable)
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		JFactory::getApplication()->triggerEvent('onrseproUnsubscribeEmail', array(array('ids' => $ids, 'ide' => $ide, 'data' => &$replacer)));
		
		$optionals			= rseventsproEmails::createOptionals($ids);
		$text				= rseventsproEmails::placeholders($replacer, $ide, $name, $optionals, $ids);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		try {
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {}
		
		return true;
	}
	
	/*
	*	Denied e-mail
	*/
	
	public static function denied($to, $ide, $name, $ids = null) {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('denied', $to, $ide);
		
		if (empty($email) || !$email->enable)
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		JFactory::getApplication()->triggerEvent('onrseproDeniedEmail', array(array('ids' => $ids, 'ide' => $ide, 'data' => &$replacer)));
		
		$optionals			= rseventsproEmails::createOptionals($ids);
		$text				= rseventsproEmails::placeholders($replacer, $ide, $name, $optionals, $ids);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		try {
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {}
		
		return true;
	}
	
	/*
	*	Reminder e-mail
	*/
	
	public static function reminder($subscriber, $ide, $lang = 'en-GB') {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('reminder', null, null, $lang);
		
		if (empty($email) || !$email->enable)
			return false;
			
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$optionals			= !empty($subscriber->id) ? rseventsproEmails::createOptionals($subscriber->id) : null;
		$text				= rseventsproEmails::placeholders($replacer, $ide, $subscriber->name, $optionals, $subscriber->id);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		try {
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($text['from'] , $text['fromName'] , $subscriber->email , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {}
		
		return true;
	}
	
	
	/*
	*	Post-reminder e-mail
	*/
	
	public static function postreminder($to, $ide, $name, $lang = 'en-GB') {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('preminder', null, null, $lang);
		
		if (empty($email) || !$email->enable)
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text				= rseventsproEmails::placeholders($replacer, $ide, $name);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		try {
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {}
		
		return true;
	}
	
	/*
	*	Guests e-mail
	*/
	
	public static function guests($to, $ide, $name, $subject, $body) {
		$config		= rseventsproHelper::getConfig();
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= 1;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text				= rseventsproEmails::placeholders($replacer,$ide,$name);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		try {
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($text['from'], $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {}
		
		return true;
	}
	
	
	/*
	*	Moderation email
	*/
	
	public function moderation($to, $ide, $lang = 'en-GB') {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('moderation', null, null, $lang);
		
		if (empty($email))
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		
		$text				= rseventsproEmails::placeholders($replacer,$ide,'');
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		$approve			= rseventsproHelper::route(JURI::root().'index.php?option=com_rseventspro&task=activate&key='.md5('event'.$ide));
		$text['body']		= str_replace('{EventApprove}',$approve,$text['body']);
		
		try {
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {}
		
		return true;
	}
	
	public static function tag_moderation($to, $ide, $items, $lang = 'en-GB') {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('tag_moderation', null, null, $lang);
		
		if (empty($email))
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text				= rseventsproEmails::placeholders($replacer,$ide,'');
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		// html
		if ($mode) {
			$approve = '<ul>';
			foreach ($items as $item) {
				$link = rseventsproHelper::route(JURI::root().'index.php?option=com_rseventspro&task=tagactivate&key='.md5('tag'.$item->id));
				$approve .= "\n".'<li><a href="'.$link.'">'.JText::sprintf('RSEPRO_APPROVE_TAG', $item->name).'</a></li>';
			}
			$approve .= '</ul>';
		} else // no html
		{
			$approve = '';
			foreach ($items as $item) {
				$link = rseventsproHelper::route(JURI::root().'index.php?option=com_rseventspro&task=tagactivate&key='.md5('tag'.$item->id));
				$approve .= "\n".JText::sprintf('RSEPRO_APPROVE_TAG', $item->name).': '.$link;
			}
		}
		$text['body'] = str_replace('{TagsApprove}',$approve,$text['body']);
		
		try {
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {}
		
		return true;
	}
	
	/*
	*	Approval e-mail
	*/
	
	public static function approval($to, $ide, $name, $lang = 'en-GB') {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('approval', null, null, $lang);
		
		if (empty($email) || empty($to))
			return false;
		
		if (!$email->enable)
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text				= rseventsproEmails::placeholders($replacer, $ide, $name);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		try {
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {}
		
		return true;
	}
	
	/*
	*	New event subscription notification email
	*/
	
	public static function notify_me($to, $ide, $additional_data = array(), $lang = 'en-GB', $optionals = null, $ids = null) {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('notify_me', null, null, $lang);
		
		if (empty($email))
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		JFactory::getApplication()->triggerEvent('onrseproNotifyEmail', array(array('ids' => $ids, 'ide' => $ide, 'data' => &$replacer)));
		
		$text				= rseventsproEmails::placeholders($replacer,$ide,'',$optionals, $ids);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		if ($additional_data) {
			$text['body']		= str_replace(array_keys($additional_data), array_values($additional_data), $text['body']);
			$text['subject']	= str_replace(array_keys($additional_data), array_values($additional_data), $text['subject']);
		}
		
		try {
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {}
		
		return true;
	}
	
	/*
	*	New paid subscription notification email
	*/
	
	public static function notify_me_paid($to, $ide, $additional_data = array(), $lang = 'en-GB', $optionals = null, $ids = null) {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('notify_me_paid', null, null, $lang);
		
		if (empty($email))
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		JFactory::getApplication()->triggerEvent('onrseproNotifyPaidEmail', array(array('ids' => $ids, 'ide' => $ide, 'data' => &$replacer)));
		
		$text				= rseventsproEmails::placeholders($replacer,$ide,'',$optionals, $ids);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		if ($additional_data) {
			$text['body']		= str_replace(array_keys($additional_data), array_values($additional_data), $text['body']);
			$text['subject']	= str_replace(array_keys($additional_data), array_values($additional_data), $text['subject']);
		}
		
		try {
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {}
		
		return true;
	}
	
	
	/*
	*	Event owner unsubscribe notification
	*/
	
	public static function notify_me_unsubscribe($to, $ide, $additional_data = array(), $lang = 'en-GB', $ids = null) {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('notify_me_unsubscribe', null, null, $lang);
		
		if (empty($email))
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		JFactory::getApplication()->triggerEvent('onrseproNotifyUnsubscribeEmail', array(array('ids' => $ids, 'data' => &$replacer)));
		
		$text				= rseventsproEmails::placeholders($replacer, $ide, '', null, $ids);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		if ($additional_data) {
			$text['body']		= str_replace(array_keys($additional_data), array_values($additional_data), $text['body']);
			$text['subject']	= str_replace(array_keys($additional_data), array_values($additional_data), $text['subject']);
		}
		
		try {
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {}
		
		return true;
	}
	
	/*
	*	Report email
	*/
	public static function report($to, $ide, $additional_data = array(), $lang = 'en-GB') {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('report', null, null, $lang);
		
		if (empty($email) || empty($to) || !$email->enable)
			return false;
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text				= rseventsproEmails::placeholders($replacer,$ide,'');
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		if ($additional_data) {
			$text['body']		= str_replace(array_keys($additional_data), array_values($additional_data), $text['body']);
			$text['subject']	= str_replace(array_keys($additional_data), array_values($additional_data), $text['subject']);
		}
		
		try {
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {}
		
		return true;
	}
	
	
	/*
	*	Rule email
	*/
	
	public static function rule($ids, $message) {
		$config		= rseventsproHelper::getConfig();
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$total		= 0;
		$paymentURL = '';
		
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		// Get subscription details
		$query  ->clear()
				->select('*')
				->from($db->qn('#__rseventspro_users'))
				->where($db->qn('id').' = '.(int) $ids);
		
		$db->setQuery($query);
		$subscription = $db->loadObject();
		$subscriber =& $subscription;
		
		if ($subscriber->URL) {
			$paymentURL = base64_decode($subscriber->URL);
			$root = JUri::getInstance()->toString(array('scheme','host'));
			if (strpos($paymentURL,$root) === false) {
				$paymentURL = $root.$paymentURL;
			}
		}
		
		// Get tickets
		$tickets = rseventsproHelper::getUserTickets($ids);
		$info	 = '';
		
		if (!empty($tickets)) {
			foreach ($tickets as $ticket) {
				// Calculate the total
				if ($ticket->price > 0) {
					$price = $ticket->price * $ticket->quantity;
					$total += $price;
					$ticketInfo = $ticket->quantity . ' x ' .$ticket->name.' ('.rseventsproHelper::currency($ticket->price).') ';
				} else {
					$ticketInfo = $ticket->quantity . ' x ' .$ticket->name.' ('.JText::_('COM_RSEVENTSPRO_GLOBAL_FREE').') ';
				}
				
				$info .= $ticketInfo.rseventsproHelper::getSeats($ids,$ticket->id).' <br />';
			}
		}
		
		if (!empty($subscription->discount) && !empty($total)) {
			$total = $total - $subscription->discount;
		}
		
		if (!empty($subscription->early_fee) && !empty($total)) {
			$total = $total - $subscription->early_fee;
		}
		
		if (!empty($subscription->late_fee) && !empty($total)) {
			$total = $total + $subscription->late_fee;
		}
		
		if (!empty($subscription->tax) && !empty($total)) {
			$total = $total + $subscription->tax;
		}
		
		$ticketstotal		= rseventsproHelper::currency($total);
		$ticketsdiscount	= !empty($subscription->discount) ? rseventsproHelper::currency($subscription->discount) : '';
		$subscriptionTax	= !empty($subscription->tax) ? rseventsproHelper::currency($subscription->tax) : '';
		$lateFee			= !empty($subscription->late_fee) ? rseventsproHelper::currency($subscription->late_fee) : '';
		$earlyDiscount		= !empty($subscription->early_fee) ? rseventsproHelper::currency($subscription->early_fee) : '';
		$gateway			= rseventsproHelper::getPayment($subscription->gateway);
		$IP					= $subscription->ip;
		$coupon				= !empty($subscription->coupon) ? $subscription->coupon : '';
		$optionals			= array($info, $ticketstotal, $ticketsdiscount, $subscriptionTax, $lateFee, $earlyDiscount, $gateway, $IP, $coupon);
		
		$email = rseventsproEmails::emailrule($message, $subscriber->lang);
		if (!$email) return false;
		
		$mode			= @$email->mode;
		$subject		= @$email->subject;
		$body			= @$email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text				= rseventsproEmails::placeholders($replacer, $subscriber->ide, $subscriber->name, $optionals);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		$text['body']		= str_replace('{Status}',rseventsproHelper::getStatuses($subscriber->state),$text['body']);
		$text['body']		= str_replace('{PaymentURL}',$paymentURL,$text['body']);
		
		try {
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($text['from'] , $text['fromName'] , $subscriber->email , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {}
		
		return true;
	}
	
	/*
	*	Attach the pdf to the activation email
	*/
	public static function pdfAttachement($to, $ide, $name, $optionals, $ids) {
		$attachments = null;
		
		if (rseventsproHelper::pdf()) {
			$app	= JFactory::getApplication();
			$db		= JFactory::getDBO();
			$query	= $db->getQuery(true);
			$now	= rseventsproHelper::showdate('now');
			
			$query->clear()
				->select($db->qn('t').'.*')->select($db->qn('ut.quantity'))
				->from($db->qn('#__rseventspro_tickets','t'))
				->join('LEFT',$db->qn('#__rseventspro_user_tickets','ut').' ON '.$db->qn('t.id').' = '.$db->qn('ut.idt'))
				->where($db->qn('t.attach').' = '.$db->q(1))
				->where($db->qn('t.ide').' = '.$db->q($ide))
				->where($db->qn('ut.ids').' = '.$db->q($ids));
			$db->setQuery($query);
			if ($tickets = $db->loadObjectList()) {
				foreach ($tickets as $ticket) {
					for ($i = 1; $i <= $ticket->quantity; $i++) {
						$code		= md5($ids.$ticket->id.$i);
						$code		= substr($code,0,4).substr($code,-4);
						$barcodetext= rseventsproHelper::getBarcodeOptions('barcode_prefix', 'RST-').$ids.'-'.$code;
						$barcodetext= in_array(rseventsproHelper::getBarcodeOptions('barcode', 'C39'), array('C39', 'C93')) ? strtoupper($barcodetext) : $barcodetext;
						$layout		= $ticket->layout;
						
						$app->triggerEvent('onrseproTicketPDFLayout',array(array('ids' => $ids, 'ide' => $ide, 'layout' => &$layout)));
						
						$app->triggerEvent('onrsepro_beforeReplacePDFLayout', array(array('layout' => &$layout, 'ids' => $ids, 'idt' => $ticket->id, 'ide' => $ide, 'position' => $i)));
						
						$layout = rseventsproEmails::placeholders($layout, $ide, $name, $optionals);
						$layout = str_replace('{sitepath}', JPATH_SITE, $layout);
						
						if (strpos($layout,'{barcode}') !== FALSE) {
							jimport('joomla.filesystem.file');
							require_once JPATH_SITE.'/components/com_rseventspro/helpers/pdf/barcodes.php';
							
							$barcodeOption = rseventsproHelper::getBarcodeOptions('barcode', 'qrcode');
							$barcodetype = rseventsproHelper::getBarcodeOptions('barcode_type', 'qrcode');
							$barcodetexttype = $barcodetext;
							
							if ($barcodeOption == 'qrcode') {
								if ($barcodetype == 'url') {
									$hash = md5($ids.$ticket->id.$i);
									$hash = substr_replace($hash, '|'.$ids.'|', 6, 0);
									$secret = JFactory::getConfig()->get('secret');
									
									try {
										require_once JPATH_SITE.'/components/com_rseventspro/helpers/crypt.php';
										
										$hash = RseventsproCrypt::encrypt($hash, $secret);
										$hash	= 'cr'.$hash;
										$hash	= str_replace(' ', '_', $hash);
									} catch (Exception $e) {}
									
									$barcodetexttype = JUri::root().'index.php?option=com_rseventspro&task=confirm&hash='.$hash;
								}
							}
							
							$barcodePDF = new TCPDFBarcode($barcodetexttype, $barcodeOption);
							$size = $barcodeOption == 'qrcode' ? '100px' : '300px';
							
							ob_start();
							$barcodePDF->getBarcodePNG();
							$thecode = ob_get_contents();
							ob_end_clean();
							
							$file = JPATH_SITE.'/components/com_rseventspro/assets/barcode/rset-'.md5($barcodetext).'.png';
							$upload = JFile::write($file,$thecode);
							$barcodeHTML = $upload ? '<img src="'.$file.'" alt="" width="'.$size.'" />' : '';
							
							$layout = str_replace('{barcode}', $barcodeHTML, $layout);
						}
						
						$seat 	= rseventsproEmails::getSeat($ids, $ticket->id, $i);
						$layout = str_replace(array('{useremail}', '{barcodetext}', '{date}', '{seat}'), array($to, $barcodetext, $now, $seat), $layout);
						
						$app->triggerEvent('onrsepro_activationEmail', array(array('id' => &$ide, 'name' => $ticket->name.' '.$i, 'attachment' => &$attachments, 'layout' => &$layout)));
					}
				}
			}
			
			$query->clear()
				->select($db->qn('invoice'))->select($db->qn('invoice_attach'))
				->from($db->qn('#__rseventspro_events'))
				->where($db->qn('id').' = '.$db->q($ide));
			$db->setQuery($query);
			$invoiceDetails = $db->loadObject();
			
			if ($invoiceDetails->invoice && $invoiceDetails->invoice_attach) {
				try {
					require_once JPATH_SITE.'/components/com_rseventspro/helpers/invoice.php';
					
					$tmp	 	= JFactory::getConfig()->get('tmp_path');
					$folder		= md5(JFactory::getSession()->getId()).'_activation';
					$invoice 	= RSEventsProInvoice::getInstance($ids);
					$output  	= $invoice->output(false);
					$path	 	= $tmp.'/'.$folder.'/'.$output['title'];
					
					if (JFile::write($path, $output['buffer'])) {
						$attachments[] = $path;
					}
				} catch (Exception $e) {
					$app->enqueueMessage($e->getMessage(),'info');
				}
			}
		}
		
		return $attachments;
	}
	
	/*
	*	Get the subject and message text
	*/
	
	public static function email($type, $to, $ide, $ulang = 'en-GB') {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		if (is_null($to) && is_null($ide)) {
			$userlanguage = $ulang;
		} else {
			// Get user language
			$query->clear()
				->select($db->qn('u.lang'))
				->from($db->qn('#__rseventspro_users','u'))
				->where($db->qn('u.email').' = '.$db->q($to))
				->where($db->qn('u.ide').' = '.(int) $ide);
			
			JFactory::getApplication()->triggerEvent('onrsepro_subscriptionsQuery', array(array('query' => &$query, 'rule' => 'u.ide')));
			
			$db->setQuery($query);
			$userlanguage = $db->loadResult();
			
			// If we don't find the users language, we set the language to english (en-GB)
			if (empty($userlanguage)) {
				$userlanguage = 'en-GB';
			}
		}
		
		$query->clear()
			->select($db->qn('id'))
			->from($db->qn('#__rseventspro_emails'))
			->where($db->qn('lang').' = '.$db->q($userlanguage))
			->where($db->qn('type').' = '.$db->q($type));
		$db->setQuery($query);
		$emailid = (int) $db->loadResult();
		
		if (!$emailid)
			$userlanguage = 'en-GB';
		
		// Get email details
		$query->clear()
			->select($db->qn('subject'))->select($db->qn('message'))
			->select($db->qn('enable'))->select($db->qn('mode'))
			->from($db->qn('#__rseventspro_emails'))
			->where($db->qn('lang').' = '.$db->q($userlanguage))
			->where($db->qn('type').' = '.$db->q($type));
			
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	/*
	*	Get rule emails
	*/
	
	public static function emailrule($mid, $lang = 'en-GB') {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		
		// Get all emails
		$query->clear()
			->select($db->qn('mode'))->select($db->qn('subject'))
			->select($db->qn('message'))->select($db->qn('lang'))
			->from($db->qn('#__rseventspro_emails'))
			->where($db->qn('type').' = '.$db->q('rule'))
			->where('('.$db->qn('id').' = '.(int) $mid.' OR '.$db->qn('parent').' = '.(int) $mid.')');
			
		$db->setQuery($query);
		$emails = $db->loadObjectList();
		
		if (empty($emails)) 
			return false;
		
		// Search for the email that have the selected language
		foreach ($emails as $email) {
			if ($email->lang == $lang) {
				return $email;
			}
		}
		
		// If there is no email with the selected language get the first email
		return $emails[0];
	}
	
	/*
	*	RSVP Going email
	*/
	public static function rsvpgoing($to, $ide) {
		return rseventsproEmails::rsvpemail('rsvpgoing', $to, $ide);
	}
	
	/*
	*	RSVP Interested email
	*/
	public static function rsvpinterested($to, $ide) {
		return rseventsproEmails::rsvpemail('rsvpinterested', $to, $ide);
	}
	
	/*
	*	RSVP Not going email
	*/
	public static function rsvpnotgoing($to, $ide) {
		return rseventsproEmails::rsvpemail('rsvpnotgoing', $to, $ide);
	}
	
	public static function waitinglist($to, $ide, $name, $hash, $claimDate, $lang = 'en-GB') {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('waitinglist', null, null, $lang);
		
		if (empty($email))
			return false;
			
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$claimURL	= JUri::getInstance()->toString(array('scheme','host')).JRoute::_('index.php?option=com_rseventspro&task=claim&hash='.$hash, false);
		$claimURL	= str_replace('/administrator', '', $claimURL);
		
		$text				= rseventsproEmails::placeholders($replacer, $ide, $name);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		$text['body']		= str_replace(array('{claim}','{claimdate}'),array($claimURL,$claimDate),$text['body']);
		
		try {
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {}
		
		return true;
	}
	
	public static function waitinglist_email($type, $to, $ide, $name, $lang = 'en-GB') {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email($type, null, null, $lang);
		
		if (empty($email))
			return false;
			
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text				= rseventsproEmails::placeholders($replacer, $ide, $name);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		try {
			$mailer	= JFactory::getMailer();
			$mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {}
		
		return true;
	}
	
	public static function cancel($ide) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$status	= rseventsproHelper::getConfig('cancel_to');
		
		$query->clear()
			->select($db->qn('registration'))->select($db->qn('rsvp'))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$db->q($ide));
		$db->setQuery($query);
		$event = $db->loadObject();
		
		if ($event->registration) {
			$query->clear()
				->select($db->qn('u.name'))->select($db->qn('u.email'))
				->select($db->qn('u.lang'))
				->from($db->qn('#__rseventspro_users','u'))
				->where($db->qn('u.ide').' = '.$db->q($ide))
				->group($db->qn('u.email'));
			
			if ($status) {
				$query->where($db->qn('u.state').' IN ('.$status.')');
			}
			
			JFactory::getApplication()->triggerEvent('onrsepro_subscriptionsQuery', array(array('query' => &$query, 'rule' => 'u.ide')));	
		} else if ($event->rsvp) {
			$query->clear()
				->select($db->qn('u.name'))->select($db->qn('u.email'))
				->select("CAST('en-GB' AS CHAR) AS lang")
				->from($db->qn('#__users','u'))
				->join('LEFT', $db->qn('#__rseventspro_rsvp_users','r').' ON '.$db->qn('u.id').' = '.$db->qn('r.uid'))
				->where($db->qn('r.ide').' = '.$db->q($ide));
			
			if ($status) {
				if ($statuses = explode(',',$status)) {
					foreach ($statuses as $i => $statusID) {
						if ($statusID == 0) $statuses[$i] = $db->q('interested');
						elseif ($statusID == 1) $statuses[$i] = $db->q('going');
						elseif ($statusID == 2) $statuses[$i] = $db->q('notgoing');
					}
					
					$status = implode(',', $statuses);
					$query->where($db->qn('r.rsvp').' IN ('.$status.')');
				}
			}
		} else {
			return false;
		}
		
		$db->setQuery($query);
		if ($subscribers = $db->loadObjectList()) {
			foreach ($subscribers as $subscriber) {
				rseventsproEmails::cancelemail($subscriber->email, $ide, $subscriber->name, $subscriber->lang);
			}
		}
	}
	
	protected static function cancelemail($to, $ide, $name, $lang) {
		$config		= rseventsproHelper::getConfig();
		$email		= rseventsproEmails::email('cancel', null, null, $lang);
		
		if (empty($email) || !$email->enable) {
			return false;
		}
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text				= rseventsproEmails::placeholders($replacer, $ide, $name);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		try {
			$mailer	= JFactory::getMailer();
			return $mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {
			return false;
		}
	}
	
	/*
	*	Get RSVP email
	*/
	
	protected static function rsvpemail($type, $to, $ide) {
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true);
		$check	= str_replace('rsvp', 'rsvp_', $type);
		
		$query->clear()
			->select($db->qn($check))
			->from($db->qn('#__rseventspro_events'))
			->where($db->qn('id').' = '.$db->q($ide));
		$db->setQuery($query);
		if (!$db->loadResult()) {
			return false;
		}
		
		$config		= rseventsproHelper::getConfig();
		$lang		= JFactory::getLanguage()->getTag();
		$email		= rseventsproEmails::email($type, null, null, $lang);
		
		if (empty($email)) return false;
		
		// Get the name of the subscriber
		$query->clear()
			->select($db->qn('name'))
			->from($db->qn('#__users'))
			->where($db->qn('email').' = '.$db->q($to));
		$db->setQuery($query);
		$name = $db->loadResult();
		
		$from		= $config->email_from;
		$fromName	= $config->email_fromname;
		$mode		= $email->mode;
		$replyto	= $config->email_replyto;
		$replyname	= $config->email_replytoname;
		$cc			= $config->email_cc;
		$bcc		= $config->email_bcc;
		$cc			= !empty($cc) ? $cc : null;
		$bcc		= !empty($bcc) ? $bcc : null;
		$subject	= $email->subject;
		$body		= $email->message;
		
		$replacer	= array(
			'from'		=> $from,
			'fromName'	=> $fromName,
			'replyto'	=> $replyto,
			'replyname' => $replyname,
			'cc'		=> $cc,
			'bcc'		=> $bcc,
			'subject'	=> $subject,
			'body'		=> $body
		);
		
		$text				= rseventsproEmails::placeholders($replacer, $ide, $name);
		$text['cc']			= isset($text['cc']) && !empty($text['cc']) ? explode(',',$text['cc']) : null;
		$text['bcc']		= isset($text['bcc']) && !empty($text['bcc']) ? explode(',',$text['bcc']) : null;
		$text['replyto']	= isset($text['replyto']) && !empty($text['replyto']) ? explode(',',$text['replyto']) : null;
		$text['replyname']	= isset($text['replyname']) && !empty($text['replyname']) ? explode(',',$text['replyname']) : null;
		
		try {
			$mailer	= JFactory::getMailer();
			return $mailer->sendMail($text['from'] , $text['fromName'] , $to , $text['subject'] , $text['body'] , $mode , $text['cc'] , $text['bcc'] , null , $text['replyto'], $text['replyname']);
		} catch(Exception $e) {
			return false;
		}
	}
	
	/*
	*	Create optional placeholders
	*/
	
	protected static function createOptionals($id) {
		$db		= JFactory::getDBO();
		$query	= $db->getQuery(true);
		$id		= (int) $id;
		$info	= array();
		$total	= 0;
		
		// Get subscription details
		$query->select('*')
			->from($db->qn('#__rseventspro_users'))
			->where($db->qn('id').' = '.$db->q($id));
		$db->setQuery($query);
		$subscription = $db->loadObject();
		
		if ($subscription->ide) {
			// Get tickets
			$tickets = rseventsproHelper::getUserTickets($id);
			
			if (!empty($tickets)) {
				foreach ($tickets as $ticket) {
					if ($ticket->price > 0) {
						$total += $ticket->price * $ticket->quantity;
						$ticketInfo = $ticket->quantity . ' x ' .$ticket->name.' ('.rseventsproHelper::currency($ticket->price).') ';
					} else {
						$ticketInfo = $ticket->quantity . ' x ' .$ticket->name.' ('.JText::_('COM_RSEVENTSPRO_GLOBAL_FREE').') ';
					}
					
					$info[] = $ticketInfo.rseventsproHelper::getSeats($id,$ticket->id);
				}
			}
			
			if (!empty($subscription->discount) && !empty($total)) {
				$total = $total - $subscription->discount;
			}
			
			if (!empty($subscription->early_fee) && !empty($total)) {
				$total = $total - $subscription->early_fee;
			}
			
			if (!empty($subscription->late_fee) && !empty($total)) {
				$total = $total + $subscription->late_fee;
			}
			
			if (!empty($subscription->tax) && !empty($total)) {
				$total = $total + $subscription->tax;
			}
		} else {
			JFactory::getApplication()->triggerEvent('onrsepro_paymentForm', array(array('id' => $id, 'total' => &$total, 'info' => &$info)));
		}
		
		$ticketstotal		= !empty($total) ? rseventsproHelper::currency($total) : '';
		$ticketsdiscount	= !empty($subscription->discount)	? rseventsproHelper::currency($subscription->discount) : '';
		$subscriptionTax	= !empty($subscription->tax)		? rseventsproHelper::currency($subscription->tax) : '';
		$lateFee			= !empty($subscription->late_fee)	? rseventsproHelper::currency($subscription->late_fee) : '';
		$earlyDiscount		= !empty($subscription->early_fee)	? rseventsproHelper::currency($subscription->early_fee) : '';
		$gateway			= rseventsproHelper::getPayment($subscription->gateway);
		$IP					= $subscription->ip;
		$coupon				= $subscription->coupon;
		
		return array(implode('<br />', $info), $ticketstotal, $ticketsdiscount, $subscriptionTax, $lateFee, $earlyDiscount, $gateway, $IP, $coupon);
	}
	
	protected static function getSeat($ids, $idt, $pos) {
		$db			= JFactory::getDbo();
		$query		= $db->getQuery(true);
		
		$query->clear()
			->select($db->qn('seat'))
			->from($db->qn('#__rseventspro_user_seats'))
			->where($db->qn('idt').' = '.(int) $idt)
			->where($db->qn('ids').' = '.(int) $ids);
		
		$db->setQuery($query);
		if ($seats = $db->loadColumn()) {
			$pos = $pos - 1;
			return isset($seats[$pos]) ? $seats[$pos] : ''; 
		}
		
		return '';
	}
}