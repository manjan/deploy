<?php

require_once('aws-autoloader.php');
require_once('config.php');

use Aws\AutoScaling\AutoScalingClient;
use Aws\Ec2\Ec2Client;

# Downloads the latest from remote without trying to merge or rebase anything
# Resets the master branch to what you just fetched
try{
if (isset($_POST['deploy']) and $_POST['deploy'] == 1)
	exec('cd ' . $doc_root . ' && git fetch --all && git reset --hard origin/master');

elseif (isset($argc) and $argc == 2 and $argv[1] == 'update')
	exec('cd ' . $doc_root . ' && git fetch --all && git reset --hard origin/master');

elseif (isset($_REQUEST['payload'])) {
	exec('cd /var/www/html/Deploy && git fetch --all && git reset --hard origin/master');
	# Create a autoscaling client object
	$as_client = AutoScalingClient::factory(array(
	    'key'    => $access_key,
	    'secret' => $secret_key,
	    'region' => $region
	));
	# Create a ec2 client object
	$ec2_client = Ec2Client::factory(array(
	    'key'    => $access_key,
	    'secret' => $secret_key,
	    'region' => $region
	));
	# EC2 instance id
	$ec2_id = array();

	# This includes all Amazon EC2 instances that are members of the group
	$result = $as_client->describeAutoScalingGroups(array(
		'AutoScalingGroupNames'	=> array($as_group)
	));

	$result = $result['AutoScalingGroups'];
	foreach($result as $value) {
		$instance = $value['Instances'];
		# Append ec2 instance id
		foreach($instance as $id)
			array_push($ec2_id, $id['InstanceId']);
	}

	# This includes all information about instance id in $ec2_id which are currently running
	$result = $ec2_client->describeInstances(array(
		'InstanceIds'	=> $ec2_id,
		'Filters'	=> array(
			array(
				'Name'	=> 'instance-state-name',
				'Values'	=> array(
					'running')
				)
			)
	));

	$result = $result['Reservations'];
	foreach($result as $value) {
		$instance = $value['Instances'];
		foreach($instance as $public_dns) {
			# Create the post url with public dns for all instances returned by Ec2client
			$url = 'http://' . $public_dns['PublicDnsName'] . '/sync/index.php';
			# Create the query string
			$query_str = 'deploy=1';
			# Initialize a cURL session
			$ch = curl_init();
			# The URL to fetch
			curl_setopt($ch, CURLOPT_URL, $url);
			# Request method: HTTP POST with 2 fields
			curl_setopt($ch, CURLOPT_POST, 1);
			# The full data to post in a HTTP POST operation
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query_str);
			# Perform a cURL session
			$result = curl_exec($ch);
			curl_close($ch);
		}
	}
}else{
		exec('cd /var/www/html/Deploy && git fetch --all && git reset --hard origin/master');
}
}
catch(Exception $e){
# Handle invalid HTTP request
	echo $e;
	die("Go and learn github (http://try.github.io) before you mess-up with me :D");
}