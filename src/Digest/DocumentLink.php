<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AbraFlexi\Digest;

/**
 * Description of DocumentLink
 *
 * @author vitex
 */
class DocumentLink extends \Ease\Html\ATag {

    public function __construct($code, $engine, $properties = []) {
        $engine->setMyKey($code);
        parent::__construct($engine->getApiURL(), $engine->getEvidence() . ':' . \AbraFlexi\RO::uncode($code), $properties);
    }

}
