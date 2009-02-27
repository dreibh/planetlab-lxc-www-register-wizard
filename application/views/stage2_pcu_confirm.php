<h3>Confirm PCU Information</h3>
<div class="plroundedconfirm">
<table><tr><td>
If the information below is correct, then Proceed, otherwise Update the values
below.
</td></tr>
<tr><td>
			<?= form_open("register/stage3_addnode/$pcu_id/$site_id", array('style' => 'display: inline; margin: 0px')) ?>

	<center>
					<input type=submit name='pcu_proceed' value='Confirm & Proceed ->'>
	</center>
			</form>
</td></tr></table>
</div>
<br>
<div class="plroundedupdate">
<?= form_open('register/stage2_confirmpcu', array('style' => 'display: inline; margin: 0px')) ?>
			<table border=0 cellpadding=3>
			<tbody>
					<tr><th>Site: </th><td><?= $pcu_site[0]['name'] ?></td></tr>
				<tr><th>Model: </th>
					<td>
						<select name='model'>
							<option value='none-selected'>---</option>
							<?php 
								 foreach( $pcu_types as $pcu_type ):
								 	$model = $pcu_type['model'];
								 	$name  = $pcu_type['name'];
									if( $pcu->data['model'] == $model)
										$selected = "selected";
									else
										$selected = "";
									?>
									<option value='<?= $model ?>' <?= $selected ?>><?= $name ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th>Hostname: </th>
					<td>
						<input type=text name='hostname' size=40 value='<?= $pcu->data['hostname'] ?>'>
						<?= ( $this->disp_errors ? $this->validation->hostname_error : "" ) ?>
					</td>
				</tr>
				<tr><th>IP Address: </th>
					<td><input type=text name='ip' value='<?= $pcu->data['ip'] ?>'>
						<?= ( $this->disp_errors ? $this->validation->ip_error : "" ) ?>
					</td>
				</tr>
				<tr><th>Username: </th>
					<td><input type=text name='username' value='<?= $pcu->data['username'] ?>'>
						<?= ( $this->disp_errors ? $this->validation->username_error : "" ) ?>
					</td>
				</tr>
				<tr><th>Password: </th>
					<td>
						<input type=text name='password' value='<?= $pcu->data['password'] ?>'>
						<?= ( $this->disp_errors ? $this->validation->password_error : "" ) ?>
					</td>
				</tr>
				<tr><th>Notes: </th>
					<td><textarea name="notes" rows=5 cols=40><?= $pcu->data['notes'] ?></textarea></td>
				</tr>
		<tr>
			<td></td>
			<td>
				<input type=hidden name='pcu_id' value='<?= $pcu_id ?>'>
				<input type=hidden name='site_id' value='<?= $site_id ?>'>
				<input type=submit name='pcu_update' value='Update'> 
			</form>
			</td>
		</tr>
	</tbody>
	</table>
</div>
        <br /><p>
			<a href='/db/sites/index.php?id=<?= $default_site_list[0] ?>'>Back to Home Site</a>
