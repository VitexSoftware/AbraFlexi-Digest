<?php
/*
 * What we have to pay
 */

/**
 * Description of WaitingPayments
 *
 * @author vitex
 */
class WaitingPayments extends \FlexiPeeHP\Digest\DigestModule implements \FlexiPeeHP\Digest\DigestModuleInterface
{

    public function heading()
    {
        return _('We have to pay');
    }
    
    public function functionName($param)
    {
        ['datSplat lte \''.\FlexiPeeHP\FlexiBeeRW::dateToFlexiDate(new \DateTime()).'\' AND (stavUhrK is null OR stavUhrK eq \'stavUhr.castUhr\') AND storno eq false'];
    }
    
}
