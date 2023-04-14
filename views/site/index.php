<?php

/** @var yii\web\View $this */

$this->title = 'SESSIA Marketplace';
?>
<div class="site-index">
    <h2 class="text-center">
        <?= Yii::t('app', 'По суммам') ?>
    </h2>
    
    <div id="report-0" class="my-3" style="height: 50vh"></div>
    <div id="report-1" class="my-3"></div>
    <div id="report-2" class="my-3"></div>
    <div id="report-3" class="my-3"></div>
    <div id="report-4" class="my-3"></div>
    <div id="report-5" class="my-3"></div>

    <br>
    <br>

    <h2 class="text-center">
        <?= Yii::t('app', 'По количеству') ?>
    </h2>
    
    <div id="report-6" class="my-3"></div>
    <div id="report-7" class="my-3"></div>
    <div id="report-8" class="my-3"></div>
    <div id="report-9" class="my-3"></div>
    <div id="report-10" class="my-3"></div>
    <div id="report-11" class="my-3"></div>
</div>

<?php
$this->registerJs("
    google.charts.load('current', {
        'packages': [
            'corechart'
        ]
    });
    google.charts.setOnLoadCallback(drawCharts);
");
$script = '';
foreach ($reports as $r => $report) {
    $name = $report['name'];
    $data = $report['data'];
    $script .= "
        var chart$r = new google.visualization.LineChart(document.getElementById('report-$r'));
        chart$r.draw(google.visualization.arrayToDataTable($data), {
            title: '$name',
            curveType: 'function',
            legend: {
                position: 'bottom'
            }
        });
    ";
}
$this->registerJs("function drawCharts() {" . $script . "}");
?>