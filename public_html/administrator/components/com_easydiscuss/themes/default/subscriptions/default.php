<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" data-ed-form>
	<div class="post-app-filter-bar">
		<div class="app-filter-bar">
			<?php echo $this->html('table.search', 'search', $search); ?>
		</div>

		<div class="app-filter-bar">
			<div class="app-filter-bar__cell">
				<div class="form-inline">
					<div class="form-group">
						<div class="app-filter-select-group">
							<?php echo $this->html('table.filter', 'filter', $filter, array('site' => 'COM_EASYDISCUSS_SITE_OPTION', 'category' => 'COM_EASYDISCUSS_CATEGORY_OPTION', 'post' => 'COM_EASYDISCUSS_POST_OPTION')); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="app-filter-bar__cell app-filter-bar__cell--last">
				<div class="form-inline">
					<div class="app-filter-select-group">
						<?php echo $this->html('table.limit', $pagination); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="panel-table">
		<table class="app-table app-table-middle table table-striped" data-ed-table>
			<thead>
				<tr>
					<td width="1%">
						<?php echo $this->html('table.checkall'); ?>
					</td>

					<td style="text-align: left;">
						<?php echo JText::_('COM_EASYDISCUSS_SUBSCRIBER_EMAIL'); ?>
					</td>

					<?php if ($filter == 'post') { ?>
					<td class="center">
						<?php echo JHTML::_('grid.sort', 'COM_EASYDISCUSS_DISCUSSION_TITLE', 'bname', $orderDirection, $order); ?>
					</td>
					<?php } ?>

					<?php if ($filter == 'category') { ?>
					<td class="center">
						<?php echo JHTML::_('grid.sort', 'COM_EASYDISCUSS_CATEGORY_TITLE', 'c.title', $orderDirection, $order); ?>
					</td>
					<?php } ?>

					<td width="20%" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_TABLE_COLUMN_DATE'); ?>
					</td>

					<td width="1%" class="center">
						<?php echo JHTML::_('grid.sort', 'COM_EASYDISCUSS_ID', 'a.id', $orderDirection, $order ); ?>
					</td>
				</tr>
			</thead>

			<tbody>
			<?php if ($subscriptions) { ?>
				<?php $i = 0; ?>
				<?php foreach ($subscriptions as $subscription) { ?>
				<tr>
					<td class="center">
						<?php echo $this->html('table.checkbox', $i++, $subscription->id); ?>
					</td>

					<td>
						<a href="index.php?option=com_easydiscuss&view=subscription&layout=form&id=<?php echo $subscription->id;?>"><?php echo $subscription->email;?></a> 
						(<?php echo (empty($subscription->name)) ? $subscription->fullname :  $subscription->name;?>)
					</td>

					<?php if ($filter != 'site') { ?>
					<td class="center">
						<?php echo $subscription->bname;?>						
					</td>
					<?php } ?>

					<td class="center">
						<?php echo $subscription->created; ?>
					</td>

					<td class="center">
						<?php echo $subscription->id;?>
					</td>
				</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td colspan="<?php echo $filter != 'site' ? 6 : 5;?>" class="center">
						<?php echo JText::_('COM_EASYDISCUSS_NO_SUBSCRIPTION_FOUND');?>
					</td>
				</tr>
			<?php } ?>
			</tbody>

			<tfoot>
				<tr>
					<td colspan="<?php echo $filter != 'site' ? 6 : 5;?>">
						<div class="footer-pagination center">
							<?php echo $pagination->getListFooter(); ?>
						</div>
					</td>
				</tr>
			</tfoot>
		</table>
	</div>

	<input type="hidden" name="filter_order" value="<?php echo $order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="" />

	<?php echo $this->html('form.hidden', 'subscription', 'subscription'); ?>
</form>
