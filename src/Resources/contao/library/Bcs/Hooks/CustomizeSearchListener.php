<?php

namespace Bcs\Hooks;

use Contao\Controller;
use Contao\Database;
use Contao\Date;
use Contao\FrontendUser;
use Contao\Input;
use Contao\PageModel;
use Contao\System;

use Isotope\Frontend;
use Isotope\Interfaces\IsotopeProduct;
use Isotope\Isotope;
use Isotope\Model\Product;

class CustomizeSearchListener
{
    
    public function generatePage(&$objPageModel, $objLayout, &$objPage)
    {
        // Try and capture a $_GET['keywords'] value from our current URL
        $keywords = Input::get('keywords');
        
        // If $keywords has any length, we successfully got our value
        if(strlen($keywords)) {

            // Try and find a product where $keyword is that product's SKU
            $product = Product::findOneBy(['tl_iso_product.sku=?'],[$keywords]);
            // If we found a product
            if($product) {
                
                // Use the Isotope functions to get our generated URL
                $arrConfig = $this->getProductConfig($product);
                $href = $product->generateUrl($arrConfig['jumpTo'], true);
                
                // Forward the user to our new URL
                header("Location: " . $href);
                // kill this process so the forward can continue in its' place
                die();
            }
        }
    }
    
    /////////////////////////////////////////////////////////////////////////////////////////////////////
    // Isotope functions, all credit goes to Contao's Isotope package: https://github.com/isotope/core //
    /////////////////////////////////////////////////////////////////////////////////////////////////////
    
    protected function getProductConfig(IsotopeProduct $product)
    {
        $type = $product->getType();

        return array(
            'module'         => $this,
            'template'       => $this->iso_list_layout ?: $type->list_template,
            'gallery'        => $this->iso_gallery ?: $type->list_gallery,
            'buttons'        => $this->iso_buttons,
            'useQuantity'    => $this->iso_use_quantity,
            'disableOptions' => $this->iso_disable_options,
            'jumpTo'         => $this->findJumpToPage($product),
        );
    }
    
    protected function findJumpToPage(IsotopeProduct $objProduct)
    {
        global $objPage;
        global $objIsotopeListPage;

        $productCategories = $objProduct instanceof AbstractProduct ? $objProduct->getCategories(true) : [];
        $arrCategories = array();

        if (!$this->iso_link_primary) {
            $arrCategories = array_intersect(
                $productCategories,
                $this->findCategories()
            );
        }

        // If our current category scope does not match with any product category,
        // use the first allowed product category in the current root page
        if (empty($arrCategories)) {
            $arrCategories = $productCategories;
        }

        $arrCategories = Frontend::getPagesInCurrentRoot(
            $arrCategories,
            FrontendUser::getInstance()
        );

        if (!empty($arrCategories)
         && ($objCategories = PageModel::findMultipleByIds($arrCategories)) !== null
        ) {
            $blnMoreThanOne = $objCategories->count() > 1;
            foreach ($objCategories as $objCategory) {

                if ('index' === $objCategory->alias && $blnMoreThanOne) {
                    continue;
                }

                return $objCategory;
            }
        }

        return $objIsotopeListPage ? : $objPage;
    }
    
    protected function findCategories(array &$arrFilters = null)
    {
        if (null !== $arrFilters) {
            $arrCategories = null;

            foreach ($arrFilters as $k => $filter) {
                if ($filter instanceof CategoryFilter) {
                    unset($arrFilters[$k]);

                    if (!\is_array($arrCategories)) {
                        $arrCategories = $filter['value'];
                    } else {
                        $arrCategories = array_intersect($arrCategories, $filter['value']);
                    }
                }
            }

            if (\is_array($arrCategories)) {
                return empty($arrCategories) ? array(0) : array_map('intval', $arrCategories);
            }
        }

        if (null !== $this->arrCategories) {
            return $this->arrCategories;
        }

        if ($this->defineRoot && $this->rootPage > 0) {
            $objPage = PageModel::findWithDetails($this->rootPage);
        } else {
            global $objPage;
        }

        $t = PageModel::getTable();
        $arrCategories = null;
        $strWhere = "$t.type!='error_403' AND $t.type!='error_404'";

        if (!\Contao\System::getContainer()->get('contao.security.token_checker')->isPreviewMode()) {
            $time = Date::floorToMinute();
            $strWhere .= " AND ($t.start='' OR $t.start<'$time') AND ($t.stop='' OR $t.stop>'" . ($time + 60) . "') AND $t.published='1'";
        }

        switch ($this->iso_category_scope) {
            case 'global':
                $arrCategories = [$objPage->rootId];
                $arrCategories = Database::getInstance()->getChildRecords($objPage->rootId, 'tl_page', false, $arrCategories, $strWhere);
                break;

            case 'current_and_first_child':
                $arrCategories   = Database::getInstance()->execute("SELECT id FROM tl_page WHERE pid={$objPage->id} AND $strWhere")->fetchEach('id');
                $arrCategories[] = $objPage->id;
                break;

            case 'current_and_all_children':
                $arrCategories = [$objPage->id];
                $arrCategories = Database::getInstance()->getChildRecords($objPage->id, 'tl_page', false, $arrCategories, $strWhere);
                break;

            case 'parent':
                $arrCategories = [$objPage->pid];
                break;

            case 'product':
                /** @var \Isotope\Model\Product\Standard $objProduct */
                $objProduct = Product::findAvailableByIdOrAlias(Input::getAutoItem('product'));
                $arrCategories = [0];

                if ($objProduct !== null) {
                    $arrCategories = $objProduct->getCategories(true);
                }
                break;

            case 'article':
                $arrCategories = array($GLOBALS['ISO_CONFIG']['current_article']['pid'] ? : $objPage->id);
                break;

            case '':
            case 'current_category':
                $arrCategories = [$objPage->id];
                break;

            default:
                if (isset($GLOBALS['ISO_HOOKS']['findCategories']) && \is_array($GLOBALS['ISO_HOOKS']['findCategories'])) {
                    foreach ($GLOBALS['ISO_HOOKS']['findCategories'] as $callback) {
                        $arrCategories = System::importStatic($callback[0])->{$callback[1]}($this);

                        if ($arrCategories !== false) {
                            break;
                        }
                    }
                }
                break;
        }

        $this->arrCategories = empty($arrCategories) ? array(0) : array_map('intval', $arrCategories);

        return $this->arrCategories;
    }
    
}
