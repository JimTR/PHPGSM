#!/usr/bin/php -d memory_limit=2048M
<?php
/*
 * scanlog.php
 * 
 * Copyright 2020 Jim Richardson <jim@noideersoftware.co.uk>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * scan game logs
 * SELECT * FROM `players` WHERE `server` LIKE 'fofserver2'
 * SELECT country,COUNT(*) as total FROM players GROUP BY country order by total desc limit 10 ;
 * SELECT name,country,log_ons from players order by log_ons desc limit 0,10
 * Major re work January 2021 
 */
//echo cr;
define('cr',PHP_EOL);
    $shortopts ="f:s:v::";
	$longopts[]="debug::";
	$longopts[]="help::";
	$longopts[]="quick::";
	$longopts[]="remote::";
	$options = getopt($shortopts,$longopts);
	define ('options',$options);
	if(isset($options['debug'])) {
		define('debug',true);
		error_reporting( -1 );
		unset($options['debug']);
	}
	else {
		define('debug',false);
		error_reporting(0);

	}
	if (isset($options['quick'])) 
	{
		define('quick',true);
		if(debug) {
			echo 'quick scan enabled'.cr;
		}
	}
	else {
		define('quick',false);
		
	}
	
	
	if(debug) {
		echo 'current options '.cr.print_r($options,true).cr;
		echo '$argv is '.cr.print_r ($argv,true).cr;
	}
	$prog = basename($argv[0]);

require ('includes/master.inc.php');
require 'includes/class.emoji.php';
require 'includes/class.steamid.php';
    $version = 2.41;
	$build = "14776-3797576078";
if(isset(options['v'])){
			echo "Scanlog v$version - $build © NoIdeer Software ".date('Y').cr;
		exit;
	}

if (empty( $settings['ip_key'] )) {
	echo 'Fatal Error - api key missing'.cr;
	exit(7);
}
$key = $settings['ip_key'] ;
if (is_cli() == false){
echo 'wrong enviroment';
exit;
}
if(empty($options['s'])) {
	echo "Scanlog v$version - $build © NoIdeer Software ".date('Y').cr;
	if (!isset(options['help'])) {
		echo 'Please supply a Server to scan'.cr;
	}
	echo 'Examples :- '.cr."\t".$prog.' -s<server id>'.cr;
	echo "\t$prog -s<server id> -f<file to import> do not use -f with the all server option it is used for importing data and \e[4mnot\e[0m scaning".cr;
	echo "\t$prog -s<all> this will scan all servers using the default log(s), slow but thorough ".cr;
	echo "\t--quick scans the current steam log rather than the full log faster but not so thorough, works with all other options".cr;
	echo "\t--debug logs technical details to the console, works with all other options".cr;
	echo "\t--help this information".cr;
	exit(0);
}
$asql = 'select * from players where steam_id64="'; // sql stub for user updates
$update_done= array();
$file =options['s'];
if ($file == 'all') {
	
		$allsql = 'SELECT servers.* , base_servers.url, base_servers.port as bport, base_servers.fname,base_servers.ip as ipaddr FROM `servers` left join `base_servers` on servers.host = base_servers.ip where servers.id <>"" and servers.running="1" order by servers.host_name';
		$game_results = $database->get_results($allsql);
		$display='';
	//print_r ($game_results);
	foreach ($game_results as $run) {
		//bulid path
		$server_key = md5( ip2long($run['ipaddr'])) ;
		if (quick) {
			$path = $run['url'].':'.$run['bport'].'/ajaxv2.php?action=lsof&filter='.$run['host_name'].'&loc='.$run['location'].'/'.$run['game'].'&return=content'; //used for steam log
			
		}
		else {
			$path = $run['url'].':'.$run['bport'].'/ajaxv2.php?action=get_file&file='.$run['location'].'/log/console/'.$run['host_name'].'-console.log&key='.$server_key; //used for screen log
		}
		$tmp = file_get_contents($path);
		if(debug) {
			echo $run['host_name'].' '.$path.cr; // debug code
		}
				
		if (!empty($tmp)) {
			//echo $tmp.cr; //debug code
		$display .= do_all($run['host_name'],$tmp);
	}
	
	}
	
	echo 'display = '.strlen($display).cr;
	if(empty($display)) {
		echo 'no output'.cr;
	}
	echo $display;
}
else {
	// do supplied file
	if(isset(options['f'])) {
	if (!file_exists(options['f'])) {
		echo 'could not open '.options['f'].cr;
		exit (1);
	}
}
	$allsql = 'SELECT servers.* , base_servers.url, base_servers.port as bport, base_servers.fname,base_servers.ip as ipaddr FROM `servers` left join `base_servers` on servers.host = base_servers.ip where servers.host_name="'.options['s'].'"';
		//echo $allsql.cr;
	$run = $database->get_row($allsql);
	if(empty($run)) {
		echo 'Invalid server id '.options['s'].' correct & try again'.cr;
		exit(2);
	}
	$server_key = md5( ip2long($run['ipaddr'])) ;
	//$path = $argv[1];
	//print_r($run);
	if (!isset(options['f'])) {
		if (quick) {
			$path = $run['url'].':'.$run['bport'].'/ajaxv2.php?action=lsof&filter='.$run['host_name'].'&loc='.$run['location'].'/'.$run['game'].'&return=content'; //used for steam log
			if (debug) {
				echo $path.cr;
			}
		}
		else {
			$path = $run['url'].':'.$run['bport'].'/ajaxv2.php?action=get_file&file='.$run['location'].'/log/console/'.$run['host_name'].'-console.log&key='.$server_key;
		}
	}
	else {
		// assume run local 
		$path = options['f'];
	}
		
		
		$tmp = file_get_contents($path);
		echo do_all(options['s'],$tmp);
}


