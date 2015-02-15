<?php

function WakeOnLan($addr, $mac, $socket_number) 
{
   $addr_byte = explode(':', $mac);
   $hw_addr   = '';
   
   for($a=0; $a <6; $a++) 
      $hw_addr .= chr(hexdec($addr_byte[$a]));
      
   $msg = chr(255).chr(255).chr(255).chr(255).chr(255).chr(255);
   
   for($a = 1; $a <= 16; $a++) 
      $msg .= $hw_addr;
      
   $s = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
   
   if($s == false) 
   {
      echo "Can't create socket!<BR>\n";
      echo "Error: '".socket_last_error($s)."' - " . socket_strerror(socket_last_error($s));
      return FALSE;
   }
   else 
   {
      $opt_ret = socket_set_option($s, 1, 6, TRUE);
      
      if($opt_ret < 0) 
      {
         echo "setsockopt() failed, error: " . strerror($opt_ret) . "<BR>\n";
         return FALSE;
      }
      
      if(socket_sendto($s, $msg, strlen($msg), 0, $addr, $socket_number)) 
      {
         $content = bin2hex($msg);
         echo "Magic Packet Sent!<BR>\n";
         echo "Data: <textarea readonly rows=\"1\" name=\"content\" cols=\"".strlen($content)."\">".$content."</textarea><BR>\n";
         echo "Port: ".$socket_number."<br>\n";
         echo "MAC: ".$_GET['wake_machine']."<BR>\n";
         socket_close($s);
         return TRUE;
      }
      else 
      {
         echo "Magic Packet failed to send!<BR>";
         return FALSE;
      } 
   }
}

function ping($ip, $port)
{
	$timeout = 1;
	$socket = @fsockopen($ip, $port, $errno, $errstr, $timeout );
	$online = ( $socket !== false );
	if($online)
	{
		return "on";
	}
	else
	{
		return "off";
	}
}

$servers = array
(
	$array = [
		"id" => "0",
		"type" => "N",
		"name" => "Callant NAS",
		"ip" => "192.168.5.5",
		"publicip" => "78.22.186.5",
		"port" => "80",
		"mac" => "00-11-32-2E-86-FD",
		"wolport" => "9",
		"broadcastaddress" => "192.168.5.255"
	],
	$array = [
		"id" => "1",
		"type" => "S",
		"name" => "Plex Media Server",
		"ip" => "192.168.5.6",
		"publicip" => "78.22.186.5",
		"port" => "81",
		"mac" => "00:15:C5:3B:FF:23",
		"wolport" => "9",
		"broadcastaddress" => "192.168.5.255"
	],
	$array = [
		"id" => "2",
		"type" => "D",
		"name" => "Bart's Desktop",
		"ip" => "192.168.5.150",
		"mac" => ""
	],
);

if(isset($_POST['send']) && isset($_POST['id']))
{
	$server = $servers[$_POST['id']];
	$send = $_POST['send'];

	if($send == "Power On")
	{
		WakeOnLan($server["broadcastaddress"], $server["mac"], $server["wolport"]);
	}
	else if($send == "Power Off")
	{
		header('location: http://' . $server["publicip"] . ':' . $server["port"] . '/shutdown.php');
	}
	else
	{
		print_r("Invalid action!");
	}
}

?>
<html>
	<head>
		<title>OnOff</title>
		
		<style>
			form, input, div
			{
				margin: 0;
				padding: 0;
			}
			body
			{
				background-color: #f7f7f7;
				margin: 10px;
				padding: 0;
			}
			body > div
			{
				font-family: sans-serif;
				font-size: 15px;
				background-color: #ccc;
				padding: 10px;
				margin-bottom: 10px;
			}
			.type
			{
				color: white;
				text-align: center;
				width: 20px;
				padding: 5px;
				margin: 0px 5px 0px 0px;
				display: inline-block;
			}
			.on
			{
				background-color: green;
			}	
			.off
			{
				background-color: red;
			}
			.name
			{
				display: inline-block;
				width: 200px;
			}
			form
			{
				display: inline-block;
				color: black;
			}
			input[type=submit]
			{
				padding: 5px;
				border: none;
				border-radius: 0;	
				background-color: #fcfcfc;
			}
		</style>
	</head>
	
	<body>
		<?php
		foreach ($servers as $server)
		{
			?>
			<div>
				<div class="name"><div class="type <?php echo ping($server["ip"], $server["port"]); ?>"><?php echo $server["type"]; ?></div><?php echo $server["name"]; ?></div>
				<form method="post">
					<input type="hidden" name="id" value="<?php echo $server["id"]; ?>" />
					<input type="submit" name="send" value="Power On" />
					<input type="submit" name="send" value="Power Off" />
				</form>
			</div>
			<?php
		}
		?>
	</body>
</html>