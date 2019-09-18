<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.2.2
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><h2 class="cell acym__walkthrough__title margin-bottom-2"><?php echo acym_translation('ACYM_WALKTHROUGH_SUCCESS'); ?></h2>

<div class="cell margin-top-2 margin-bottom-2">
	<img src="<?php echo ACYM_IMAGES.'happy_man_1.png'; ?>" alt="happy man" id="acym__walkthrough__success__img">
</div>

<div class="cell margin-top-2 margin-bottom-3">
	<p class="acym__walkthrough__text">
        <?php echo acym_translation('ACYM_WALK_SUCCESS_1'); ?><br />
        <?php echo acym_translation('ACYM_WALK_SUCCESS_2'); ?><br />
	</p>
</div>

<div class="cell margin-top-3">
	<button type="button" class="acy_button_submit button" data-task="saveStepSupport"><?php echo acym_translation('ACYM_FINISH'); ?></button>
</div>

