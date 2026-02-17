<?php

$html = '';
$hidden = ($_SESSION['admin']) ? '' : 'hidden';

foreach ($sensors as $sensor) {
  $html .= '
    <tr>
      <td>' . $sensor['name'] . '</td>
      <td>' . $sensor['pin'] . '</td>
      <td>' . $sensor['elevation'] . '</td>
      <td>' . $sensor['private'] . '</td>
      <td>
        ' . $sensor['id'] . '
        <form class="inline float right ' . $hidden . '" method="post" onsubmit="return confirm(\'' . t('setup.suresensor') . '\')">
          <input type="hidden" name="action" value="deletesensor">
          <input type="hidden" name="id" value="' . $sensor['id'] . '">
          <button type="submit" class="icon red" title="' . t('setup.delete') . '">'.icon('trash').'</button>
        </form>
      </td>
    </tr>';
}

if ($html)
  $html = '
    <h2>'.t('setup.sensors').'</h2>

    <div class="overflow-scroll">
      <table>
        <tr>
          <th>'.t('setup.nam').'</th>
          <th>'.t('setup.pin').'</th>
          <th>'.t('a.label').'</th>
          <th>'.t('setup.private').'</th>
          <th>ID</th>
        </tr>
    '.$html.'
      </table>
    </div>';

if ($_SESSION['admin'])
  $html .= '
    <h2>' . t('setup.addsensor') . '</h2>

    <form method="post" class="">
      <label for="sensorname">' . t('setup.name') . '</label>
      <input type="text" name="sensorname" id="sensorname" placeholder="Sensor name" value="" length="32" required="required">
      <label for="elevation">' . t('a.title') . ', ' . t('a.unit.m') . ':</label>
      <input type="number" name="elevation" id="elevation" value="100" length="4" required="required">
      <label for="private"><input type="checkbox" name="private" id="private"> ' . t('setup.private') . '</label>
      <input type="hidden" name="action" value="addsensor">
      <input type="submit" id="submit" value="' . t('setup.add') . '">
    </form>';

return $html;
