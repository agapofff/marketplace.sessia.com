<?php

/** @var yii\web\View $this */

$this->title = 'SESSIA Marketplace';
?>
<div class="site-index">
<!--
    <ul class="nav nav-tabs" id="reportsTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="sum-tab" data-bs-toggle="tab" data-bs-target="#sum-tab-pane" type="button" role="tab" aria-controls="sum-tab-pane" aria-selected="true">
                <?= Yii::t('app', 'По суммам') ?>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="qty-tab" data-bs-toggle="tab" data-bs-target="#qty-tab-pane" type="button" role="tab" aria-controls="qty-tab-pane" aria-selected="false">
                <?= Yii::t('app', 'По количеству') ?>
            </button>
        </li>
    </ul>
    <div class="tab-content" id="reportsTabContent">
        <div class="tab-pane fade show active" id="sum-tab-pane" role="tabpanel" aria-labelledby="sum-tab" tabindex="0">
            <div class="row">
                <div class="col-md-6">
                    <div id="report-0" class="my-3"></div>
                    <div id="report-1" class="my-3"></div>
                    <div id="report-2" class="my-3"></div>
                </div>
                <div class="col-md-6">
                    <div id="report-3" class="my-3"></div>
                    <div id="report-4" class="my-3"></div>
                    <div id="report-5" class="my-3"></div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="qty-tab-pane" role="tabpanel" aria-labelledby="qty-tab" tabindex="0">
            <div class="row">
                <div class="col-md-6">
                    <div id="report-6" class="my-3"></div>
                    <div id="report-7" class="my-3"></div>
                    <div id="report-8" class="my-3"></div>
                </div>
                <div class="col-md-6">
                    <div id="report-9" class="my-3"></div>
                    <div id="report-10" class="my-3"></div>
                    <div id="report-11" class="my-3"></div>
                </div>
            </div>    
        </div>
    </div>
-->

<?php
    foreach ($reports as $r => $report) {
?>
        <div id="report-<?= $r ?>" class="my-3"></div>
<?php
    }
?>
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
        var chart" . $r . " = new google.visualization.LineChart(document.getElementById('report-$r'));
        chart" . $r . ".draw(google.visualization.arrayToDataTable($data), {
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