<?php

require_once 'plc_login.php'; // Require login
require_once 'plc_session.php'; // Get session and API handles
global $plc, $api, $adm;

// Print header
require_once 'plc_drupal.php';

include 'plc_header.php';

// Common functions
require_once 'plc_functions.php';
//require_once 'plc_sorts.php';
include 'plc_objects.php';


class Register extends Controller {
	
	var $pcu_id = 0;
	function index()
	{
		$this->load->helper(array('form', 'url'));
		$data=array();
		$data['stage'] = 0;
		$this->load->view('header', $data);
		$this->load->view('debug', $data);
		$this->load->view('stage0', $data);
		$this->load->view('footer', $data);
	}
	function stage1_addpcu()
	{
		global $api, $plc;
		$this->load->helper(array('form', 'url'));
		$this->load->library('validation');
		
		$this->validation->set_error_delimiters('<span class="error">', '</span>');

		$rules['model']		= "trim|required";
		$rules['hostname']	= "trim|required";
		$rules['ip'] 		= "trim|required|valid_ip";
		$rules['username']	= "trim";
		$rules['password']	= "trim|required";
		$rules['notes'] 	= "trim";
		$this->validation->set_rules($rules);

		$fields['model']	= "Model";
		$fields['hostname']	= "Hostname";
		$fields['ip'] 		= "IP Address";
		$fields['username']	= "Username";
		$fields['password']	= "Password";
		$this->validation->set_fields($fields);

		$person = new Person($plc->person);
		$data = array();
		if ($this->validation->run() == TRUE)
		{
		    	if ($this->validation->model != "none-selected" )
			{
				/* b/c the submit is valid, it doesn't matter if pcu_register is set */
				$this->pcu_id = $this->add_pcu($data);
			}
		}
		$data = $this->get_stage1_data($person, $data);
		$data['stage'] = 1;
		$this->load->view('header', $data);
		$this->load->view('debug', $data);
		$this->load->view('stage1_pcu_choose', $data);
		$this->load->view('footer', $data);
	}

	function add_pcu(&$data)
	{
		global $api, $plc;
		/* add pcu, get pcu info */
		$site_id = intval( $_REQUEST['site_id'] );
		$fields= array( 'protocol'=>	'',
				'model'=>	$_REQUEST['model'], 
				'hostname'=>	$this->validation->hostname,
				'ip'=>		$this->validation->ip,
				'username'=>	$this->validation->username, 
				'password'=>	$this->validation->password, 
				'notes'=>	$_REQUEST['notes'], );
		$pcu_id= $api->AddPCU( $site_id, $fields );

		if( $pcu_id == 0 ) {
			$data['error'] = $api->error();
			print $data['error'];
		}
		$data['pcu_id'] = $pcu_id;
		
		return $pcu_id;
	}

	function get_stage1_data($person, $data=NULL)
	{
		global $api, $plc;
		if ( $data == NULL ){
		    $data = array();
		}
		$data['default_site_list'] = $person->getSites();
		$data['pcu_types'] = $api->GetPCUTypes(NULL, array('model', 'name'));
		$data['pcu_list'] = $this->getpculist($person);
		$data['site_list'] = $this->getsitelist($person);
		return $data;
	}

	function getpculist($person)
	{
		global $api, $plc;
		$plc_pcu_list = $api->GetPCUs(array('site_id' => $person->getSites()));
		return PlcObject::constructList('PCU', $plc_pcu_list);
	}

	function getsitelist($person)
	{
		global $api, $plc;
		// get sites depending on role and sites associated.
		if( $person->isAdmin() ) {
			$site_info= $api->GetSites(array('peer_id' => NULL,'-SORT'=>'name'), 
						   array( "name", "site_id", "login_base" ) );
		} else {
			$site_info= $api->GetSites( $person->getSites(), array( "name", "site_id", "login_base" ) );
		}
		//sort_sites( $site_info );
		return $site_info;
	}

