<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// No direct access.
defined('_JEXEC') or die;

/**
 * PWT ACL component helper.
 *
 * @since   3.0
 */
class PwtaclHelper
{
	/**
	 * Sidebar items
	 *
	 * @param   string $vName Active view name
	 *
	 * @return  void
	 * @since   3.0
	 */
	public static function addSubmenu($vName)
	{
		// Control Panel
		JHtmlSidebar::addEntry(
			Text::_('COM_PWTACL_SUBMENU_DASHBOARD'),
			'index.php?option=com_pwtacl&view=dashboard',
			$vName == 'dashboard'
		);

		// Group
		JHtmlSidebar::addEntry(
			Text::_('COM_PWTACL_SUBMENU_GROUP'),
			'index.php?option=com_pwtacl&view=assets&type=group',
			$vName == 'group'
		);

		// User
		JHtmlSidebar::addEntry(
			Text::_('COM_PWTACL_SUBMENU_USER'),
			'index.php?option=com_pwtacl&view=assets&type=user',
			$vName == 'user'
		);

		// Diagnostic
		if (Factory::getUser()->authorise('pwtacl.diagnostics', 'com_pwtacl'))
		{
			JHtmlSidebar::addEntry(
				Text::_('COM_PWTACL_SUBMENU_DIAGNOSTICS'),
				'index.php?option=com_pwtacl&view=diagnostics',
				$vName == 'diagnostics'
			);
		}

		// Wizard
		if (Factory::getUser()->authorise('pwtacl.wizard', 'com_pwtacl'))
		{
			JHtmlSidebar::addEntry(
				Text::_('COM_PWTACL_SUBMENU_WIZARD'),
				'index.php?option=com_pwtacl&view=wizard',
				$vName == 'wizard'
			);
		}
	}

	/**
	 * Load system language files from installed extensions.
	 *
	 * @return  void
	 * @since   3.0
	 */
	public static function getLanguages()
	{
		// Initialise variable
		$lang  = Factory::getLanguage();
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		// Load frontend default language file
		$lang->load('', JPATH_SITE, null, false, false)
		|| $lang->load('', JPATH_SITE, $lang->getDefault(), false, false);

		// Get all active extensions
		$query->select('element AS value')
			->from('#__extensions')
			->where('enabled >= 1')
			->where('type =' . $db->Quote('component'));

		$languages = $db->setQuery($query)->loadObjectList();

		if (count($languages))
		{
			foreach ($languages as &$language)
			{
				// Load system language files for all extensions
				$extension = $language->value;
				$source    = JPATH_ADMINISTRATOR . '/components/' . $extension;
				$lang->load($extension . '.sys', JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
				|| $lang->load($extension . '.sys', $source, $lang->getDefault(), false, false)
				|| $lang->load($extension . '.sys', JPATH_ADMINISTRATOR, null, false, false)
				|| $lang->load($extension . '.sys', $source, null, false, false);

				// Load com_config language file
				if ($language->value == 'com_config')
				{
					$lang->load($extension, JPATH_ADMINISTRATOR, $lang->getDefault(), false, false)
					|| $lang->load($extension, $source, $lang->getDefault(), false, false)
					|| $lang->load($extension, JPATH_ADMINISTRATOR, null, false, false)
					|| $lang->load($extension, $source, null, false, false);
				}
			}
		}

		return;
	}
}
