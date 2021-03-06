<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\WhitelistedAddresses;

defined('_JEXEC') || die;

use Akeeba\AdminTools\Admin\Helper\Storage;
use Akeeba\AdminTools\Admin\Model\ControlPanel;
use Akeeba\AdminTools\Admin\View\Mixin\SystemPluginExists;
use FOF40\View\DataView\Html as BaseView;
use Joomla\CMS\Language\Text;


class Html extends BaseView
{
	use SystemPluginExists;

	/**
	 * The component's parameters
	 *
	 * @var  Storage
	 */
	public $componentParams;

	/** @var  string    Order column */
	public $order = 'id';

	/** @var  string Order direction, ASC/DESC */
	public $order_Dir = 'DESC';

	/** @var  array    Sorting order options */
	public $sortFields = [];

	public $filters = [];

	/**
	 * The detected IP address of the visitor
	 *
	 * @var  string
	 */
	protected $myIP = '';

	protected $hash_view = 'admintoolswhitelistedaddresses';

	protected function onBeforeBrowse()
	{
		$this->populateSystemPluginExists();

		parent::onBeforeBrowse();

		// Load the components parameters
		$this->componentParams = Storage::getInstance();

		// ...ordering
		$platform        = $this->container->platform;
		$input           = $this->input;
		$this->order     = $platform->getUserStateFromRequest($this->hash_view . 'filter_order', 'filter_order', $input, 'id');
		$this->order_Dir = $platform->getUserStateFromRequest($this->hash_view . 'filter_order_Dir', 'filter_order_Dir', $input, 'DESC');

		// ...filter state
		$this->filters['ip']          = $platform->getUserStateFromRequest($this->hash_view . 'filter_ip', 'ip', $input);
		$this->filters['description'] = $platform->getUserStateFromRequest($this->hash_view . 'filter_description', 'description', $input);

		// Construct the array of sorting fields
		$this->sortFields = [
			'ip'          => Text::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_IP'),
			'description' => Text::_('COM_ADMINTOOLS_LBL_WHITELISTEDADDRESS_DESCRIPTION'),
		];
	}

	protected function onBeforeEdit()
	{
		$this->populateMyIP();

		parent::onBeforeEdit();
	}

	protected function onBeforeAdd()
	{
		$this->populateMyIP();

		parent::onBeforeAdd();
	}

	private function populateMyIP()
	{
		/** @var ControlPanel $cpanelModel */
		$cpanelModel = $this->container->factory->model('ControlPanel');
		$this->myIP  = $cpanelModel->getVisitorIP();
	}
}
