<?php
defined('_JEXEC') or die('Restricted access');
?><div class="cell grid-x align-middle text-center acym__users__display__click acym__content margin-bottom-1">
    <?php echo acym_round_chart('', $data['pourcentageOpen'], 'open', 'cell small-6', acym_translation('ACYM_AVERAGE_OPEN'), ''); ?>
    <?php echo acym_round_chart('', $data['pourcentageClick'], 'click', 'cell small-6', acym_translation('ACYM_AVERAGE_CLICK'), ''); ?>
</div>

