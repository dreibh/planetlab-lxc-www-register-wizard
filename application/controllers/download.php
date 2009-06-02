<?php

require_once 'plc_login.php'; // Require login
require_once 'plc_session.php'; // Get session and API handles
global $plc, $api, $adm;

// Print header
require_once 'plc_drupal.php';

include 'plc_header.php';

// Common functions
require_once 'plc_functions.php';
include 'plc_objects.php';


class Download extends Controller {
	
	var $pcu_id = 0;
	function stage5_bootimage($pcu_id=0, $site_id=0, $node_id=0)
	{
		global $api, $plc;
		$this->load->helper(array('form', 'url'));
		$this->load->library('validation');
		
		$this->validation->set_error_delimiters('<span class="error">', '</span>');

		# TODO: if result is false, redirect to beginning.
		/*$result = $this->validation->run();*/
		/*print "RESULT: ".$result . "<br>";*/
		/*print $this->validation->error_string . "<br>";*/

		$data = array();
		$data['pcu_id'] = intval($pcu_id);
		$data['node_id'] = intval($node_id);
		$data['site_id'] = intval($site_id);
		$data['stage'] = 5;
		$this->load->view('header', $data);
		$this->load->view('debug', $data);
		$this->load->view('stage5_bootimage', $data);
		$this->load->view('footer', $data);
	}

	function stage6_download($pcu_id=0, $site_id=0, $node_id=0)
	{
		global $api, $plc;
		$this->load->helper(array('form', 'url'));
		$this->load->helper('download');
		$this->load->library('validation');
		
		$this->validation->set_error_delimiters('<span class="error">', '</span>');
		$person = new Person($plc->person);

		$fields['action']  = "Download Action";
		$this->validation->set_fields($fields);

		$rules['action']	= "required";
		$this->validation->set_rules($rules);

		# TODO: if result is false, redirect to beginning.
		$result = $this->validation->run();

		$data = array();
		$data['pcu_id'] = intval($pcu_id);
		$data['node_id'] = intval($node_id);
		$data['site_id'] = intval($site_id);
		$data['action'] = $this->validation->action;
		$data['stage'] = 6;
		$data['generic_iso_name'] = $this->get_bootcd_version();
		$data = $this->get_stage6_data($person, $data);
		print $this->validation->error_string . "<br>";
		$this->load->view('header', $data);
		$this->load->view('debug', $data);
		$this->load->view('stage6_download', $data);
		$this->load->view('footer', $data);
	}

	function getnode($node_id)
	{
		global $api, $plc;
		$plc_node_list = $api->GetNodes(array('node_id' => intval($node_id) ));
		return $plc_node_list[0];
	}

