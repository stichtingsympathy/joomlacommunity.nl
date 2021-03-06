<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="container-fluid form-horizontal">
	<div class="control-group">
		<div class="control-label">
			<label for="rsepro-new-category"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_CATEGORY_NAME'); ?></label>
		</div>
		<div class="controls">
			<input type="text" id="rsepro-new-category" class="form-control" name="category" placeholder="<?php echo JText::_('COM_RSEVENTSPRO_EVENT_ENTER_CATEGORY_NAME'); ?>" />
		</div>
	</div>
	<div class="control-group">
		<div class="control-label">
			<label for="category-parent"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_CHOOSE_PARENT'); ?></label>
		</div>
		<div class="controls">
			<select id="category-parent" name="parent" class="custom-select">
				<?php echo JHtml::_('select.options', JHtml::_('category.categories','com_rseventspro')); ?>
			</select>
		</div>
	</div>
	<?php if ($this->config->modaltype == 2) { ?>
	<div class="control-group">
		<div class="control-label"><label>&nbsp;</label></div>
		<div class="controls">
			<button class="btn btn-primary rsepro-event-add-category"><?php echo JText::_('COM_RSEVENTSPRO_EVENT_ADD_CATEGORY_ADD'); ?></button>
		</div>
	</div>
	<?php } ?>
</div>