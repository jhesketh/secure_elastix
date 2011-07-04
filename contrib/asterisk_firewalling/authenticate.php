<?php

$auth_token = "MYSECRETAUTH";

if ( !isset($_GET['auth']) || $_GET['auth'] != $auth_token ) {
    print "Auth token incorrect.";
    die();
    exit;
}

if ( isset($_GET['ip']) ) {
    $ip = $_GET['ip'];
}
else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

$commands = array();
$commands[] = 'sudo iptables -A INPUT -p tcp --dport 5038 -s ' . $ip . ' -j ACCEPT'; // astman

$commands[] = 'sudo iptables -A INPUT -p tcp --dport 5060 -s ' . $ip . ' -j ACCEPT'; // sip
$commands[] = 'sudo iptables -A INPUT -p udp --dport 5060 -s ' . $ip . ' -j ACCEPT'; // sip
$commands[] = 'sudo iptables -A INPUT -p udp --dport 5004 -s ' . $ip . ' -j ACCEPT'; // sip

$commands[] = 'sudo iptables -A INPUT -p udp --dport 4569 -s ' . $ip . ' -j ACCEPT'; // IAX2
$commands[] = 'sudo iptables -A INPUT -p udp --dport 5037 -s ' . $ip . ' -j ACCEPT'; // IAX

$commands[] = 'sudo iptables -A INPUT -p udp --dport 10000:20000 -s ' . $ip . ' -j ACCEPT'; // RTP

$commands[] = 'sudo iptables -A INPUT -p tcp --dport 3478 -s ' . $ip . ' -j ACCEPT'; // stun
$commands[] = 'sudo iptables -A INPUT -p udp --dport 3478 -s ' . $ip . ' -j ACCEPT'; // stun



//$commands[] = 'sudo iptables -A INPUT -p udp --dport 443 -s ' . $ip . ' -j ACCEPT';
$check_tables =  explode("\n", exec_output("sudo iptables --line-number -nvL | grep -c " . $ip));

if ( $check_tables[1] >= count($commands) ) {
    print "This ip ($ip) is already authenticated";
}
else {
    foreach ( $commands as $command ) {
        print exec_output($command) . "\n<br /><br />\n";
    }
}
function exec_output($command) {
    $output = array($command);
    exec($command.' 2>&1', $output);
    return implode("\n", $output);
}

?>