	function get_stage6_data($person, $data=NULL)
	{
		global $api, $plc;
		if ( $data == NULL ){
		    $data = array();
		}
		$data['api'] = $api;
		$data['has_primary'] = 1;
		$node_detail = $this->getnode($data['node_id']);
		$data['node_detail'] = $node_detail;
		$node_id = $data['node_id'];
		$action = $_REQUEST['action'];
		switch ($action) {

			case "download-generic-iso":
			case "download-generic-usb":
		   
				if ($action=="download-generic-iso") {
					$boot_action="generic-iso";
					$basename=PLC_NAME."-BootCD.iso";
				} else {
					$boot_action="generic-usb";
					$basename=PLC_NAME."-BootCD.usb";
				}

				$api->UpdateNode( $hostname, array( "boot_state" => 'disabled',
													"version" => $this->get_bootcd_version() ) );
				/* exits on success */
				$success = $this->deliver_bootmedium($node_id, $boot_action, $basename);
				if (! $success ) {
					$error="Unexpected error from deliver_bootmedium - probably wrong directory modes";
					print ("<div class='plc-error'> $error </div>\n");
					print ("<p><a href='/db/nodes/index.php?id=$node_id'>Back to node </a>\n");
					return ;
				}
				break;

			case "download-node-floppy-with-iso":
			case "download-node-floppy-with-usb":
			case "download-node-floppy":
			case "download-node-iso":
			case "download-node-usb":
			case "download-node-usb-partition":
		   
				$return= $api->GetNodes( array( $node_id ) );
				$node_detail= $return[0];

				// non-admin people need to be affiliated with the right site
				if( !in_array( 10, $person->data['role_ids'] ) ) {
					$node_site_id = $node_detail['site_id'];
					$in_site = in_array ($node_site_id,$person->data['site_ids']);
					if( ! $in_site) {
						$error= "Insufficient permission. You cannot create configuration files for this node.";
					}
				}

				$hostname= $node_detail['hostname'];
				$return= $api->GetInterfaces( array( "node_id" => $node_id ), NULL );
		   
				$can_gen_config= 1;
				$data['has_primary']= 0;
		   
				if( count($return) > 0 ) {
					foreach( $return as $node_network_detail ) {
						if( $node_network_detail['is_primary'] == true ) {
							$data['has_primary']= 1;
							break;
						}
					}
					$data['node_network_detail'] = $node_network_detail;
				}

				if( !$data['has_primary'] ) {
					$can_gen_config= 0;
				} else {
					if( $node_detail['hostname'] == "" ) {
						$can_gen_config= 0;
						$node_detail['hostname']= "<i>Missing</i>";
					}
			 
					$fields= array("method","ip");
					foreach( $fields as $field ) {
						if( $node_network_detail[$field] == "" ) {
							$can_gen_config= 0;
							$node_network_detail[$field]= "<i>Missing</i>";
						}
					}

					if( $node_network_detail['method'] == "static" ) {
						$fields= array("gateway","netmask","network","broadcast","dns1");
						foreach( $fields as $field ) {
							if( $node_network_detail[$field] == "" ) {
								$can_gen_config= 0;
								$node_network_detail[$field]= "<i>Missing</i>";
							}
						}
					}

					if( $node_network_detail['method'] != "static" && $node_network_detail['method'] != "dhcp" ) {
						$can_gen_config= 0;
						$node_network_detail['method']= "<i>Unknown method</i>";
					}
				}

				if( $can_gen_config && isset($_REQUEST['download']) ) {
					$download = $_REQUEST['download'];
					if (method_exists($api, "GetBootMedium"))
						$file_contents= $api->GetBootMedium($node_id, "node-floppy", "");
					else
						$file_contents= $api->GenerateNodeConfFile($node_id, true);
		
					switch ($action) {
						case 'download-node-floppy-with-iso':
						case 'download-node-floppy-with-usb':
						case 'download-node-floppy':
							$boot_action='node-floppy';
							$basename = "plnode.txt";
							break;
						case 'download-node-iso':
							$boot_action='node-iso';
							$basename = "$hostname.iso";
							break;
						case 'download-node-usb':
							$boot_action='node-usb';
							$basename = "$hostname.usb";
							break;
						case "download-node-usb-partition":
							$boot_action='node-usb-partition';
							$basename = "$hostname-partition.usb";
							break;
					}	 
					if ($action != 'download-node-floppy')
					{
						$api->UpdateNode( $hostname, array( "boot_state" => 'disabled',
										  "version" => $this->get_bootcd_version() ) );
					}
					/* exits on success */
					$success = $this->deliver_bootmedium($node_id, $boot_action, $basename);
					if (! $success ) {
						$error="Unexpected error from deliver_bootmedium - probably wrong directory modes";
						print ("<div class='plc-error'> $error </div>\n");
						print ("<p><a href='/db/nodes/index.php?id=$node_id'>Back to node </a>\n");
						return ;
					}
				}
				$action_labels = array (
				    'download-node-floppy' => 'textual node config (for floppy)' ,
				    'download-node-floppy-with-iso' => 'textual node config (for floppy)' ,
				    'download-node-floppy-with-usb' => 'textual node config (for floppy)' ,
					'download-node-iso' => 'ISO image',
					'download-node-usb-partition' => 'USB image',
					'download-node-usb' => 'USB image' );

				$format = $action_labels [ $action ] ;
				/*drupal_set_title("Download boot material for $hostname");*/
			break;
		 
			default:
				echo "Unknown action $action.";
				exit();
			break;
		}
		$data['format'] = $format;
		$data['can_gen_config'] = $can_gen_config;
		return $data;
	}
	function get_bootcd_version()
	{
		$BOOTCD="/usr/share/bootcd";
		$BOOTCDVERSION="$BOOTCD/build/version.txt";
		$version = trim(file_get_contents($BOOTCDVERSION));
		$bootcd_version = PLC_NAME . "-BootCD-".$version;
		return $bootcd_version;
	}

	function deliver_bootmedium($node_id, $action, $filename)
	{
		global $api;
		ini_set("memory_limit","300M");
		$options = array();
		switch ($action) {
			case "node-usb-partition":
				$action = "node-usb";
				$options[] = "partition";
				break;
		}	 
		$b64_data = $api->GetBootMedium($node_id,$action,"", $options);
		$error= $api->error();
		if ( empty($error) )
		{
			if ( $action == "node-floppy" )
			{
				# data comes back in plain text for node-floppy.
				$data = $b64_data;
			} else {
				$data = base64_decode($b64_data);
			}
			$size = strlen($data);
			/* exits on success */
			force_download($filename, $data);
		}
		return False;
	}

}
?>
