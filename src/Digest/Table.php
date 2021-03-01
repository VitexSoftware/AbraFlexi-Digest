<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AbraFlexi\Digest;

/**
 * Description of Table
 *
 * @author vitex
 */
class Table extends \Ease\Html\TableTag {

    /**
     * Digest Table
     * 
     * @param array $thCols    TH Columns
     * @param array $properties
     */
    public function __construct($thCols, $properties = array()) {
        parent::__construct(null, $properties);
        $this->addRowHeaderColumns($thCols);
        $this->addTagClass('table');
    }

}
