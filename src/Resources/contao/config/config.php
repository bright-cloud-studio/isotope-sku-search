<?php
 
/**
 * @copyright  Bright Cliud Studio
 * @author     Bright Cloud Studio
 * @package    Isotope SKU Search
 * @license    LGPL-3.0+
 * @see	       https://github.com/bright-cloud-studio/isotope-sku-search
 */

/** Hooks */
$GLOBALS['TL_HOOKS']['generatePage'][] 		 = array('Bcs\Hooks\CustomizeSearchListener', 'generatePage');

$GLOBALS['TL_HOOKS']['customizeSearch'][] 		 = array('Bcs\Hooks\CustomizeSearchListener', 'onCustomizeSearch');
