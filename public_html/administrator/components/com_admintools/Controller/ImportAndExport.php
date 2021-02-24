<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Controller;

defined('_JEXEC') || die;

use Akeeba\AdminTools\Admin\Controller\Mixin\CustomACL;
use Akeeba\AdminTools\Admin\Helper\Storage;
use Exception;
use FOF30\Controller\Controller;
use Joomla\CMS\Language\Text;

class ImportAndExport extends Controller
{
	use CustomACL;

	public function export()
	{
		$this->layout = 'export';

		parent::display();
	}

	public function import()
	{
		$this->layout = 'import';

		parent::display();
	}

	public function doexport()
	{
		/** @var \Akeeba\AdminTools\Admin\Model\ImportAndExport $model */
		$model = $this->getModel();
		$data  = $model->exportData();

		if ($data)
		{
			$json = json_encode($data);

			// Clear cache
			while (@ob_end_clean())
			{
			}

			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: public", false);

			// Send MIME headers
			header("Content-Description: File Transfer");
			header('Content-Type: json');
			header("Accept-Ranges: bytes");
			header('Content-Disposition: attachment; filename="admintools_settings.json"');
			header('Content-Transfer-Encoding: text');
			header('Connection: close');
			header('Content-Length: ' . strlen($json));

			echo $json;

			$this->container->platform->closeApplication();
		}
		else
		{
			$this->setRedirect('index.php?option=com_admintools&view=ImportAndExport&task=export', Text::_('COM_ADMINTOOLS_IMPORTANDEXPORT_SELECT_DATA_WARN'), 'warning');
		}
	}

	public function doimport()
	{
		$params = Storage::getInstance();
		$params->setValue('quickstart', 1, true);

		/** @var \Akeeba\AdminTools\Admin\Model\ImportAndExport $model */
		$model = $this->getModel();

		try
		{
			$model->importDataFromRequest();

			$type = null;
			$msg  = Text::_('COM_ADMINTOOLS_IMPORTANDEXPORT_IMPORT_OK');
		}
		catch (Exception $e)
		{
			$type = 'error';
			$msg  = $e->getMessage();
		}

		$this->setRedirect('index.php?option=com_admintools&view=ImportAndExport&task=import', $msg, $type);
	}
}
