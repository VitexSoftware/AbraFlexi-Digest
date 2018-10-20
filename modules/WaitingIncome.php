<?php
/*
 * Debts
 */

/**
 * Description of WaitingIncome
 *
 * @author vitex
 */
class WaitingIncome extends \FlexiPeeHP\DigestMail\DigestModule implements \FlexiPeeHP\DigestMail\DigestModuleInterface
{

    public function heading()
    {
        return _('Waiting Income');
    }

    public function functionName($param)
    {
        ['datSplat lte \''.\FlexiPeeHP\FlexiBeeRW::dateToFlexiDate(new \DateTime()).'\' AND (stavUhrK is null OR stavUhrK eq \'stavUhr.castUhr\') AND storno eq false'];
    }
}
