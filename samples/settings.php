<?php
/*********************************\ 
SETTINGS v1.0.18.1&psi;
 do not edit this file ! use admin cp unless told otherwise
\*********************************/
$version = "3.00";
$settings['logo'] = "/img/logo.png"; //not used
$settings['gamelocation'] = "/<user>/games"; //game location (multiple servers)
$settings['adminemail'] = "someone@somewhere.co.uk"; // email address to send cron reports to
$settings['server_tz'] = "Europe/London"; //  set where in the world the server thinks it is
$settings['paypal_email'] = "someone@somewhere.co.uk"; // future expansion
$settings['siteclosed'] = "0"; // stop any one accessing the api
$settings['siteclosed_url'] = "/closed.html"; // if closed display this file 
$settings['pwdlen'] = "8"; // minimum password length
$settings['https'] = "1"; // forces https not used
$settings['url'] = ""; // resolvable url to where the api is installed
$settings['steamkey'] = ""; // steam api key
$settings['SQ_TIMEOUT'] = 2; // timeout for game server queries
$settings['year'] = "0"; // set to 1 if you want dates in roman numerals
$settings['start_year'] = "2014";
$settings['time_format'] = "h:i:s a"; // all times are displayed  in this format
$settings['date_format'] = "d/m/Y"; // all dates are displayed in this format
$settings['login'] = "1"; //unused currently
$settings['ip_key'] =''; // your data key for ipdata.io
$settings['scanlog'] = 1; //unused currently
$settings['send_cors'] = 0; // send cors headers or not 0 = not 1 = send header
$settings['secure_user'] =''; // ajax secure user
$settings['secure_password']=''; // ajax secure password
$settings['mux'] = "screen"; // set to tmux if you want to use tmux
$settings['router_ip'] = true; // show all router ip's in the list
$settings['branch'] = "v3"; // which branch to  to track
?>
