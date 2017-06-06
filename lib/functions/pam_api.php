<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/
 * This script is distributed under the GNU General Public License 2 or later.
 *
 * @filesource  pam_api.php
 *
 * @author This piece of software has been copied and adapted from:
 *    Mantis - a php based bugtracking system (GPL)
 *    Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
 *    Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
 * @author franciscom (code adaptation)
 * @author anton7811 (code adaptation for pam)
 *
 * PAM API (authentication)
 *
 *
 * @internal revisions
 * @since 1.9.14
 * 20151030 - anton7811 - PAM authentication is introduced
 *
 */


// ----------------------------------------------------------------------------
// Attempt to authenticate the user against PAM
function pam_authenticate( $p_login_name, $p_password )
{

  if ( is_blank( $p_password ) )
  {
    return false;
  } 

  $t_authenticated = new stdClass();
  $t_authenticated->status_ok = false;
  $t_authenticated->status_code = null;
  $t_authenticated->status_verbose = '';

  $authCfg = config_get('authentication');

  $t_pam_script = $authCfg['pam_script_path'];

  $return = 1;
  exec($t_pam_script." ".$p_login_name." '".$p_password."'", $out, $return);
  if (!$return) {
    $t_authenticated->status_ok = true;
    $t_authenticated->status_code = 'OK';
    $t_authenticated->status_verbose = 'OK';
  } else {
    $t_authenticated->status_ok = false;
    $t_authenticated->status_code = 1;
    $t_authenticated->status_verbose = 'PAM AUTHENTICATION FAILED';
  }

  return $t_authenticated;
}

/**
 * Gets the value of all fields from PAM given the user name.
 *
 * @param string $p_username The user name.
 * @return hash array The field values.
 */
function pam_get_user_attributes( $p_username ) {

  $authCfg = config_get('authentication');

  $userString = exec('getent passwd ' . $p_username);
  $userStringArray = explode(':', $userString);

  $p_user = array(
                  "login" => $userStringArray[0],
                  "UID" => $userStringArray[2],
                  "GID" => $userStringArray[3]
                  );

  $p_userInfo = explode(',', $userStringArray[4]);
  // $p_userFullName = explode(' ', $p_userInfo[0]); 
  $p_userFullName = explode(' ', explode('-', $p_userInfo[0])[0]); 

  $p_user['firstName'] = (count($p_userFullName) > 1 ? $p_userFullName[0] : $p_user['login']);
  $p_user['firstName'] = ($p_user['firstName'] ? ucfirst($p_user['firstName']) : $p_user['login']);
  $p_user['lastName'] = (count($p_userFullName) > 1 ? ucfirst($p_userFullName[1]) : 'UNKNOWN_LAST_NAME');

  if (array_key_exists('pam_email_domain', $authCfg)) {
    $p_user['email'] = strtolower($p_user['firstName'] . '.' . $p_user['lastName'] . $authCfg['pam_email_domain']);
  }

  return $p_user;
}

