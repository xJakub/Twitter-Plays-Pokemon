<?php

function proccommand($data) {
	static $fb = null;
	static $ufb = null;
	static $cfb = null;
	static $tfb = null;
	static $nfb = null;
	static $hfb = null;
	static $userlist = Array();
	static $commandlist = Array();
	static $totaltime = null;
	static $starttime = null;
	static $lasttime = null;
	static $starttime2;
	static $commandscount = 0;
	
	static $deftime = Array('A'=>3,'B'=>3,'start'=>3,'select'=>3,'up'=>3,'down'=>3,'right'=>3,'left'=>3);
	global $commandqueue;
	
	if ($fb === null) {
		$commandqueue = Array();
		$fb = fopen("../lua/nextcommands.lua","a");
		file_put_contents("realtime.txt","");
		file_put_contents("realtime-commands.txt","");
		file_put_contents("realtime-hours.txt","");
		
		if (!file_exists("realtime-time.txt")) file_put_contents("realtime-time.txt","");
		if (!file_exists("realtime-count.txt")) file_put_contents("realtime-count.txt","0");
		$starttime = file_get_contents("realtime-time.txt")*1;
		$commandscount = file_get_contents("realtime-count.txt")*1;
		$starttime2 = $data['time'];
		
		$ufb = fopen("realtime.txt","r+");
		$cfb = fopen("realtime-commands.txt","r+");
		$tfb = fopen("realtime-time.txt","r+");
		$hfb = fopen("realtime-hours.txt","r+");
		$nfb = fopen("realtime-count.txt","r+");
		}
		
		$commandicon = Array();
		$commandicon['up'] = '↑';
		$commandicon['down'] = '↓';
		$commandicon['left'] = '←';
		$commandicon['right'] = '→';
		$commandicon['select'] = 'select';
		$commandicon['start'] = 'start';
		$commandicon['A'] = 'A';
		$commandicon['B'] = 'B';
		
	if ($data !== null) {
		$user = $data['username'];
		$text = $data['text'];
		$tweetid = $data['tweetid'];
		$time = $data['time'];
		
		#$rand = Array("B","A","down","left","right","up","down","left","right","up","down","left","right","up","select","start");
		
		
		if ($data['time']*1 != $lasttime) {
			$totaltime = $data['time']-$starttime2 + $starttime;
			
			fseek($tfb,0);
			fseek($hfb,0);
			ftruncate($tfb,0);
			ftruncate($hfb,0);
			fwrite($tfb,$totaltime);
			
			$totaltimestr = "";
			if ($totaltime >= 86400) $totaltimestr .= floor($totaltime / 86400)."d ";
			if ($totaltime >= 3600) $totaltimestr .= floor($totaltime%86400 / 3600)."h ";
			if ($totaltime >= 60) $totaltimestr .= floor($totaltime%3600 / 60)."m ";
			$totaltimestr .= ($totaltime%60)."s ";
			
			$lasttime = $data['time'];
			
			fwrite($hfb,$totaltimestr);
		}
		
		#$text = $rand[rand(0,count($rand))-1];
		
		$text = strtolower($text);
		#$text = "$text #twitterplayspokemon please";
		
		$command = false;
		
		if (preg_match_all("'\b(a|b|start|up|down|left|right|up|down|left|right)\b'"," $text ",$matches)) {
			
			foreach($matches[1] as $ci => $command) {
				#$command = strtolower($match[1]);
				if (strlen($command)==1) $command = strtoupper($command);
				$commandqueue[]=Array($ci,$command,$data);
			}
		}
	}
	else {
		$queuelength = count($commandqueue);
		$newcommands = Array();
		foreach($commandqueue as $i => $arr) {
			list($ci,$command,$data) = $arr;
			
			if ($ci > 0) {
				$arr[0]--;
				$newcommands[] = $arr;
			}
			else {
				$user = $data['username'];
				$text = $data['text'];
				$tweetid = $data['tweetid'];
				$time = $data['time'];
				
				echo "Writing $command...\n";
				$commandscount++;
				
				#$commline = 'joypad.set(1, {'.$command.'=1})';
				$commline = 'if (ktab["'.$command.'"]==0) then ktab["'.$command.'"]='.$deftime[$command].' end';
				$commline = str_pad($commline,46," ");
				$commline .= "-- |";
				
				if (count($userlist)>=16) {
					$userlist = array_slice($userlist,1);
					$commandlist = array_slice($commandlist,1);
				}
				$userlist[] = $user;
				
				$commandlist[] = $commandicon[$command];

				fseek($ufb,0);
				fseek($cfb,0);
				fseek($nfb,0);
				ftruncate($ufb,0);
				ftruncate($cfb,0);
				ftruncate($nfb,0);
				fwrite($ufb,implode("\n",$userlist));
				fwrite($cfb,implode("\n",$commandlist));
				fwrite($nfb,$commandscount);
				
				fwrite($fb, "\r\n".$commline."\r\n -- ".$user.' ('.time().' - '.$data['tweetid'].')'."\r\n");
				
			}
		}
		$commandqueue = $newcommands;
	}
	
	return "";
	
}