function do_all($server,$data) {
	// cron code
	
	$count = 0;
	$done= 0;
	$update_users = 0;
	$uds = false;
	global $database, $key;
	$update_req = 'Your server needs to be restarted in order to receive the latest update.';
	$asql = 'select * from players where steam_id64="'; // sql stub for user updates
	$rt = 'Processing server '.$server.cr.cr;
	$log = explode(cr,$data);
	if(debug) {
		echo 'Rows to process '.count($log).cr; //debug code
	}
    foreach ($log as $value) {
		// loop lines, in here check for server needs a restart
		if ( strpos($data,$update_req)) {
			// server needs an update & restart
			$uds = true;
		}
		$bot = strpos($value,' connected, address "none');
		if($bot) {continue;} //ignore bot lines
		$x = strpos($value,' connected, address ');
		if ($x >0) {
		// save output
		$value=trim($value);
		$tmp = user_data($value);
		$la[$tmp['name'] ]= $tmp;
		
	}
		 
}
if (debug) {
	if (isset($la)) {
		echo 'Found the following:-'.cr. print_r($la,true).cr;
	} //debug code
	else {
		echo "nothing found for $server".cr;
	}
}
if (!isset($la)) { 
	$pc = 0;
	} 
	else {$pc = count($la);}
	if ( $pc == 0 ) {
	//echo "\t Nothing to do".cr;
	if ($uds == true) {
		$s = update_server($server);
		return $s;
	}
return ; // "no player data for $server".cr;
}

foreach ($la as $user_data) {
	$logon = false; 
	// now do data
	$user = trim($user_data['id']);
	$user_search = $user_data['id2'].'"';
	if(debug) {
		echo "query using $user_search".cr; 
	}
	$username = $user_data['name'];
	$ip = $user_data['ip'];
	$user_data['ip'] = ip2long($user_data['ip']);
	$modify = false;
	$added = false;
	; // start of log line
	$ut='';
	$result = $database->get_row($asql.$user_search);
	if (!empty($result)){
		$user_stub ="\t".$username.' ('.$result['country'].') ';
		unset($result['id']); // take out id
		unset($result['steam_id']);
		$where['steam_id64'] = $user_data['id2'];
		$last_logon = strtotime($user_data['time']);
		
		
		if ($last_logon >  $result['last_log_on']) {
			$result['last_log_on'] = $last_logon;
			$result['log_ons'] ++;
			$ut.= ' new logon  at '.$user_data['time'].' (total '.$result['log_ons'].')';
			$modify=true;
			$logon = true;
		}
		if (empty($result['steam_id64'])) {
		$ut .=' no ID64 (correcting)';
		$result['steam_id64'] = $user_data['id2'];
		$modify=true;
		}
		
		if ($user_data['ip'] <> $result['ip'] ) {
			$ut.= ' IP Changed from '.long2ip($result['ip']).' to '.long2ip($user_data['ip']);
			//check ip on change
			$ip_data = get_ip_detail($ip);
			$result['continent'] = $ip_data['continent_name'];
			$result['country_code'] = $ip_data['country_code'];
			$result['country'] = $ip_data['country_name'];
			$result['region'] = $ip_data['region'];
			$result['city'] = $ip_data['city'];
			$result['flag'] = Emoji::Encode($ip_data['emoji_flag']);
			$result['time_zone'] = $ip_data['time_zone']['name'];
			if (isset($ip_data['asn'])) {
				$result['type'] = $ip_data['asn']['type'];
		}
		else {
			$result['type'] ='n/a';
		}
			$result['threat'] = $ip_data['threat']['is_threat'];
			$result['ip'] = $user_data['ip'];
			$modify=true;
		}
		
		if (trim($username) <> $result['name']) {
			$ut.= ' User name change from '.$result['name'].' to '.$username;
			$result['name'] = trim($username);
			$modify=true;
		}
		
		if(strpos($result['server'],$server) === false) {
			$ut.= ' played a new server';
			$result['server'].=$server.'*';
			$modify=true;
			}
			
		if ($modify) {
		$result = $database->escape($result);
		$n = $database->update('players',$result,$where);
		if ($logon == true) { 
		$sql = 'call update_logins ('.$result['steam_id64'].',"'.$server.'",'.$result['last_log_on'].')';
		$database->query($sql);
		unset($logon);
		}
		if ($n === false) {
			//
			echo cr.'Database Update failed with'.cr;
			print_r($result);
			echo 'trying again';
			$database->query('SET character_set_results = binary;');
			$result['name'] = $database->filter($result['name']);
			unset($result['steam_id']);
			$n = $database->update('players',$result,$where);
					 
		}
		$update_users++;
		$ut .= cr;
	}
	else{
		if (debug) {
			echo "no change for $username".cr;
		}
	}
	}
	else {
		if (debug) {
			echo 'adding '.$username.cr;
		}
		$added = true;
		$ut .= $ut.' New user';
		$count ++;
		$last_logon = time();
		$ip_data = get_ip_detail($ip);
		$result['ip'] = $user_data['ip'];
		//$result['steam_id'] = $user;
		$result['steam_id64'] = $user_data['id2'];
		$result['name'] = $username;
		$result['first_log_on'] = $last_logon;
		$result['log_ons'] = 1;
		$result['last_log_on'] = $last_logon;
		$result['continent'] = $ip_data['continent_name'];
		$result['country_code'] = $ip_data['country_code'];
		$result['country'] = $ip_data['country_name'];
		$result['region'] = $ip_data['region'];
		$result['city'] = $ip_data['city'];
		$result['flag'] = Emoji::Encode($ip_data['emoji_flag']);
		$result['time_zone'] = $ip_data['time_zone']['name'];
		if (isset($ip_data['asn']['type'])) {
		$result['type'] = $ip_data['asn']['type'];
	}
	else {
		$result['type'] = 'N/A';
	}
		$result['threat'] = $ip_data['threat']['is_threat'];
		$result['server'] = $server.'*';
		
		
		$result = $database->escape($result);
	    $in = $database->insert('players',$result);
	    $user_stub ="\t".$username.' ('.$result['country'].') ';
	    if ($in === true ){
			 	 $done++;
			 	 $ut .=' Record added at '.$user_data['time'].cr;
			 	 $sql = 'call update_logins ('.$result['steam_id64'].',"'.$server.'",'.$result['last_log_on'].')';
			 	 //$ut .= $sql.cr;
			 	 $database->query($sql);
			 }
	   else {
		 echo 'Database Insertion failed with'.cr;
		 print_r($result);		 
		//echo cr;
}

 //$rt.=cr;			
	}
	if(debug) {
		echo "$username record".cr.print_r($result,true).cr; 
	}

	if (isset($ut)) {
		if ($modify || $added) {		
			$rt .= $user_stub.' '.$ut;
		}
	}
}


$mask = "%15.15s %4.4s \n";
if ($done || $update_users ) {
//echo $rt;
$rt .= sprintf($mask,'New Users',$done );
$rt .= sprintf($mask,'Modified Users',$update_users );
if ($uds == true) {
	$rt .= cr.'Warning '.$server.' needs updating & restarting'.cr;
	$rt .= update_server($server);
}
$rt .= cr.'Processed '.$server.cr;

return $rt;
}
$rt = "no changes on $server".cr;
if (debug ) {
	echo strlen($rt).cr;
	//echo "$rt";
}
return $rt;
}
function get_ip_detail($ip) {
	// return api data
	global $key;
	
	$cmd =  'https://api.ipdata.co/'.$ip.'?api-key='.$key;
	if (debug) {
		echo "getting ip data with $cmd".cr; 
	}
	$ip_data = json_decode(file_get_contents($cmd), true); //get the result
	 if (empty($ip_data['threat']['is_threat'])) {$ip_data['threat']['is_threat']=0;}
	 //print_r($ip_data); //debug code
	 return $ip_data;
}

