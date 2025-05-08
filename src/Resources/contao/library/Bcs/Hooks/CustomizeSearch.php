<?php

namespace Bcs\Hooks;

use Contao\Controller;
use Contao\Database;
use Contao\System;

use Isotope\Interfaces\IsotopeProduct;
use Isotope\Isotope;
use Isotope\Model\Product;

class CustomizeSearch
{
    public function customizeSearch($pageIds, $keywords, $queryType, $fuzzy, $module)
    {
        echo "Hey Yo...";
        die();
        
    }
}
