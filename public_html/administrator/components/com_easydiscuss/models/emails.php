<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(__DIR__ . '/model.php');

class EasyDiscussModelEmails extends EasyDiscussAdminModel
{
	public $total = null;
	public $pagination = null;

	public function __construct()
	{
		parent::__construct();
		
		$limit = $this->getStateFromRequest('limit', $this->app->getCfg('list_limit'), 'int');
		$limitstart = $this->input->get('limitstart', 0, 'int');

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Generates the path to the overriden folder
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function getOverrideFolder($file)
	{
		$path = JPATH_ROOT . '/templates/' . $this->getCurrentTemplate() . '/html/com_easydiscuss/emails/' . ltrim($file, '/');

		return $path;
	}

	/**
	 * Retrieves a list of email templates
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTemplates()
	{
		// Should we be hardcoding this
		$folder = DISCUSS_ROOT . '/themes/wireframe/emails';

		// Get a list of files
		$rows = JFolder::files($folder, '.php', false, true);
		$files = [];

		// Get the current site template
		$currentTemplate = $this->getCurrentTemplate();

		foreach ($rows as $row) {
			
			$fileName = basename($row);

			if ($fileName == 'index.html' || stristr($fileName, '.orig') !== false) {
				continue;
			}

			// Get the file object
			$file = $this->getTemplate($row);
			$files[] = $file;
		}

		return $files;
	}

	/**
	 * Retrieves a list of email templates
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getTemplate($absolutePath, $contents = false)
	{
		$file = new stdClass();
		$file->name = basename($absolutePath);

		$file->desc = str_ireplace('.php', '', $file->name);
		$file->desc = strtoupper(str_ireplace(array('.', '-'), '_', $file->desc));
		$file->desc = JText::_('COM_EASYDISCUSS_EMAILS_' . $file->desc);
		$file->path = $absolutePath;

		// Get the current site template
		$currentTemplate = $this->getCurrentTemplate();

		// Determine if the email template file has already been overriden.
		$overridePath = JPATH_ROOT . '/templates/' . $currentTemplate . '/html/com_easydiscuss/emails/' . $file->name;
		
		$file->relative = str_ireplace(JPATH_ROOT . '/components/com_easydiscuss/themes/wireframe/emails/', '', $absolutePath);

		$file->override = JFile::exists($overridePath);
		$file->overridePath = $overridePath;
		$file->contents = '';

		if ($contents) {
			if ($file->override) {
				$file->contents = file_get_contents($file->overridePath);
			} else {
				$file->contents = file_get_contents($file->path);
			}
		}

		return $file;
	}

	/**
	 * Retrieves the current site template
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function getCurrentTemplate()
	{
		$db = ED::db();

		$query = 'SELECT ' . $db->nameQuote('template') . ' FROM ' . $db->nameQuote('#__template_styles');
		$query .= ' WHERE ' . $db->nameQuote('home') . '=' . $db->Quote(1);
		$query .= ' AND ' . $db->qn('client_id') . '=' . $db->Quote(0);

		$db->setQuery($query);

		$template = $db->loadResult();

		return $template;
	}
}
