<?php
include 'header.php';
?>
<div id="wrapper" class="flex glass" >

  <aside id="latest" class="panel flex">
    <div id="datetime" class="">
      <div id="opts">
        <select class="inline" id="units_t">
          <option value="C"><?= t('t.unit.C') ?></option>
          <option value="F"><?= t('t.unit.F') ?></option>
          <!--
          <option value="K"><?= t('t.unit.K') ?></option>
          -->
        </select>
        <!--
        <select class="inline" id="units_p">
          <option value="mb"><?= t('p.unit.mb') ?></option>
          <option value="inHg"><?= t('p.unit.inHg') ?></option>
          <option value="mmHg"><?= t('p.unit.mmHg') ?></option>
        </select>
        <select class="inline" id="units_a">
          <option value="m"><?= t('a.unit.m') ?></option>
          <option value="ft"><?= t('a.unit.ft') ?></option>
        </select>
        -->
        <select class="inline" id="locale">
        </select>
      </div>
      <span class="label"></span>
    </div>
    <div id="temperature"></div>
    <div class="flex column">
      <p id="humidity"><span class="icon"><?= icon("humidity") ?></span><span class="label"></span><span class="value"></span></p>
      <p id="dewpoint"><span class="icon"><?= icon("dewpoint") ?></span><span class="label"></span><span class="value"></span></p>
      <p id="pressure"><span class="icon"><?= icon("pressure") ?></span><span class="label"></span><span class="value"></span></p>
    </div>
    <div class="flex column">
      <p id="forecast"><span class="icon steady"><?= icon("forecast") ?></span><span class="label"></span><span class="value"></span></p>
      <p id="voltage"><span class="icon"><?= icon("battery") ?></span><span class="label"></span><span class="value"></span></p>
      <p id="updated"><span class="icon"><?= icon("timeout") ?></span><span class="label"></span><span class="value"></span></p>
    </div>
  </aside>
  
  <main id="" class="panel flex glass" >
    <div id="records">
      <a href="setup.php" title="<?= t('setup.title') ?>"><span class="icon"><?= icon("settings") ?></span></a>
      <span class="label hide-s"></span>
      <select class="inline" id="sensors">
      </select>
      <select class="inline" id="rec">
        <option value="1"></option>
        <option value="3"></option>
        <option value="7"></option>
        <option value="0"></option>
      </select>
    </div>
    <div id="chart_container"><canvas id="conditions_chart" style="display:none"></canvas></div>
  </main>

  <script src="assets/app.js?<?= filemtime('assets/app.js') ?>"></script>

</div>
<?php
include 'footer.php';

