<?php
/*
 * Incoming payments for us
 */

/**
 * Description of IncomingPayments
 *
 * @author vitex
 */
class IncomingPayments extends \FlexiPeeHP\Digest\DigestModule  implements \FlexiPeeHP\Digest\DigestModuleInterface
{
    public function heading()
    {
        return _('Incoming payments');
    }
}
