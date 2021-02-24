<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="ed-convo-messages__item" data-ed-conversations-message>
    <div class="o-media o-media--top ed-convo-messages__item-content">
        <div class="o-media__image ">
            <?php echo $this->html('user.avatar', $message->getCreator(), array('rank' => false, 'size' => 'l')); ?>
        </div>

        <div class="o-media__body">
            <div class="ed-user-name t-mb--xs"><?php echo $message->getCreator()->getName();?></div>
            
            <div class="ed-convo-text t-mb--xs">
                <?php echo $message->getContent();?>
            </div>

            <?php if ($message->hasAttachments()) { ?>
            <div class="ed-convo-attachments">
                Attachment placeholder
            </div>
            <?php } ?>

        </div>
    </div>
    
    <div class="ed-convo-messages__time">
    	<div class="o-meta"><?php echo $message->getElapsedTime();?></div>
    </div>
</div>