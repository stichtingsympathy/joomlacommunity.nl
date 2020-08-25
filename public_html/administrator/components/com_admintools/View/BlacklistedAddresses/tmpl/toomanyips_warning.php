<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2020 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

/** @var  \Akeeba\AdminTools\Admin\View\BlacklistedAddresses\Html $this */

$db              = $this->getContainer()->db;
$query           = $db->getQuery(true)
                      ->select('COUNT(*)')
                      ->from($db->qn('#__admintools_ipblock'));
$totalBlockedIPs = $db->setQuery($query)->loadResult();

if ($totalBlockedIPs < 50) return;

?>
<div class="alert alert-warning">
	<h3><?php echo Text::_('COM_ADMINTOOLS_BLACKLISTEDADDRESSES_ERR_TOOMANY_TITLE'); ?></h3>
	<p>
		<?php echo Text::sprintf('COM_ADMINTOOLS_BLACKLISTEDADDRESSES_ERR_TOOMANY_BODY', 'https://www.akeeba.com/documentation/admin-tools/waf-ip-blacklist.html#do-not-overdo-it-with-ip-blacklisting'); ?>
	</p>
</div>
