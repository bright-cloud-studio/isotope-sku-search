<?php

use Contao\Config;

$GLOBALS['TL_DCA']['tl_settings']['palettes']['default'] = str_replace('{files_legend', '{isotope_sku_search_legend}, product_to_seek;{files_legend', $GLOBALS['TL_DCA']['tl_settings']['palettes']['default']);


$GLOBALS['TL_DCA']['tl_settings']['fields'] += [

    // Add a Radio option to choose if we should automatically generate or not
    'product_to_seek' => [
        'label'             => &$GLOBALS['TL_LANG']['tl_settings']['produt_to_seek'],
        'inputType'         => 'radio',
        'options'           => array('variant' => 'Variant', 'parent' => 'Parent'),
        'default'           => 'variant',
        'eval'              => array('mandatory'=>true, 'tl_class'=>'w50'),
    ]
    
];
