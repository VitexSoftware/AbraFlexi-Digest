<?php

namespace AbraFlexi\Digest;

/**
 * Description of Vertical
 *
 * @author vitex
 */
class VerticalChart extends \Ease\Html\UlTag
{

    /**
     *
     * @var string 
     */
    static public $chartCss = "@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,800);.chart{clear:both;padding:0;width:100%}@media (min-width: 700px){.chart{background:url(\"data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAGpCAYAAACwOHd0AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA+lpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYxIDY0LjE0MDk0OSwgMjAxMC8xMi8wNy0xMDo1NzowMSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczpkYz0iaHR0cDovL3B1cmwub3JnL2RjL2VsZW1lbnRzLzEuMS8iIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M1LjEgTWFjaW50b3NoIiB4bXA6Q3JlYXRlRGF0ZT0iMjAxMy0wNy0yNlQxMDozMDozNC0wNzowMCIgeG1wOk1vZGlmeURhdGU9IjIwMTMtMDctMjZUMTg6MTg6MjYtMDc6MDAiIHhtcDpNZXRhZGF0YURhdGU9IjIwMTMtMDctMjZUMTg6MTg6MjYtMDc6MDAiIGRjOmZvcm1hdD0iaW1hZ2UvcG5nIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkE1Q0VENTZGRUU0MTExRTJCNkYwQThDRDBGRURBREI1IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkE1Q0VENTcwRUU0MTExRTJCNkYwQThDRDBGRURBREI1Ij4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6QTVDRUQ1NkRFRTQxMTFFMkI2RjBBOENEMEZFREFEQjUiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6QTVDRUQ1NkVFRTQxMTFFMkI2RjBBOENEMEZFREFEQjUiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7ehQ8YAAAAeklEQVR42uzWsQ3AIAxFQYiyEZkpOzFURmAWwgS4sOjuS+5OcvvqGKOXzeoCZQvmnFtwlWAAAAAAAAAAkCnSe90bgSZ6AQAAAAAAANEregEAAAAAAICTydoi0CPwSFYAAAAAAAAgUaRhcH6pF4oUAAAAAAAAAPLgF2AAdCstyHhunaAAAAAASUVORK5CYII=\") right top repeat-x;height:50%;margin:0 auto emCalc(-32px)}}.chart li{display:block;height:125px;padding:emCalc(25px) 0;position:relative;text-align:center;vertical-align:bottom;-moz-border-radius:4px 4px 0 0;-webkit-border-radius:4px;border-radius:4px 4px 0 0;-moz-box-shadow:inset 0 1px 0 0 rgba(255,255,255,0.6);-webkit-box-shadow:inset 0 1px 0 0 rgba(255,255,255,0.6);box-shadow:inset 0 1px 0 0 rgba(255,255,255,0.6)}@media (min-width: 700px){.chart li{display:inline-block;margin:0 5px 0 0;min-width:30px}}.chart .axis{display:none;top:emCalc(-45px);width:6%}@media (min-width: 700px){.chart .axis{display:inline-block}}.chart .label{background:#ccc;margin:-9px 0 71px 0}.chart .percent{opacity:.4;width:100%;font-size:10px;font-size:.625rem}@media (min-width: 700px){.chart .percent{position:absolute;font-size:8px;font-size:.5rem}}.chart .percent span{font-size:8px;font-size:.5rem}.chart .skill{font-weight:800;opacity:.5;overflow:hidden;text-transform:uppercase;width:100%;font-size:14px;font-size:.875rem}@media (min-width: 700px){.chart .skill{bottom:20px;position:absolute;font-size:8px;font-size:.5rem}}.chart .teal{background:#4ecdc4;border:1px solid #4ecdc4;background-image:url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJvYmplY3RCb3VuZGluZ0JveCIgeDE9IjAuNSIgeTE9IjAuMCIgeDI9IjAuNSIgeTI9IjEuMCI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzc2ZDhkMSIvPjxzdG9wIG9mZnNldD0iNzAlIiBzdG9wLWNvbG9yPSIjNGVjZGM0Ii8+PC9saW5lYXJHcmFkaWVudD48L2RlZnM+PHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNncmFkKSIgLz48L3N2Zz4g');background-size:100%;background-image:-webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #76d8d1),color-stop(70%, #4ecdc4));background-image:-moz-linear-gradient(#76d8d1,#4ecdc4 70%);background-image:-webkit-linear-gradient(#76d8d1,#4ecdc4 70%);background-image:linear-gradient(#76d8d1,#4ecdc4 70%)}.chart .salmon{background:#ff6b6b;border:1px solid #ff6b6b;background-image:url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJvYmplY3RCb3VuZGluZ0JveCIgeDE9IjAuNSIgeTE9IjAuMCIgeDI9IjAuNSIgeTI9IjEuMCI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2ZmOWU5ZSIvPjxzdG9wIG9mZnNldD0iNzAlIiBzdG9wLWNvbG9yPSIjZmY2YjZiIi8+PC9saW5lYXJHcmFkaWVudD48L2RlZnM+PHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNncmFkKSIgLz48L3N2Zz4g');background-size:100%;background-image:-webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #ff9e9e),color-stop(70%, #ff6b6b));background-image:-moz-linear-gradient(#ff9e9e,#ff6b6b 70%);background-image:-webkit-linear-gradient(#ff9e9e,#ff6b6b 70%);background-image:linear-gradient(#ff9e9e,#ff6b6b 70%)}.chart .lime{background:#97f464;border:1px solid #97f464;background-image:url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJvYmplY3RCb3VuZGluZ0JveCIgeDE9IjAuNSIgeTE9IjAuMCIgeDI9IjAuNSIgeTI9IjEuMCI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2I3Zjc5NCIvPjxzdG9wIG9mZnNldD0iNzAlIiBzdG9wLWNvbG9yPSIjOTdmNDY0Ii8+PC9saW5lYXJHcmFkaWVudD48L2RlZnM+PHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNncmFkKSIgLz48L3N2Zz4g');background-size:100%;background-image:-webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #b7f794),color-stop(70%, #97f464));background-image:-moz-linear-gradient(#b7f794,#97f464 70%);background-image:-webkit-linear-gradient(#b7f794,#97f464 70%);background-image:linear-gradient(#b7f794,#97f464 70%)}.chart .peach{background:#ffcd92;border:1px solid #ffcd92;background-image:url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJvYmplY3RCb3VuZGluZ0JveCIgeDE9IjAuNSIgeTE9IjAuMCIgeDI9IjAuNSIgeTI9IjEuMCI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2ZmZTRjNSIvPjxzdG9wIG9mZnNldD0iNzAlIiBzdG9wLWNvbG9yPSIjZmZjZDkyIi8+PC9saW5lYXJHcmFkaWVudD48L2RlZnM+PHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNncmFkKSIgLz48L3N2Zz4g');background-size:100%;background-image:-webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #ffe4c5),color-stop(70%, #ffcd92));background-image:-moz-linear-gradient(#ffe4c5,#ffcd92 70%);background-image:-webkit-linear-gradient(#ffe4c5,#ffcd92 70%);background-image:linear-gradient(#ffe4c5,#ffcd92 70%)}.chart .grape{background:#ab64f4;border:1px solid #ab64f4;background-image:url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJvYmplY3RCb3VuZGluZ0JveCIgeDE9IjAuNSIgeTE9IjAuMCIgeDI9IjAuNSIgeTI9IjEuMCI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iI2M1OTRmNyIvPjxzdG9wIG9mZnNldD0iNzAlIiBzdG9wLWNvbG9yPSIjYWI2NGY0Ii8+PC9saW5lYXJHcmFkaWVudD48L2RlZnM+PHJlY3QgeD0iMCIgeT0iMCIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNncmFkKSIgLz48L3N2Zz4g');background-size:100%;background-image:-webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #c594f7),color-stop(70%, #ab64f4));background-image:-moz-linear-gradient(#c594f7,#ab64f4 70%);background-image:-webkit-linear-gradient(#c594f7,#ab64f4 70%);background-image:linear-gradient(#c594f7,#ab64f4 70%)}";

    /**
     * 
     * @param array $axis
     * @param array $properties
     */
    public function __construct($axis = [], $properties = array())
    {
        parent::__construct(null, $properties);
        $this->addTagClass('chart');
        if (!empty($axis)) {
            $axisLi = new \Ease\Html\LiTag(null, ['class' => 'axis']);
            foreach ($axis as $axe) {
                $axisLi->addItem(new \Ease\Html\DivTag($axe,
                                ['class' => 'label']));
            }
            $this->addItem($axisLi);
        }
//        $this->addCSS(self::$chartCss);
    }

    /**
     * 
     * @param int $percent
     * @param float $amount
     * @param string $caption
     * @param string $addClass
     */
    public function addBar($percent, $amount, $caption, $addClass)
    {
        $bar = new \Ease\Html\LiTag(null,
                ['class' => 'bar ' . $addClass, 'style' => 'height: ' . $percent . 'px;', 'title' => $caption]);
        $bar->addItem(new \Ease\Html\DivTag([$percent, new \Ease\Html\SpanTag('%')],
                        ['class' => 'percent']));
        $bar->addItem(new \Ease\Html\DivTag($caption, ['class' => 'skill']));
        $this->addItem($bar);
    }
}