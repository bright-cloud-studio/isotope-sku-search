<?php

namespace Bcs\Hooks;

use Contao\Controller;
use Contao\Database;
use Contao\System;

use Isotope\Interfaces\IsotopeProduct;
use Isotope\Isotope;
use Isotope\Model\Product;

class CustomizeSearchListener
{
    public function onCustomizeSearch($arrPages, &$strKeywords, $strQueryType, $blnFuzzy)
    {
        $strKeywords .= "CHANGE MADE";
    }


    public function generatePage(&$objPageModel, $objLayout, &$objPage)
    {

        if($objPageModel->id == 249)
        {
           
        }
    }

    
}
