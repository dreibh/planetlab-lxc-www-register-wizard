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
<?= form_open("register/stage4_confirmnode/$pcu_id/$site_id") ?>
		<table border=0 cellpadding=3>
			<tbody>
				<tr><td colspan='2'><h3>Choose a Node to Associate with PCU</h3></td></tr>
		<?php if ( !empty($node_list) ): ?>
					<tr><th>Node Name: </th><td>
							<select name='node_id'>
									<option value='0'>--</option>
							<?php foreach ( $node_list as $node): ?>
									<option value='<?= $node->data['node_id'] ?>' <?= ( $this->node_id == $node->data['node_id'] ? "selected" : "" ) ?>>
										<?= $node->hostname ?>
									</option>
							<?php endforeach; ?>
							</select>
						</td>
						<td>
						</td>
				<tr><td></td>
					<td>
						<input type=submit name='node_choose' value='Continue'>
					</td>
				</tr>
					</tr>
		<?php endif; ?>
			</tbody>
			</table>
	</form>
</div>
<br>
<div class="plroundedupdate">
		<table border=0 cellpadding=3>
			<tbody>
<?php if ( !isset($node_id) || $node_id == 0 ): ?>
<?= form_open("register/stage3_addnode/$pcu_id/$site_id", array('name'=>'fm', 'method'=>'post')) ?>
		<tr><td colspan='2'><h3>Or, Add a Node</h3></td></tr>
					<tr><th>Site: </th><td><?= $site['name'] ?></td>
					</tr>
				<tr>
					<th width=200>Hostname:</td>
					<td><input type="text" name="hostname" value="<?= $this->validation->hostname ?>" 
								size="40" maxlength="256">
						<?= ( $this->disp_errors ? $this->validation->hostname_error : "")  ?>
					</td>
				</tr>
				<tr>
					<th>Model:</td>
					<td>
						<input type="text" name="model" value="<?= $this->validation->model ?>" 
								size="40" maxlength="256">
						<?= ( $this->disp_errors ? $this->validation->model_error : "")  ?>
					</td>
				</tr>
				<tr>
					<td> <b>Network Settings</b> </td>
				</tr>

				<tr>
					<th valign='top' width="200">Addressing Method</th>
					<td>
						<input type="radio" name="method" value="dhcp" onChange='updateStaticFields()'
						<?= ( $this->validation->method == 'dhcp' ? "checked" : "" ) ?>>DHCP 
						<input type="radio" name="method" value="static" onChange='updateStaticFields()'
						<?= ( $this->validation->method == 'static' ? "checked" : "" ) ?>>Static 
					</td>
				</tr>

				<tr><th valign='top'>IP Address</td>
					<td><input type="text" name="ip" value="<?= $this->validation->ip ?>">
						<?= ( $this->disp_errors ? $this->validation->ip_error : "")  ?>
					</td>
				</tr>

				<tr> 
					<th valign='top'>Netmask</font></td>
					<td><input type="text" name="netmask" value="<?= $this->validation->netmask ?>">
						<?= ( $this->disp_errors ? $this->validation->netmask_error : "")  ?>
					</td>
				</tr>

				<tr> 
					<th valign='top'>Network address</td>
					<td><input type="text" name="network" value="<?= $this->validation->network ?>">
						<?= ( $this->disp_errors ? $this->validation->network_error : "")  ?>
					</td>
				</tr>

				<tr> 
					<th valign='top'>Gateway Address</td>
					<td><input type="text" name="gateway" value="<?= $this->validation->gateway ?>">
						<?= ( $this->disp_errors ? $this->validation->gateway_error : "")  ?>
					</td>
				</tr>

				<tr> 
					<th valign='top'>Broadcast address</td>
					<td><input type="text" name="broadcast" value="<?= $this->validation->broadcast ?>">
						<?= ( $this->disp_errors ? $this->validation->broadcast_error : "")  ?>
					</td>
				</tr>

				<tr> 
					<th valign='top'>Primary DNS</td>
					<td><input type="text" name="dns1" value="<?= $this->validation->dns1 ?>">
						<?= ( $this->disp_errors ? $this->validation->dns1_error : "")  ?>
					</td>
				</tr>

				<tr>
					<th valign='top'>Secondary DNS</td>
					<td><input type="text" name="dns2" value="<?= $this->validation->dns2 ?>">
						<?= ( $this->disp_errors ? $this->validation->dns2_error : "")  ?>
						(optional)
					</td>
				</tr>

				<tr><td></td>
					<td>
						<input type=hidden name='pcu_id' value='<?= $pcu_id ?>'>
						<input type=hidden name='site_id' value='<?= $site_id ?>'>
						<input type=submit name='register_node' value='Add New Node'>
					</td>
				</tr>
		</tbody>
	</table>
<?php else: ?>
	<table border=0 cellpadding=3>
		<tbody>
			<tr><td></td><td><h3>Node Added Successfully!!!!</h3></td></tr>
			<?php if ( isset($errors) and ! empty ($errors) ): ?>
				<?php foreach ( $errors as $error ): ?>
					<tr><td></td><td><?= $error ?></td></tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
<?php endif; ?>
</div>
        <br /><p>
			<a href='/db/sites/index.php?id=<?= $default_site_list[0] ?>'>Back to Home Site</a>
	
	</form>
<script language="javascript">
updateStaticFields();
</script>