function update_server($server){
	// if found stop the server and update
	//Your server needs to be restarted in order to receive the latest update.
	global $database, $update_done,$settings;
	$s = 'Server Update via Scanlog '.VERSION.cr;
	$sql = 'select * from server1 where host_name="'.$server.'"';
	$steamcmd = '/usr/games/steamcmd';
	$game = $database->get_row($sql);
	$stub =  $game['url'].':'.$game['bport'].'/ajaxv2.php?action=exescreen&server='.$game['host_name'].'&key='.md5($game['host']).'&cmd='; // used to start & stop
	if (in_array($game['install_dir'],$update_done)) {
				$s .= 'Update already done'.cr;
			    //$cmd = $stub.'r';
			    //$s .=  file_get_contents($cmd).cr; 	
				return $s;
			}
	$cmd = $stub.'q';
	$s .= file_get_contents($cmd).cr; // stopped server
	// need to check if this is a root install, if so use the 'c' module to elevate the privs  
	$exe = urlencode($steamcmd.' +login anonymous +force_install_dir '.$game['install_dir'].' +app_update '.$game['server_id'].' +quit');
	$cmd = $game['url'].':'.$game['bport'].'/ajaxv2.php?action=exe&cmd='.$exe.'&debug=true';
	$s .=file_get_contents($cmd);
	//echo 'updated server using '.$cmd.cr;
	//$cmd = $stub.'s';
	//$s .= file_get_contents($cmd).cr;
	//need to restart all that stem from this install dir
	$sql = "SELECT * FROM `server1` WHERE `game` like '".$game['game']."' and `install_dir` like '".$game['install_dir']."'";
		$restarts = $database->get_results($sql);
		foreach ($restarts as $restart) {
			// restart them all
			$cmd =  $game['url'].':'.$restart['bport'].'/ajaxv2.php?action=exescreen&server='.$restart['host_name'].'&key='.md5($restart['host']).'&cmd=r'; // used to restart
			$s .= file_get_contents($cmd).cr;
		}
		
	$update_done[] = $game['install_dir'];
	return $s;
}
function user_data($value) {
	//
	 $vx =preg_split('/"/', $value);
	 $vx = array_filter($vx);
	 $vx[0] = trim(str_replace('L ','',$vx[0]));
	 $vx[0] =  substr($vx[0], 0, -1);
	 $vx[0] = str_replace('-','',$vx[0]);
	 $s =preg_split('/<\d+>/', $vx[1]);
	 $s[1] = str_replace('<','',$s[1]);
	 $s[1] = str_replace('>','',$s[1]);
	 $vx[1] = Emoji::Encode($s[0]);
	 $vx[2] = $s[1];
	 $vx[3] = strtok( $vx[3], ':' );
	 $steam_id = new SteamID( $vx[2] );
	 $vx[] = $steam_id->ConvertToUInt64();
	 $return['time'] = $vx[0];
	 $return['name'] = $vx[1];
	 $return['id'] = $vx[2];
	 $return['id2'] = $steam_id->ConvertToUInt64();
	 $return['ip'] = $vx[3];
	 if(debug) {
	 echo 'Returning user data as'.cr.print_r($return,true).cr;
	}
	 return $return;
	 
}
?>
