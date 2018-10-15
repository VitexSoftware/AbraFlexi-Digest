<?php
/*
 * Outcoming payments
 */

/**
 * Description of OutcomingPayments
 *
 * @author vitex
 */
class OutcomingPayments extends \FlexiPeeHP\DigestMail\DigestModule  implements \FlexiPeeHP\DigestMail\DigestModuleInterface
{
    public function heading()
    {
        return _('Outcoming payments');
    }
}
