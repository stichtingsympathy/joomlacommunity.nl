<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Akeeba\AdminTools\Admin\Model\CleanTempDirectory;
use FOF30\Container\Container;
use FOF30\Date\Date;

defined('_JEXEC') or die;

class AtsystemFeatureCleantemp extends AtsystemFeatureAbstract
{
	protected $loadOrder = 650;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return ($this->params->get('cleantemp', 0) == 1);
	}

	public function onAfterInitialise()
	{
		$minutes = (int) $this->params->get('cleantemp_freq', 0);

		if ($minutes <= 0)
		{
			return;
		}

		$lastJob = $this->getTimestamp('clean_temp');
		$nextJob = $lastJob + $minutes * 60;

		$now = new Date();

		if ($now->toUnix() >= $nextJob)
		{
			$this->setTimestamp('clean_temp');
			$this->tempDirectoryCleanup();
		}
	}

	/**
	 * Cleans up the temporary director
	 */
	private function tempDirectoryCleanup()
	{
		if (!defined('FOF30_INCLUDED') && !@include_once(JPATH_LIBRARIES . '/fof30/include.php'))
		{
			// FOF 3.0 is not installed
			return;
		}

		$container = Container::getInstance('com_admintools');

		try
		{
			/** @var CleanTempDirectory $model */
			$model = $container->factory->model('CleanTempDirectory')->tmpInstance();

			// This also runs the first batch of deletions
			$model->startScanning();

			// and this runs more deletions until the time is up
			$model->run();
		}
		catch (Exception $e)
		{
			// Avoid any blank page on error
		}
	}
}
