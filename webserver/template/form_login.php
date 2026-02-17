<?php

return '
<form method="post" class="">
    <label for="login">' . t('setup.login') . ':</label>
    <input type="text" name="login" id="login" placeholder="Login" value="" length="32" required="required">
    <label for="password">' . t('setup.password') . ':</label>
    <input type="password" name="password" id="password" placeholder="Password" value="" length="4" required="required">
    <input type="hidden" name="action" value="login">
    <input type="submit" id="submit" value="' . t('setup.log_in') . '">
</form>';

