<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2020 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
$open = !$links ? 'target="_blank"' : ''; ?>

<?php if ($items) { ?>
<div id="rsepro-upcoming-module">
	<?php foreach ($items as $block => $events) { ?>
	<ul class="rsepro_upcoming<?php echo $suffix; ?> <?php echo RSEventsproAdapterGrid::row(); ?>">
		<?php foreach ($events as $id) { ?>
		<?php $details = rseventsproHelper::details($id); ?>
		<?php $image = !empty($details['image_s']) ? $details['image_s'] : rseventsproHelper::defaultImage(); ?>
		
		<?php if (isset($details['event']) && !empty($details['event'])) $event = $details['event']; else continue; ?>
		<li class="clearfix <?php echo RSEventsproAdapterGrid::column(12 / $columns); ?>">
			<div class="rsepro-image">
				<img src="<?php echo $image; ?>" alt="<?php echo $event->name; ?>" width="70" />
			</div>
			
			<a <?php echo $open; ?> href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id='.rseventsproHelper::sef($event->id,$event->name),true,$itemid); ?>"><?php echo $event->name; ?></a> <?php if ($event->published == 3) { ?><small class="text-error">(<?php echo JText::_('MOD_RSEVENTSPRO_UPCOMING_CANCELED'); ?>)</small><?php } ?>
			<br />
			<small>(<?php echo $event->allday ? rseventsproHelper::date($event->start,rseventsproHelper::getConfig('global_date'),true) : rseventsproHelper::date($event->start,null,true); ?>)</small>
		</li>
		<?php } ?>
	</ul>
	<?php } ?>
</div>
<div class="clearfix"></div>
<?php } ?>