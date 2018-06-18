<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

// No direct access.
use Joomla\CMS\Access\Access;
use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/**
 * PWT ACL Plugin
 *
 * @since   3.0
 */
class PlgSystemPwtacl extends JPlugin
{
	/**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 * @since  3.0
	 */
	protected $app;

	/**
	 * @var    String  base update url, to decide whether to process the event or not
	 *
	 * @since  1.0.0
	 */
	private $baseUrl = 'https://extensions.perfectwebteam.com/pwt-acl';

	/**
	 * @var    String  Extension identifier, to retrieve its params
	 *
	 * @since  1.0.0
	 */
	private $extension = 'com_pwtacl';

	/**
	 * @var    String  Extension title, to retrieve its params
	 *
	 * @since  1.0.0
	 */
	private $extensionTitle = 'PWT ACL';

	/**
	 * Constructor
	 *
	 * @param   object $subject   The object to observe
	 * @param   array  $config    An optional associative array of configuration settings.
	 *                            Recognized key values include 'name', 'group', 'params', 'language'
	 *                            (this list is not meant to be comprehensive).
	 *
	 * @since   1.0
	 */
	public function __construct(&$subject, array $config = array())
	{
		parent::__construct($subject, $config);
	}

	/**
	 * On After Route trigger
	 *
	 * @return  void
	 * @since   3.0
	 * @throws  Exception
	 */
	public function onAfterRoute()
	{
		$option                 = $this->app->input->getCmd('option');
		$view                   = $this->app->input->getCmd('view');
		$params                 = ComponentHelper::getParams('com_pwtacl');
		$aclCategoryManager     = $params->get('acl_categorymanager', 1);
		$aclContentOnlyEditable = $params->get('acl_onlyallowed', 1);
		$aclModulesOnlyEditable = $params->get('acl_modules_only_editable', 1);
		$aclContactOnlyEditable = $params->get('acl_contacts_only_editable', 1);

		// Not for frontend
		if ($this->app->isClient('site'))
		{
			return;
		}

		// Article Manager overrides
		if ($aclContentOnlyEditable && $option == 'com_content' && (($view == 'articles') || (!$view)))
		{
			$this->overrideJoomlaCoreClass('ContentModelArticles', 'com_content/models/articles.php');
		}

		// Contact Manager overrides
		if ($aclContactOnlyEditable && $option == 'com_contact' && (($view == 'contacts') || (!$view)))
		{
			$this->overrideJoomlaCoreClass('ContactModelContacts', 'com_contact/models/contacts.php');
		}

		// Module Manager overrides
		if ($aclModulesOnlyEditable && $option == 'com_modules' && (($view == 'articles') || (!$view)))
		{
			$this->overrideJoomlaCoreClass('ModulesModelModules', 'com_modules/models/modules.php');
		}

		// Check for ACL support and access permission
		$this->checkAclAccess($option, $aclCategoryManager);
	}

	/**
	 * Method to check for proper ACL setup
	 *
	 * @param   string  $component          Component name
	 * @param   integer $aclCategoryManager Category ACL setting
	 *
	 * @return  boolean
	 * @since   3.0
	 * @throws  Exception
	 */
	public function checkAclAccess($component, $aclCategoryManager)
	{
		// No component to check
		if (!$component)
		{
			return true;
		}

		// Get actions from access.xml
		$actions = Access::getActionsFromFile(
			JPATH_ADMINISTRATOR . '/components/' . $component . '/access.xml',
			"/access/section[@name='component']/"
		);

		// Get actions from config.xml as fallback
		if (empty($actions))
		{
			$actions = Access::getActionsFromFile(
				JPATH_ADMINISTRATOR . '/components/' . $component . '/config.xml',
				"/config/fieldset/field[@section='component']/"
			);
		}

		// If we have actions all is fine
		if ($actions)
		{
			return true;
		}

		// Core components without ACL
		$coreNoAcl = array(
			'com_admin',
			'com_config',
			'com_cpanel',
			'com_login',
			'com_mailto',
			'com_wrapper',
			'com_contenthistory',
			'com_ajax',
			'com_fields'
		);

		// Add Categories Manager if not active
		if (!$aclCategoryManager)
		{
			$coreNoAcl[] = 'com_categories';
		}

		// Core component without ACL, so all is fine
		if (in_array($component, $coreNoAcl))
		{
			return true;
		}

		// In case of no ACL support and no access we are going to block it...
		if (!Factory::getUser()->authorise('core.manage', $component))
		{
			throw new NotAllowed(Text::_('JERROR_ALERTNOAUTHOR'), 403);
		}

		return true;
	}