	var $run_update = True;
	var $disp_errors = True;
	function stage2_confirmpcu()
	{
		global $api, $plc;
		$this->load->helper(array('form', 'url'));
		$this->load->library('validation');
		
		$this->validation->set_error_delimiters('<span class="error">', '</span>');
		$person = new Person($plc->person);

		if ( isset($_REQUEST['pcu_choose']) ) {
			$rules['pcu_id']  = "required";
			$this->validation->set_rules($rules);
			$fields['pcu_id']  = "PCU id";
			$this->validation->set_fields($fields);

			$result = $this->validation->run();
			/* I don't know, shouldn't we redirect to the first stage in this case? */
			$this->run_update = False;
			$this->disp_errors = False;
			print $this->validation->error_string . "<br>";

		} else {
			# Information update

			$rules['pcu_id']  = "required";
			$rules['hostname']  = "trim|required";
			$rules['ip'] 		= "trim|required|valid_ip";
			$rules['username']  = "trim";
			$rules['password']  = "trim|required";
			$rules['notes'] 	= "trim";
			$this->validation->set_rules($rules);

			$fields['pcu_id']  = "PCU id";
			$fields['hostname']  = "Hostname";
			$fields['ip'] 		= "IP Address";
			$fields['username']  = "Username";
			$fields['password']  = "Password";
			$this->validation->set_fields($fields);

			if ( $this->validation->run() == FALSE )
			{
				print $this->validation->error_string . "<br>";
				$this->run_update = False;
			} 
		}
		$data = $this->get_stage2_data($person);
		$data['stage'] = 2;
		$this->load->view('header', $data);
		$this->load->view('debug', $data);
		$this->load->view('stage2_pcu_confirm', $data);
		$this->load->view('footer', $data);

	}

	function update_pcu($data)
	{
		global $api, $plc;
		/* add pcu, get pcu info */
		$pcu_id = intval($this->validation->pcu_id);
		$fields= array( 'protocol'=>	'',
				'model'=>	$_REQUEST['model'], 
				'hostname'=>	$this->validation->hostname,
				'ip'=>		$this->validation->ip,
				'username'=>	$this->validation->username, 
				'password'=>	$this->validation->password, 
				'notes'=>	$_REQUEST['notes'], );
		$ret = $api->UpdatePCU( $pcu_id, $fields );

		if( $ret != 1 ) {
			$data['error'] = $api->error();
			print $data['error'];
		}
		
		return $pcu_id;
	}

	function get_stage2_data($person)
	{
		global $api, $plc;

		$data = array();
		if ( $this->run_update && isset($_REQUEST['pcu_update']) ) 
		{
			$this->update_pcu($data);
		} 

		if ( isset($_REQUEST['pcu_id']) ) 
		{
			$pcu_id = intval($_REQUEST['pcu_id']);
			$pcu_data = $api->GetPCUs(array('pcu_id'=>$pcu_id));
			$pcu = new PCU($pcu_data[0]);
			$data['pcu'] = $pcu;
			$data['pcu_id'] = $pcu_id;
			$data['site_id'] = $pcu_data[0]['site_id'];
		} 

		$data['default_site_list'] = $person->getSites();
		$data['pcu_types'] = $api->GetPCUTypes(NULL, array('model', 'name'));
		$data['pcu_site'] = $api->GetSites( $pcu_data[0]['site_id'], array( "name", "site_id", "login_base" ) );
		return $data;
	}

