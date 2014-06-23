<?php
// Make sure we have a payload, stop if we do not.
if( ! isset( $_POST['payload'] ) )
	die( '<h1>No payload present</h1><p>A BitBucket POST payload is required to deploy from this script.</p>' );

/**
 * Tell the script this is an active end point.
 */
define( 'ACTIVE_DEPLOY_ENDPOINT', true );

require_once 'deploy-config.php';

/**
 * Deploys BitBucket git repos
 */
class BitBucket_Deploy extends Deploy {
	/**
	 * Decodes and validates the data from bitbucket and calls the 
	 * deploy constructor to deploy the new code.
	 */
	function __construct( $name, $repo ) {
		parent::__construct( $name, $repo );
	}
}
// Start the deploy attempt.
$payload = json_decode( stripslashes( $_POST['payload'] ), true );

$reposBitbucket = null;

foreach ( $repos as $name => $repo )
	$reposBitbucket = Deploy::register_repo( $name, $repo );

foreach ($reposBitbucket as $nickname => $repo) {
	$shouldPull = false;
	$currcommit = null;
	foreach($payload['commits'] as $commit) {
		$shouldPull = $repo['repo_name'] == $payload['repository']['name'] && $repo['branch'] == $commit['branch'];
		if($shouldPull) {
			$currcommit = $commit;
			break;
		}
	}
	if($shouldPull) {
		$data = $repo;
		$data['commit'] = $currcommit['node'];
		new BitBucket_Deploy( $nickname, $data );
	}
}