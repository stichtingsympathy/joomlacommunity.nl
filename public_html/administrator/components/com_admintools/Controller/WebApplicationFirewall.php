<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Controller;

defined('_JEXEC') || die;

use Akeeba\AdminTools\Admin\Controller\Mixin\CustomACL;
use Akeeba\AdminTools\Admin\Controller\Mixin\PredefinedTaskList;
use FOF30\Container\Container;
use FOF30\Controller\Controller;

class WebApplicationFirewall extends Controller
{
	use PredefinedTaskList, CustomACL;

	public function __construct(Container $container, array $config)
	{
		parent::__construct($container, $config);

		$this->predefinedTaskList = ['default'];
	}
}
