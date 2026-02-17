<?php

$html = '';
$hidden = ($_SESSION['admin']) ? '' : 'hidden';

foreach ($users as $user) {
  $html .= '
    <tr>
      <td>' . $user['login'] . '</td>
      <td>' . $user['admin'] . '</td>
      <td>
        ' . $user['id'] . '
        <form class="inline float right ' . $hidden . '" method="post" onsubmit="return confirm(\'' . t('setup.sureuser') . '\')">
          <input type="hidden" name="action" value="deleteuser">
          <input type="hidden" name="id" value="' . $user['id'] . '">
          <button type="submit" class="icon red" title="' . t('setup.delete') . '">'.icon('trash').'</button>
        </form>
      </td>
    </tr>';
}

if ($html)
  $html = '
    <h2>'.t('setup.users').'</h2>

    <div class="overflow-scroll">
      <table>
        <tr>
          <th>'.t('setup.login').'</th>
          <th>'.t('setup.admin').'</th>
          <th>ID</th>
        </tr>
    '.$html.'
      </table>
    </div>';

$disabledCheckbox = ($firstSetup) ? 'disabled="disabled" checked><input type="hidden" name="admin" id="admin" value="on"' : '';
if ($firstSetup || $_SESSION['admin'])
  $html .= '
    <h2>' . t('setup.adduser') . '</h2>

    <form method="post" class="">
      <label for="login">' . t('setup.login') . ':</label>
      <input type="text" name="login" id="login" placeholder="Login" value="" length="32" required="required">
      <label for="password">' . t('setup.password') . ':</label>
      <input type="password" name="password" id="password" placeholder="Password" value="" minlength="6" required="required">
      <label for="admin"><input type="checkbox" name="admin" id="admin" ' . $disabledCheckbox . '> ' . t('setup.admin') . '</label>
      <input type="hidden" name="action" value="adduser">
      <input type="submit" id="submit" value="' . t('setup.add') . '">
    </form>';

return $html;
