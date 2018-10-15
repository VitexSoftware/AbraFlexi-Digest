<?php
/*
 * What we have to pay
 */

/**
 * Description of WaitingPayments
 *
 * @author vitex
 */
class WaitingPayments extends \FlexiPeeHP\DigestMail\DigestModule implements \FlexiPeeHP\DigestMail\DigestModuleInterface
{

    public function heading()
    {
        return _('We have to pay');
    }
}