	var $node_id = 0;
	function stage3_addnode($pcu_id, $site_id)
	{
		global $api, $plc;
		$this->load->helper(array('form', 'url'));
		$this->load->library('validation');
		
		$this->validation->set_error_delimiters('<span class="error">', '</span>');
		$person = new Person($plc->person);
		$data = array();
		$data['pcu_id'] = intval($pcu_id);
		$data['site_id'] = intval($site_id);

		if ( isset($_REQUEST['pcu_proceed']) ) {
			$rules['hostname']	= "";
			$rules['node_type']     = 'regular';
			$rules['model']  	= "";
			$rules['method']  	= "dhcp";
			$rules['ip'] 		= "";
			$rules['netmask']	= "";
			$rules['network']	= "";
			$rules['gateway']	= "";
			$rules['broadcast']	= "";
			$rules['dns1']		= "";
			$rules['dns2']		= "";
			$this->validation->set_rules($rules);
			$fields['hostname']	= "Hostname";
			$fields['node_type']	= "Node Type";
			$fields['model']	= "Model";
			$fields['method']	= "Method";
			$fields['ip']		= "IP Address";
			$fields['netmask']	= "Netmask Address";
			$fields['network']	= "Network Address";
			$fields['gateway']	= "Gateway Address";
			$fields['broadcast']	= "Broadcast Address";
			$fields['dns1'] 	= "Primary DNS Address";
			$fields['dns2'] 	= "Secondary DNS Address";
			$this->validation->set_fields($fields);

			$result = $this->validation->run();
			/*print "RESULT: ".$result . "<br>";*/
			# TODO: if result is false, redirect to beginning.
			$this->disp_errors = False;
			print $this->validation->error_string . "<br>";
		} else {
			$rules['hostname']	= "trim|required";
			$rules['node_type']	= "required";
			$rules['model']  	= "trim|required";
			$rules['method']  	= "required";
			$rules['ip'] 		= "trim|required|valid_ip";
			if ( isset ($_REQUEST['method']) && $_REQUEST['method'] == 'static' )
			{
				$rules['netmask']  = "trim|valid_ip";
				$rules['network']  = "trim|valid_ip";
				$rules['gateway']  = "trim|valid_ip";
				$rules['broadcast']  = "trim|valid_ip";
				$rules['dns1']  = "trim|valid_ip";
				$rules['dns2']  = "trim|valid_ip";
			} else {
				$rules['netmask']  = "";
				$rules['network']  = "";
				$rules['gateway']  = "";
				$rules['broadcast']  = "";
				$rules['dns1']  = "";
				$rules['dns2']  = "";
			}
			$this->validation->set_rules($rules);

			$fields['hostname']	= "Hostname";
			$fields['node_type']	= "Node Type";
			$fields['model']	= "Model";
			$fields['method']	= "Method";
			$fields['ip']		= "IP Address";
			$fields['netmask']	= "Netmask Address";
			$fields['network']	= "Network Address";
			$fields['gateway']	= "Gateway Address";
			$fields['broadcast']	= "Broadcast Address";
			$fields['dns1'] 	= "Primary DNS Address";
			$fields['dns2'] 	= "Secondary DNS Address";
			$this->validation->set_fields($fields);

			if ($this->validation->run() == TRUE)
			{
				/* b/c the submit is valid, all values are minimally consistent. */
				$this->node_id = $this->add_node($data);
			}
		}
		$data = $this->get_stage3_data($person, $data);
		$data['stage'] = 3;
		$this->load->view('header', $data);
		$this->load->view('debug', $data);
		$this->load->view('stage3_node_choose', $data);
		$this->load->view('footer', $data);
	}

