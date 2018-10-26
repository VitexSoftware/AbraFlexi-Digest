<?php
/**
 * FlexiBee Digest - Yearly 
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018 Vitex Software
 */

namespace FlexiPeeHP\Digest;
define('EASE_APPNAME', 'FlexiBeeAllTimeDigest');

require_once './init.php';

$subject = sprintf(_('FlexiBee Alltime'));

$digestor = new Digestor($subject);
$digestor->dig(null, constant('MODULE_DIR'));
