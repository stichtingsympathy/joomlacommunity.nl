<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

/**
 * Status view.
 *
 * @package  JDiDEAL
 * @since    4.13.0
 */
class JdidealgatewayViewStatus extends JViewLegacy
{
	/**
	 * The message to show
	 *
	 * @var    string
	 * @since  4.13.0
	 */
	protected $message = '';

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @see     fetch()
	 *
	 * @throws  Exception
	 *
	 * @since   4.13.0
	 */
	public function display($tpl = null)
	{
		/** @var JdidealgatewayModelStatus $model */
		$model         = $this->getModel();
		$this->message = $model->getMessage();

		return parent::display($tpl);
	}
}
