<?php

/** @var yii\web\View $this */

$this->title = 'SESSIA Marketplace';
?>
<div class="site-index">
    <h2 class="text-center d-none">
        <?= Yii::t('app', 'По суммам') ?>
    </h2>
<?php
    for ($i = 0; $i < 6; $i++) {
?>
        <div id="report-<?= $i ?>" class="my-3" style="height: 50vh"></div>
<?php
    }
?>
    <br>
    <br>
    <!--
    <h2 class="text-center">
        <?= Yii::t('app', 'По количеству') ?>
    </h2>
<?php
    for ($i = 6; $i < 12; $i++) {
?>
        <div id="report-<?= $i ?>" class="my-3" style="height: 50vh"></div>
<?php
    }
?>
    -->
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