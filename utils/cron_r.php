#!/usr/bin/php -d memory_limit=2048M
<?php
/*
 * cron_r.php
 * 
 * Copyright 2021 Jim Richardson <jim@noideersoftware.co.uk>
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
 * restart servers at a given time
 *  
 */
if (!defined('DOC_ROOT')) {
    	define('DOC_ROOT', realpath(dirname(__FILE__) . '/../'));
    }

 define('cr',PHP_EOL);
 define('plus','%2B');
 define('space','%20');  

require_once DOC_ROOT.'/includes/master.inc.php';
include  DOC_ROOT.'/functions.php';
require  DOC_ROOT.'/xpaw/SourceQuery/bootstrap.php';
use xPaw\SourceQuery\SourceQuery;
define( 'SQ_ENGINE',      SourceQuery::SOURCE );
$done = array();
		$Query = new SourceQuery( ); 
$sql = 'select * from servers where running=1';
//$sql = 'SELECT servers.* , base_servers.url, base_servers.port as bport, base_servers.fname,base_servers.ip as ipaddr, base_servers.base_ip,base_servers.password FROM `servers` left join `base_servers` on servers.host = base_servers.ip where servers.id <>"" and servers.running="1" order by servers.host_name';
$sql = "SELECT * FROM `server1` WHERE running=1 ORDER BY`host_name` ASC";
$games = $database->get_results($sql);
foreach ($games as $game) {
		if (ping($game['host'], $game['port'], 1)) {
		$Query->Connect( $game['host'], $game['port'], 1, SQ_ENGINE );
		$info = $Query->GetInfo();
		$Query->Disconnect( );
		if ($info['Players'] == 0 ) {
			$game['restart'] = $game['url'].':'.$game['bport'].'/ajaxv2.php?action=exescreen&server='.$game['host_name'].'&key='.md5($game['host']).'&cmd=';
			$restart[] = $game;
		}

		elseif ($info['Bots'] == $info['Players']) {
			$game['restart'] = $game['url'].':'.$game['bport'].'/ajaxv2.php?action=exescreen&server='.$game['host_name'].'&key='.md5($game['host']).'&cmd=';
			$restart[] = $game;
		}
		else  {
			$game['restart'] = $game['url'].':'.$game['bport'].'/ajax.php?action=exescreen&server='.$game['host_name'].'&key='.md5($game['host']).'&cmd=';
			$check[] = $game; 
		}
	}
	else { continue;}
	
}
	echo 'Restarting '.count($restart).'/'.count($games).' server(s)'.cr;
	foreach ($restart as $game) {
			echo file_get_contents($game['restart'].'q').cr; // stop server
			//print_r($game);
			$exe = urlencode ('./scanlog.php '.$game['host_name'].' '.$game['location'].'/log/console/'.$game['host_name'].'-console.log');
			$cmd = $game['url'].':'.$game['bport'].'/ajaxv2.php?action=exe&cmd='.$exe.'&debug=true';
			echo file_get_contents($cmd); // scan log
			// check updates
			if (in_array($game['server_id'],$done)) {
				echo 'update already checked'.cr;
			}
			else{
				$steamcmd = trim(shell_exec('which steamcmd')); // is steamcmd in the path ?
				if(empty($steamcmd)) {
					$steamcmd = './steamcmd';
				}
				chdir(dirname($game['install_dir'])); // move to install dir root steamcmd should be there
				echo 'moved to '.getcwd ( ).cr;
				$exe = $steamcmd.' +login anonymous +force_install_dir '.$game['install_dir'].' +app_update '.$game['server_id'].' +quit';
				echo 'will execute '.$exe.cr;
				$done[]=$game['server_id']; // use this to test if update on core files has been done
			}
			// log prune
			$exe = 'tmpreaper --mtime 1d '.$game['location'].'/log/console/';
			echo 'Prune command  '.$exe.cr;
			$exe = 'tmpreaper --mtime 1d '.$game['location'].'/'.$game['game'].'/logs/';
			echo 'Prune here also '.$exe.cr;
			sleep(1);
			echo file_get_contents($game['restart'].'s').cr; // start server
			}
	     echo print_r($done,true),cr; //test array
	
	
	if (isset($check)) { 
		echo 'Defered '.count($check).'/'.count($games).' server(s)'.cr;
	foreach ($check as $restart) {
		echo  $restart['server_name'].cr;
	}
}

?>