	function add_node(&$data)
	{
		global $api, $plc;
		$hostname = trim($_REQUEST['hostname']);
		$node_type = trim($_REQUEST['node_type']);
		$model = trim($_REQUEST['model']);
		$method = trim($_REQUEST['method']);
		$ip = trim($_REQUEST['ip']);
		if ( $method == 'static' )
		{
			$netmask = trim($_REQUEST['netmask']);
			$network = trim($_REQUEST['network']);
			$gateway = trim($_REQUEST['gateway']);
			$broadcast = trim($_REQUEST['broadcast']);
			$dns1 = trim($_REQUEST['dns1']);
			$dns2 = trim($_REQUEST['dns2']);
		}

		// used to generate error strings for static fields only
		$static_fields= array();
		$static_fields['netmask']= "Netmask address";
		$static_fields['network']= "Network address";
		$static_fields['gateway']= "Gateway address";
		$static_fields['broadcast']= "Broadcast address";
		$static_fields['dns1']= "Primary DNS address";
		
		if( $method == 'static' )
		{
			if( !is_valid_network_addr($network,$netmask) )
			{
				$errors[] = "The network address does not coorespond to the netmask";
			}
		}

		if( !isset($errors) || count($errors) == 0 ) {
			// add new node and its network
			$optional_vals= array( 'hostname'=>$hostname, 'node_type'=>$node_type, 'model'=>$model );

			$site_id= $data['site_id'];
			// Try to get node in case this is from an error:
			$nodes = $api->GetNodes($optional_vals);
			if ( count($nodes) > 0 ) {
				$node_id= $nodes[0]['node_id'];
			} else {
				$node_id= $api->AddNode( intval( $site_id ), $optional_vals );
				if( $node_id <= 0 ) {
					$data['error'] = $api->error();
					print $data['error'];
				}
			}

			// now, try to add the network.
			$optional_vals= array();
			$optional_vals['is_primary']= true;
			$optional_vals['ip']= $ip;
			$optional_vals['type']= 'ipv4';
			$optional_vals['method']= $method;
			
			if( $method == 'static' )
			{
				$optional_vals['gateway']= $gateway;
				$optional_vals['network']= $network;
				$optional_vals['broadcast']= $broadcast;
				$optional_vals['netmask']= $netmask;
				$optional_vals['dns1']= $dns1;
				if (!empty($dns2)) {
					$optional_vals['dns2']= $dns2;
				}
			}

			$interface_id= $api->AddInterface( $node_id, $optional_vals);
			if( $interface_id <= 0 ) {
				$data['error'] = $api->error();
				print $data['error'];
			}

			$pcus = $api->GetPCUs(array('pcu_id' => $data['pcu_id']));
			if ( count($pcus) > 0 )
			{
				$pcu = $pcus[0];
				# if $node_id in $pcu['node_ids']
				if ( ! in_array( $node_id , $pcu['node_ids'] ) )
				{
				    $success = $api->AddNodeToPCU( $node_id, $data['pcu_id'], 1);
				    if( !isset($success) || $success <= 0 ) {
					    $data['error'] = $api->error();
					    print $data['error'];
				    }
				   
				}
			}
			$data['interface_id'] = $interface_id;
			$data['node_id'] = $node_id;
			if ( isset($errors) ) { $data['errors'] = $errors; }
			return $node_id;

		} else {
		    $data['error'] = $errors[0];
			return 0;
		}
		
	}

	function get_stage3_data($person, $data=NULL)
	{
		global $api, $plc;
		if ( $data == NULL ){
		    $data = array();
		}
		$data['default_site_list'] = array($data['site_id']);
		$data['node_list'] = $this->getnodelist($data['site_id']);
		$data['site'] = $this->getsite($data['site_id']);
		return $data;
	}

	function getnodelist($site_id)
	{
		global $api, $plc;
		$plc_node_list = $api->GetNodes(array('site_id' => intval($site_id) ));
		$ret = array();
		foreach ($plc_node_list as $plc_node)
		{
			$ret[] = new Node($plc_node, True);
		}
		return $ret;
	}

	function getsite($site_id)
	{
		global $api, $plc;
		$site_info = $api->GetSites($site_id, array( "name", "site_id", "login_base" ) );
		return $site_info[0];
	}

	function stage45_mappcu($pcu_id=0, $site_id=0, $node_id=0)
	{
		global $api, $plc;
		$this->load->helper(array('form', 'url'));
		$this->load->library('validation');
		
		$this->validation->set_error_delimiters('<span class="error">', '</span>');

		$fields['port_number']  = "Port Number";
		$this->validation->set_fields($fields);

		$data = array();
		$data['pcu_id'] = intval($pcu_id);
		$data['site_id'] = intval($site_id);
		$data['node_id'] = intval($node_id);

		if ( isset($_REQUEST['node_confirm']) ) {
			/* skip the rules, since we're just displaying the page. */
			$rules['port_number']		= "";
			$this->validation->set_rules($rules);

		} else {
			/* setup rules, to validate the form data. */
			$rules['port_number']		= "trim|required|intval";
			$this->validation->set_rules($rules);

			$result = $this->validation->run();
			if ( ! $result ) {
				print "ERROR";
			} else {
				$port = $this->validation->port_number;
				/* if we do not delete the node from the PCU first, a fault is raised */
				$ret = $api->DeleteNodeFromPCU($data['node_id'], $data['pcu_id']);
				$ret = $api->AddNodeToPCU($data['node_id'], $data['pcu_id'], $port);
				if ( $ret != 1 )
				{
					$data['error'] = $api->error();
					print $data['error'];
				}
			}
			$this->disp_errors = False;
			print $this->validation->error_string . "<br>";
		}

		$data['node'] = $this->getnode($data['node_id']);
		if ( sizeof($data['node']->pcu_ids) == 0)
		{
			$data['pcu_assigned'] = False;
			$data['pcu_port'] = -1;
		} else {
			$data['pcu_assigned'] = True;
			$api_pcus = $api->GetPCUs($data['node']->pcu_ids);
			$pcu = $api_pcus[0];
			# NOTE: find index of node id, then pull out that index of
			$index = array_search($data['node_id'], $pcu['node_ids']);
			$data['pcu_port'] = $pcu['ports'][$index];
		}

		$data['stage'] = 4.5;
		#$data = $this->get_stage4_data($person, $data);
		$this->load->view('header', $data);
		$this->load->view('debug', $data);
		$this->load->view('stage45_pcuport', $data);
		$this->load->view('footer', $data);
	}

