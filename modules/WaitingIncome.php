<?php
/*
 * Debts
 */

/**
 * Description of WaitingIncome
 *
 * @author vitex
 */
class WaitingIncome  extends \FlexiPeeHP\DigestMail\DigestModule implements \FlexiPeeHP\DigestMail\DigestModuleInterface
{
    public function heading()
    {
        return _('Waiting Income');
    }
}