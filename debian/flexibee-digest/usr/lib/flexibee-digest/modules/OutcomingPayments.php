<?php
/*
 * Outcoming payments
 */

/**
 * Description of OutcomingPayments
 *
 * @author vitex
 */
class OutcomingPayments extends \FlexiPeeHP\Digest\DigestModule  implements \FlexiPeeHP\Digest\DigestModuleInterface
{
    public function heading()
    {
        return _('Outcoming payments');
    }
}