	/**
	 * onAfterRender trigger
	 *
	 * @return  void
	 * @since   3.0
	 */
	public function onAfterRender()
	{
		// Not for frontend
		if ($this->app->isClient('site'))
		{
			return;
		}

		// Get Category ACL setting
		$aclCategoryManager = ComponentHelper::getParams('com_pwtacl')->get('acl_categorymanager', 1);

		// Remove category links from output in case of no access
		if (($aclCategoryManager) && (!Factory::getUser()->authorise('core.manage', 'com_categories')))
		{
			$buffer = preg_replace("/<a.*?com_categories.*?>(.*?)<\/a>/", "", $this->app->getBody());
			$this->app->setBody($buffer);
		}
	}

	/**
	 * Method to override a Joomla core class
	 *
	 * @param   string $class Class name
	 * @param   string $file  File to override
	 *
	 * @return  void
	 * @since   3.0
	 */
	private function overrideJoomlaCoreClass($class, $file)
	{
		// Check if class exists already
		if (!class_exists($class . 'Core', false))
		{
			$core = str_replace($class, $class . 'Core', file_get_contents(JPATH_ADMINISTRATOR . '/components/' . $file));
			$core = preg_replace('/^\\<\\?php[^A-z]/', '', $core);

			// We need this here...
			eval($core);

			JLoader::register($class, dirname(__FILE__) . '/overrides/' . $file);
			JLoader::load($class);
		}
	}

	/**
	 * Adding required headers for successful extension update
	 *
	 * @param   string $url     url from which package is going to be downloaded
	 * @param   array  $headers headers to be sent along the download request (key => value format)
	 *
	 * @return  boolean true    Always true, regardless of success
	 *
	 * @since   1.0.0
	 *
	 * @throws  Exception
	 */
	public function onInstallerBeforePackageDownload(&$url, &$headers)
	{
		// Are we trying to update our own extensions?
		if (strpos($url, $this->baseUrl) !== 0)
		{
			return true;
		}

		// Load language file
		$jLanguage = Factory::getLanguage();
		$jLanguage->load('com_pwtacl', JPATH_ADMINISTRATOR . '/components/com_pwtacl/', 'en-GB', true, true);
		$jLanguage->load('com_pwtacl', JPATH_ADMINISTRATOR . '/components/com_pwtacl/', null, true, false);

		// Get the Download ID from component params
		$downloadId = ComponentHelper::getComponent($this->extension)->params->get('downloadid', '');

		// Set Download ID first
		if (empty($downloadId))
		{
			Factory::getApplication()->enqueueMessage(
				Text::sprintf('COM_PWTACL_DOWNLOAD_ID_REQUIRED',
					$this->extension,
					$this->extensionTitle
				),
				'error'
			);

			return true;
		}
		// Append the Download ID
		else
		{
			$separator = strpos($url, '?') !== false ? '&' : '?';
			$url       .= $separator . 'key=' . $downloadId;
		}

		// Get the clean domain
		$domain = '';

		if (preg_match('/\w+\..{2,3}(?:\..{2,3})?(?:$|(?=\/))/i', Uri::base(), $matches) === 1)
		{
			$domain = $matches[0];
		}

		// Append domain
		$url .= '&domain=' . $domain;

		return true;
	}
}
