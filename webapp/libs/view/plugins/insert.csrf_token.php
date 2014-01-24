<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */

/**
 * Help Link
 *
 * Type:     insert<br>
 * Name:     csrf_token
 * Date:     April 26, 2011
 * Purpose:  Returns session CSRF token.
 * Input:    key
 * Example:  {insert name="csrf_token"}
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2011-2013 Mark Wilkie
 * @version 1.0
 */
function smarty_insert_csrf_token($params, &$smarty) {
    $csrf_token = Session::getCSRFToken();
    if (isset($csrf_token)) {
        return sprintf('<input type="hidden" name="csrf_token" value="%s" />', $csrf_token);
    } else {
        return '<!-- Error: no csrf token found in session -->';
    }
}
