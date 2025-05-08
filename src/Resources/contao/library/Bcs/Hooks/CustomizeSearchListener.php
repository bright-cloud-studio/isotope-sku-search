<?php

namespace Bcs\Hooks;

use Contao\Controller;
use Contao\Database;
use Contao\Input;
use Contao\System;

use Isotope\Interfaces\IsotopeProduct;
use Isotope\Isotope;
use Isotope\Model\Product;

class CustomizeSearchListener
{
    
    public function generatePage(&$objPageModel, $objLayout, &$objPage)
    {
        // If, when loading a page, there is a keywords value in the URL, we are attempting a search!
        if(strlen(Input::get('keywords'))) {

            // Try and find a product with the keyword as the SKU

                // If we find a product, forward the user to that page
            
        }
        
    }
    
}
