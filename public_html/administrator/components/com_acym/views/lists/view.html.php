<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.5.2
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA SAS - All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<?php

class ListsViewLists extends acymView
{
    public function __construct()
    {
        parent::__construct();

        $this->steps = [
            'settings' => 'ACYM_LIST_SETTINGS',
            'subscribers' => 'ACYM_SUBSCRIBERS',
        ];
    }
}
