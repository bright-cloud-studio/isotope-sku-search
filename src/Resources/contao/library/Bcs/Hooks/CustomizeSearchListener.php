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
    public function onCustomizeSearch($arrPages, $strKeywords, $strQueryType, $blnFuzzy)
    {
        echo "Hey Yo...";
        die();
        
    }
}