	function stage4_confirmnode($pcu_id=0, $site_id=0)
	{
		global $api, $plc;
		$this->load->helper(array('form', 'url'));
		$this->load->library('validation');
		
		$this->validation->set_error_delimiters('<span class="error">', '</span>');
		$person = new Person($plc->person);

		$fields['hostname']	= "Hostname";
		$fields['node_type']	= "Node Type";
		$fields['model']	= "Model";
		$fields['method']	= "Method";
		$fields['ip']		= "IP Address";
		$fields['netmask']	= "Netmask Address";
		$fields['network']	= "Network Address";
		$fields['gateway']	= "Gateway Address";
		$fields['broadcast']	= "Broadcast Address";
		$fields['dns1'] 	= "Primary DNS Address";
		$fields['dns2'] 	= "Secondary DNS Address";
		$fields['node_id']	= "NODE id";
		$this->validation->set_fields($fields);

		$data = array();
		$data['pcu_id'] = intval($pcu_id);
		$data['site_id'] = intval($site_id);

		if ( isset($_REQUEST['node_choose']) ) {
			$rules['node_id']	= "required|intval";
			$rules['hostname']	= "";
			$rules['node_type']	= "regular";
			$rules['model']		= "";
			$rules['method']	= "dhcp";
			$rules['ip']		= "";
			$rules['netmask']	= "";
			$rules['network']	= "";
			$rules['gateway']	= "";
			$rules['broadcast']	= "";
			$rules['dns1']		= "";
			$rules['dns2']		= "";
			$this->validation->set_rules($rules);

			$result = $this->validation->run();
			/*print "RESULT: ".$result . "<br>";*/
			# TODO: if result is false, redirect to beginning.
			$this->disp_errors = False;
			print $this->validation->error_string . "<br>";
		} else {
			$rules['hostname']	= "trim|required";
			$rules['node_type']	= 'required';
			$rules['model']  	= "trim|required";
			$rules['method']  	= "required";
			$rules['ip'] 		= "trim|required|valid_ip";
			if ( isset ($_REQUEST['method']) && $_REQUEST['method'] == 'static' ) {
				$rules['netmask']  = "trim|valid_ip";
				$rules['network']  = "trim|valid_ip";
				$rules['gateway']  = "trim|valid_ip";
				$rules['broadcast']  = "trim|valid_ip";
				$rules['dns1']  = "trim|valid_ip";
				$rules['dns2']  = "trim";
			} else {
				# NOTE: There are no conditions that must be met for these fields.
				$rules['netmask']  = "";
				$rules['network']  = "";
				$rules['gateway']  = "";
				$rules['broadcast']  = "";
				$rules['dns1']  = "";
				$rules['dns2']  = "";
			}
			$rules['node_id']  = "required|intval";
			$this->validation->set_rules($rules);

			if ($this->validation->run() == TRUE)
			{
				/* b/c the submit is valid, all values are minimally consistent. */
				$this->node_id = $this->update_node($data);
			}
		}
		$data['node_id'] = intval($this->validation->node_id);
		$data['stage'] = 4;
		if ( $this->checknodeid($data['node_id']) ) {
			$data = $this->get_stage4_data($person, $data);
			$this->load->view('header', $data);
			$this->load->view('debug', $data);
			$this->load->view('stage4_node_confirm', $data);
			$this->load->view('footer', $data);
		} else {
			print "You must select a valid Node before continuing.";
		}
	}

