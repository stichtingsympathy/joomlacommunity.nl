<?php
/**
 * @package       RSEvents!Pro
 * @copyright (C) 2015 www.rsjoomla.com
 * @license       GPL, http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// get RSEventsPro Template Helper
$template = JFactory::getApplication()->getTemplate();
include_once JPATH_THEMES . '/' . $template . '/helpers/rsevents.php';

// Instantiate the helper class
$rsEventHelper = new ThisRSEventsProHelper();

$open = !$links ? '\'target\' => \'_blank\'' : '';
$i    = 0;
?>


<div class="panel-body">
    <p>Vergroot je Joomla-kennis door gebruikersgroepen, evenementen en curssusen te bezoeken. Voor de beginnende én gevorderde gebruiker! Of draag zelf bij aan Joomla! tijdens Pizza Bugs & Fun.</p>
</div>

<div class="list-group list-group-flush">
	<?php foreach ($events as $eventid) : ?>
		<?php
		$details   = rseventsproHelper::details($eventid);
		$category  = $rsEventHelper->getCategoryName($eventid);
		$category  = str_replace('Joomla Gebruikersgroep', 'Gebruikersgroep', $category);
		$event     = $details['event'];
		$startdate = rseventsproHelper::date($event->start, 'Y-m-d');
		$now       = JFactory::getDate()->format('Y-m-d');
		?>
		<?php if (isset($details['event']) && !empty($details['event']) && ($startdate >= $now) && ($i < 10)): ?>

            <a class="list-group-item" href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&layout=show&id=' . rseventsproHelper::sef($event->id, $event->name), true, $event->itemid); ?>">
                <div class="date-icon">
                    <span class="date-day"><?php echo rseventsproHelper::date($event->start, 'j', true); ?></span><?php echo rseventsproHelper::date($event->start, 'M', true); ?>
                </div>
                <h4 class="list-group-item-heading">
					<?php echo strip_tags($event->name); ?>
                </h4>
                <p class="list-group-item-text"><?php echo $category; ?></p>
            </a>
			<?php $i++; ?>
		<?php endif; ?>
	<?php endforeach; ?>
</div>