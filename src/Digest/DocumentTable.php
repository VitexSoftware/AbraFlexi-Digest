<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FlexiPeeHP\Digest;

/**
 * Description of DocumentTable
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class DocumentTable extends Table
{
    public function __construct($engine, $properties = array())
    {
        parent::__construct([], $properties);
    }
}