	function update_node(&$data)
	{
		# TODO: RECODE To update values instead of adding them...
		global $api, $plc;
		$hostname = trim($_REQUEST['hostname']);
		$node_type = trim($_REQUEST['node_type']);
		$model = trim($_REQUEST['model']);
		$node_id = intval($this->validation->node_id);
		$optional_vals = array('hostname' => $hostname, 'model' => $model, 'node_type' => $node_type );
		$ret = $api->UpdateNode( $node_id, $optional_vals);
		if( $ret <= 0 ) {
			$data['error'] = $api->error();
			print $data['error'];
		}

		$api_node_list = $api->GetNodes($node_id);
		if ( count($api_node_list) > 0 ) {
			$node_obj = new Node($api_node_list[0], True);
		} else {
			print "broken!!!";
			exit (1);
		}
		

		$optional_vals= array();

		$method = trim($_REQUEST['method']);
		if ( $node_obj->method != $method ) {
			$optional_vals['method']= $method;
		}

		$ip = trim($_REQUEST['ip']);
		if ( $node_obj->ip != $ip ) {
			$optional_vals['ip']= $ip;
		}

		// used to generate error strings for static fields only
		$static_fields= array();
		$static_fields['netmask']	= "Netmask address";
		$static_fields['network']	= "Network address";
		$static_fields['gateway']	= "Gateway address";
		$static_fields['broadcast']	= "Broadcast address";
		$static_fields['dns1']		= "Primary DNS address";

		if ( $method == 'static' )
		{
			$netmask = trim($_REQUEST['netmask']);
			$network = trim($_REQUEST['network']);
			$gateway = trim($_REQUEST['gateway']);
			$broadcast = trim($_REQUEST['broadcast']);
			$dns1 = trim($_REQUEST['dns1']);
			$dns2 = trim($_REQUEST['dns2']);

			if( !is_valid_network_addr($network,$netmask) )
			{
				$errors[] = "The network address does not coorespond to the netmask";
			}
		}

		if ( !isset($errors) || count($errors) == 0 )
		{
			// now, try to add the network.
			if( $method == 'static' )
			{
				if ( $node_obj->gateway != $gateway ) {
					$optional_vals['gateway']= $gateway;
				}
				if ( $node_obj->network != $network ) {
					$optional_vals['network']= $network;
				}
				if ( $node_obj->broadcast != $broadcast ) {
					$optional_vals['broadcast']= $broadcast;
				}
				if ( $node_obj->netmask != $netmask ) {
					$optional_vals['netmask']= $netmask;
				}
				if ( $node_obj->dns1 != $dns1 ) {
					$optional_vals['dns1']= $dns1;
				}
				if ( $node_obj->dns2 != $dns2 ) {
					$optional_vals['dns2']= $dns2;
				}
			}

			if ( count($optional_vals) > 0 )
			{
			  // print_r($optional_vals);
				$ret = $api->UpdateInterface( $node_obj->interface_id, $optional_vals);
				if( $ret <= 0 ) {
					$data['error'] = $api->error();
					print $data['error'];
				}
			}
		}

		$data['node_id'] = $node_id;
		if ( isset($errors) ) { $data['errors'] = $errors; }
		
		return $node_id;
	}

	function get_stage4_data($person, $data=NULL)
	{
		global $api, $plc;
		if ( $data == NULL ){
		    $data = array();
		}
		$data['node'] = $this->getnode($data['node_id']);
		$data['site'] = $this->getsite($data['site_id']);
		/*print "SITENAME: " . $data['site']['login_base'] . "<BR>";*/
		return $data;
	}

	function checknodeid($node_id)
	{
		global $api, $plc;
		$plc_node_list = $api->GetNodes(array('node_id' => intval($node_id) ), array('node_id'));
		if ( count($plc_node_list) > 0 ) {
			return True;
		} else {
			return False;
		}
	}

	function getnode($node_id)
	{
		global $api, $plc;
		$plc_node_list = $api->GetNodes(array('node_id' => intval($node_id) ));
		if ( count($plc_node_list) > 0 )
		{
			return new Node($plc_node_list[0], True);
		} else {
			return NULL;
		}
	}

}
?>
