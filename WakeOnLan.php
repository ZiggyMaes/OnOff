<?php 
flush();
$port = 9;
 
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
 
$result = null;

if($_GET["wake_machine"] != "") 
    $result = WakeOnLan("192.168.2.255", $_GET["wake_machine"], $port);
 
if($result != null) 
   echo "<HR>WOL for ".$_GET["wake_machine"]." was successful!<BR>\n"; 

?>

<title>Wake On LAN</title>

<s:select label="WakeOnLan"
    headerKey="-1" headerValue="Select Machine"
    list="machines"
    name="WakeOnLan" />
 
<form name="WakeOnLan" method="GET" action="index.php">
<td class="WOL">
   <label for="WakeOnLan" class="label">
       Select a machine to wake:<br>
   </label>
</td>
<td>
<select name="wake_machine" id="WakeOnLan">
    <option value="-1">Select Machine</option>
    <option value="BC:AE:C5:E3:BF:FA">HOST</option>
    <option value="00:17:A4:99:E8:81">fs1</option>
    <option value="00:30:48:30:53:78">fs</option>
<input type="submit" value="Submit" />
</form>
</select>
</td>