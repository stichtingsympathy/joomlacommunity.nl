<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );?>

<?php if ($this->showform) { ?>
<form method="post" action="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=sendsubscription',true); ?>" id="sFrom" name="sForm">
	<div class="<?php echo RSEventsproAdapterGrid::card(); ?>" style="text-align:center;">
		<div class="card-body">
			<p><strong><?php echo JText::_('COM_RSEVENTSPRO_USER_SUBSCRIPTIONS_INFO'); ?></strong></p>
			<?php echo RSEventsproAdapterGrid::inputGroup('<input type="text" class="form-control" value="" name="email" />', null, '<button type="submit" class="btn btn-primary">'.JText::_('COM_RSEVENTSPRO_GLOBAL_SEND').'</button>'); ?>
		</div>
	</div>
</form>
<?php } else { ?>
<?php if ($this->params->get('show_page_heading', 1)) { ?>
<?php $title = $this->params->get('page_heading', ''); ?>
<h1><?php echo !empty($title) ? $this->escape($title) : JText::_('COM_RSEVENTSPRO_MY_SUBSCRIPTIONS'); ?></h1>
<?php } ?>

<?php if (!empty($this->subscriptions) || !empty($this->rsvpsubscriptions)) { ?>
<table class="table table-striped table-hover">
	<thead>
		<tr>
			<th width="20%" class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JText::_('COM_RSEVENTSPRO_MY_SUBSCRIPTION_DATE'); ?></th>
			<th width="20%"><?php echo JText::_('COM_RSEVENTSPRO_MY_SUBSCRIPTION_EVENT'); ?></th>
			<th width="15%" class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JText::_('COM_RSEVENTSPRO_MY_SUBSCRIPTION_STATUS'); ?></th>
			<?php if ($this->pdf) { ?><th width="20%" class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo JText::_('COM_RSEVENTSPRO_MY_SUBSCRIPTION_TICKET'); ?></th><?php } ?>
			<th width="10%" class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">&nbsp;</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($this->subscriptions as $i => $subscription) { ?>
		<?php $tooltip = JText::_('COM_RSEVENTSPRO_EVENT_STARTS').' '.rseventsproHelper::showdate($subscription->start,null,true); ?>
		<?php if ($subscription->end != JFactory::getDbo()->getNullDate()) $tooltip .= '<br/>'.JText::_('COM_RSEVENTSPRO_EVENT_ENDS').' '.rseventsproHelper::showdate($subscription->end,null,true); ?>
		<tr class="row<?php echo $i % 2; ?>">
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo rseventsproHelper::showdate($subscription->subscribe_date,null,true); ?></td>
			<td><a class="hasTooltip" title="<?php echo $tooltip; ?>" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($subscription->id,$subscription->name),false,rseventsproHelper::itemid($subscription->id)); ?>"><?php echo $subscription->name; ?></a></td>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
				<span class="subscription_state<?php echo $subscription->state; ?>">
				<?php if ($subscription->state == 1) { ?>
				<i class="fa fa-check"></i>
				<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_COMPLETED'); ?>
				<?php } else if ($subscription->state == 0) { ?>
				<?php if (!empty($subscription->URL)) { ?>
				<a href="<?php echo $subscription->URL; ?>">
				<?php } ?>
				<i class="fa fa-exclamation-triangle"></i>
				<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_INCOMPLETE'); ?>
				<?php if (!empty($subscription->URL)) { ?>
				</a>
				<?php } ?>
				<?php } else if ($subscription->state == 2) { ?>
				<i class="fa fa-minus-circle"></i>
				<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_STATUS_DENIED'); ?>
				<?php } ?>
				</span>
			</td>
			<?php if ($this->pdf) { ?>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
				<?php if ($subscription->tickets) { ?>
				<?php foreach ($subscription->tickets as $ticket) { ?>
					<?php $hasLayout = rseventsproHelper::hasPDFLayout($ticket->layout,$subscription->SubmissionId); ?>
					<?php if (!$hasLayout) continue; ?>
					<?php for($i=1; $i <= $ticket->quantity; $i++) { ?>
						<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=ticket&from=subscriptions&format=raw&id='.$subscription->ids.'&ide='.$ticket->ide.'&tid='.$ticket->id.'&position='.$i.$this->code); ?>">
							<i class="fa fa-file-pdf-o"></i> <?php echo $ticket->name; ?>
						</a> <br />
					<?php } ?>
				<?php } ?>	
			<?php } else { ?>&mdash;<?php } ?>
			</td>
			<?php } ?>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
				<div class="btn-group">
					<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=editsubscriber&id='.rseventsproHelper::sef($subscription->ids,$subscription->iname).'&ide='.rseventsproHelper::sef($subscription->id,$subscription->name).$this->code.'&return='.$this->return,false); ?>" class="<?php echo RSEventsproAdapterGrid::styles(array('btn','btn-small')); ?>">
						<i class="fa fa-pencil"></i>
					</a>
					<?php if ($subscription->state == 1 && rseventsproHelper::hasInvoice($subscription->ids)) { ?>
					<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.invoice&id='.rseventsproHelper::sef($subscription->ids,$subscription->iname).'&ide='.rseventsproHelper::sef($subscription->id,$subscription->name),false); ?>" title="<?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_INVOICE'); ?>" class="<?php echo RSEventsproAdapterGrid::styles(array('btn','btn-small')); ?>">
						<i class="fa fa-file-pdf-o fa-fw"></i>
					</a>
					<?php } ?>
					<?php if (rseventsproHelper::canUnsubscribe($subscription->id, true)) { ?>
					<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.deletesubscriber&id='.$subscription->ids.$this->code, false); ?>" onclick="return confirm('<?php echo JText::_('COM_RSEVENTSPRO_MY_SUBSCRIPTION_DELETE',true); ?>');" class="<?php echo RSEventsproAdapterGrid::styles(array('btn','btn-small')); ?>">
						<i class="fa fa-trash"></i>
					</a>
					<?php } ?>
				</div>
			</td>
		</tr>
		<?php } ?>
		
		<?php foreach ($this->rsvpsubscriptions as $i => $subscription) { ?>
		<?php $tooltip = JText::_('COM_RSEVENTSPRO_EVENT_STARTS').' '.rseventsproHelper::showdate($subscription->start,null,true); ?>
		<?php if ($subscription->end != JFactory::getDbo()->getNullDate()) $tooltip .= '<br/>'.JText::_('COM_RSEVENTSPRO_EVENT_ENDS').' '.rseventsproHelper::showdate($subscription->end,null,true); ?>
		<tr class="row<?php echo $i % 2; ?>">
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo rseventsproHelper::showdate($subscription->date,null,true); ?></td>
			<td><a class="hasTooltip" title="<?php echo $tooltip; ?>" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($subscription->ide,$subscription->name),false,rseventsproHelper::itemid($subscription->id)); ?>"><?php echo $subscription->name; ?></a></td>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>"><?php echo rseventsproHelper::RSVPStatus($subscription->rsvp); ?></td>
			<?php if ($this->pdf) { ?><td></td><?php } ?>
			<td class="<?php echo RSEventsproAdapterGrid::styles(array('center')); ?>">
				<a href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&task=rseventspro.deletesubscriber&from=rsvp&id='.$subscription->id.$this->code, false); ?>" onclick="return confirm('<?php echo JText::_('COM_RSEVENTSPRO_MY_SUBSCRIPTION_DELETE',true); ?>');" class="<?php echo RSEventsproAdapterGrid::styles(array('btn','btn-small')); ?>">
					<i class="fa fa-trash"></i>
				</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php } else { ?>
<h2><?php echo JText::_('COM_RSEVENTSPRO_MY_SUBSCRIPTIONS_NO_SUBSCRIPTIONS'); ?></h2>
<?php } ?>
<?php } ?>