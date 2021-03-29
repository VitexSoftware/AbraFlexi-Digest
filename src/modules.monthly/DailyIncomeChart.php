<?php

/**
 * Description of DailyIncomeChart
 *
 * @author vitex
 */
class DailyIncomeChart extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface {

    public $timeColumn = 'datVyst';

    /**
     * // Color Pallette
      $orange:        #ff9e2c;
      $gray:          #999;
      $grayLight:     lighten($gray, 20%);
      $teal:          #4ecdc4;
      $salmon:        #ff6b6b;
      $lime:          #97f464;
      $peach:         lighten($orange, 20%);
      $grape:         #ab64f4;

     * @var array 
     */
    public static $currencyColor = ['CZK' => 'lime', 'EUR' => 'grape', 'USD' => 'teal'];

    /**
     *
     * @var \AbraFlexi\Digest\VerticalChart 
     */
    public $incomeChart = null;

    /**
     * 100% of chart
     * @var array 
     */
    private $average = [];

    /**
     * 
     */
    public function dig() {
        $banker = new AbraFlexi\Banka(null,['nativeTypes'=>false]);
        $averages = [];
        $incomes = $banker->getColumnsFromAbraFlexi(['mena', 'sumCelkem', 'sumCelkemMen',
            'datVyst'],
                array_merge($this->condition,
                        ['typPohybuK' => 'typPohybu.prijem', 'storno' => false]));
        $days = [];
        if (empty($incomes)) {
            $this->addItem(_('none'));
        } else {
            foreach ($incomes as $income) {
                $currency = self::getCurrency($income);
                if (!array_key_exists($income['datVyst'], $days)) {
                    $days[$income['datVyst']] = [];
                }
                if (!array_key_exists($currency, $averages)) {
                    $averages[$currency] = [];
                }

                if ($currency == 'CZK') {
                    $incomeAmount = floatval($income['sumCelkem']);
                } else {
                    $incomeAmount = floatval($income['sumCelkemMen']);
                }

                if (array_key_exists($currency, $days[$income['datVyst']])) {
                    $days[$income['datVyst']][$currency] += $incomeAmount;
                } else {
                    $days[$income['datVyst']][$currency] = $incomeAmount;
                }

                if (array_key_exists($income['datVyst'], $averages[$currency])) {
                    $averages[$currency][$income['datVyst']] += $incomeAmount;
                } else {
                    $averages[$currency][$income['datVyst']] = $incomeAmount;
                }
            }

            $avg = new \Ease\Container();
            foreach ($averages as $currency => $amounts) {
                $this->average[$currency] = ceil(array_sum($averages[$currency]) / count($averages[$currency]));
                $avg->addItem(new Ease\Html\DivTag(sprintf(_('100%% - average income is %s %s'),
                                        $this->average[$currency], $currency)));
            }

            $this->incomeChart = new \AbraFlexi\Digest\VerticalChart();

            foreach (array_reverse($days) as $day => $currencies) {
                $this->addChartDay($day, $currencies);
            }
    
            $this->addItem($this->cardBody([$avg,$this->incomeChart,'<br clear="all">']));


        }

        return !empty($incomes);
    }

    /**
     * 
     * @param type $day
     * @param type $currencies
     */
    public function addChartDay($day, $currencies) {
        foreach ($currencies as $curency => $amount) {
            $this->addChartCurrency($curency, $amount, $day);
        }
    }

    /**
     * 
     * @param type $currency
     * @param type $amount
     */
    public function addChartCurrency($currency, $amount, $day) {
        $this->addBar($currency, $amount, $day);
    }

    /**
     * 
     * @param string $caption
     * @param integer $height
     */
    public function addBar($caption, $amount, $day) {
        $maxAmount = $this->average[$caption]; //100%
        $procento = $maxAmount / 100;
        $percentChange = $amount / $procento;

        $this->incomeChart->addBar(round($percentChange), $amount,
                $amount . ' ' . $caption . ' ' . \AbraFlexi\RO::flexiDateToDateTime($day)->format('d/m D'),
                self::$currencyColor[$caption]);
    }

    /**
     * Module heading
     * @return string
     */
    public function heading() {
        return _('Incoming payments chart');
    }

}
