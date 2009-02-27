<?php
if( isset($errors) && count($errors) > 0 )
{
  print( "<p><strong>The following errors occured:</strong>" );
  print( "<font color='red' size='-1'><ul>\n" );
  foreach( $errors as $err )
    {
      print( "<li>$err\n" );
    }
  print( "</ul></font>\n" );
}
?>
<script language="javascript">
function updateStaticFields()
{
  var is_dhcp= document.fm.method[0].checked;

  document.fm.netmask.disabled= is_dhcp;
  document.fm.network.disabled= is_dhcp;
  document.fm.gateway.disabled= is_dhcp;
  document.fm.broadcast.disabled= is_dhcp;
  document.fm.dns1.disabled= is_dhcp;
  document.fm.dns2.disabled= is_dhcp;
}
</script>
<div class="plroundedconfirm">
<h3>Confirm Node Information</h3>
<?= form_open("register/stage45_mappcu/$pcu_id/$site_id/$node_id", array('method'=>'post')) ?>
		<table border=0 cellpadding=3>
			<tbody>
				<tr><td colspan='2'>
Please review the information below, and if it is correct, the Proceed to then
next stage.  Otherwise, please Update the information as appropriate.
				</td></tr>
				<tr><td></td>
					<td>
					<center>
						<input type=hidden name='pcu_id' value='<?= $pcu_id ?>'>
						<input type=hidden name='node_id' value='<?= $node_id ?>'>
						<input type=hidden name='site_id' value='<?= $site_id ?>'>
						<input type=submit name='node_confirm' value='Confirm & Proceed'>
					</center>
					</td>
				</tr>
					</tr>
			</tbody>
			</table>
	</form>
</div>
<br>
<div class="plroundedupdate">
<?= form_open("register/stage4_confirmnode/$pcu_id/$site_id/$node_id", array('name'=>'fm', 'method'=>'post')) ?>
		<table border=0 cellpadding=3>
			<tbody>
		<tr><td colspan='2'>
		</td></tr>
		<?php if ( !empty($site) ) 
			{ ?>
					<tr><th>Site: </th><td> <?= $site['name'] ?> </td>
					</tr>
		<?php } ?>
				<tr>
				</tr>
					<th width=200>Hostname:</td>
					<td><input type="text" name="hostname" value="<?= $node->hostname ?>" 
								size="40" maxlength="256">
						<?= ( $this->disp_errors ? $this->validation->hostname_error : "")  ?>
					</td>
				<tr>
					<th>Model:</th>
					<td>
						<input type="text" name="model" value="<?= $node->model ?>" 
								size="40" maxlength="256">
						<?= ( $this->disp_errors ? $this->validation->model_error : "")  ?>
					</td>
				</tr>
				<tr>
					<th valign='top' width="200">Addressing Method</th>
					<td>
						<input type="radio" name="method" value="dhcp" onChange='updateStaticFields()'
						<?= ( $node->method == 'dhcp' ? "checked" : "" ) ?>>DHCP 
						<input type="radio" name="method" value="static" onChange='updateStaticFields()'
						<?= ( $node->method == 'static' ? "checked" : "" ) ?>>Static 
					</td>
				</tr>

				<tr><th valign='top'>IP Address</th>
					<td><input type="text" name="ip" value="<?= $node->ip ?>">
						<?= ( $this->disp_errors ? $this->validation->ip_error : "")  ?>
					</td>
				</tr>

				<tr> 
					<th valign='top'>Netmask</font></th>
					<td><input type="text" name="netmask" value="<?= $node->netmask ?>">
						<?= ( $this->disp_errors ? $this->validation->netmask_error : "")  ?>
					</td>
				</tr>

				<tr> 
					<th valign='top'>Network address</th>
					<td><input type="text" name="network" value="<?= $node->network ?>">
						<?= ( $this->disp_errors ? $this->validation->network_error : "")  ?>
					</td>
				</tr>

				<tr> 
					<th valign='top'>Gateway Address</th>
					<td><input type="text" name="gateway" value="<?= $node->gateway ?>">
						<?= ( $this->disp_errors ? $this->validation->gateway_error : "")  ?>
					</td>
				</tr>

				<tr> 
					<th valign='top'>Broadcast address</th>
					<td><input type="text" name="broadcast" value="<?= $node->broadcast ?>">
						<?= ( $this->disp_errors ? $this->validation->broadcast_error : "")  ?>
					</td>
				</tr>

				<tr> 
					<th valign='top'>Primary DNS</th>
					<td><input type="text" name="dns1" value="<?= $node->dns1 ?>">
						<?= ( $this->disp_errors ? $this->validation->dns1_error : "")  ?>
					</td>
				</tr>

				<tr>
					<th valign='top'>Secondary DNS</th>
					<td><input type="text" name="dns2" value="<?= $node->dns2 ?>">
						<?= ( $this->disp_errors ? $this->validation->dns2_error : "")  ?>
						(optional)
					</td>
				</tr>

				<tr><td></td>
					<td>
						<input type=hidden name='pcu_id' value='<?= $pcu_id ?>'>
						<input type=hidden name='node_id' value='<?= $node_id ?>'>
						<input type=hidden name='site_id' value='<?= $site_id ?>'>
						<input type=submit name='node_update' value='Update & Return'> 
					</td>
				</tr>
			</tbody>
			</table>
</div>
        <br /><p>
			<a href='/db/sites/index.php?id=<?= $site_id ?>'>Back to Home Site</a>
	
	</form>
<script language="javascript">
updateStaticFields();
</script>
