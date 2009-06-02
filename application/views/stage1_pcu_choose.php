<div class="plroundedconfirm">
<?= form_open('register/stage2_confirmpcu') ?>
		<table border=0 cellpadding=3>
			<tbody>
		<?php if ( !empty($pcu_list) ): ?>
				<tr><td colspan='2'><h3>Choose an Existing PCU</h3></td></tr>
				<tr><th>PCU Name: </th><td>
							<select name='pcu_id'>
							<?php foreach ( $pcu_list as $pcu ): ?>
									<option value='<?= $pcu->data['pcu_id'] ?>' <?= ( $this->pcu_id == $pcu->data['pcu_id'] ? "selected" : "") ?>>
										<?= $pcu->pcu_name() ?>
									</option>
							<?php endforeach; ?>
							</select>
						</td>
				</tr>
				<tr><td></td>
					<td>
						<input type=submit name='pcu_choose' value='Continue'>
					</td>
				</tr>
		<?php endif; ?>
		</tbody>
	</table>
</form>
</div>
<br>
<div class="plroundedupdate">
<?php if ( !isset($pcu_id) || $pcu_id == 0 ): ?>
	<?= form_open('register/stage1_addpcu') ?>
			<table border=0 cellpadding=3>
				<tbody>
					<tr><td colspan='2'><h3>Or, Register a New PCU</h3></td></tr>
			<?php if ( !empty($site_list) ): ?>
					<tr><th>Site: </th><td>
								<select name='site_id'>
								<?php 
									 foreach ( $site_list as $site ) 
									 { 
										if ( $site['site_id'] == $default_site_list[0] )
											$selected = " selected";
										else
											$selected = "";
										?>
										<option value='<?= $site['site_id'] ?>' <?= $selected ?>>
											<?= $site['name'] ?>
										</option>
								<?php } ?>
								</select>
							</td>
							<td>
							</td>
						</tr>
			<?php else: ?>
					<tr><th>ERROR:</th><td>No sites returned...</td></tr>
			<?php endif; ?>
					<tr><th>Model: </th>
						<td>
			<select name='model'>
				<option value='none-selected' 
						<?= $this->validation->set_select('model', 'none-selected') ?>>---</option>
				<?php foreach( $pcu_types as $pcu_type ): ?>
						<option value='<?= $pcu_type['model'] ?>' 
							<?= $this->validation->set_select('model', $pcu_type['model']) ?>>
							<?= $pcu_type['name'] ?>
						</option>
				<?php endforeach; ?>
			</select>
						</td>
					</tr>
					<tr>
						<th>Hostname: </th>
						<td nowrap>
							<input	type=text name='hostname' size=40 
									value='<?= $this->validation->hostname ?>'>
							<?= $this->validation->hostname_error ?>
						</td>
					</tr>
					<tr><th>IP Address: </th>
						<td nowrap>
							<input type=text name='ip' 
									value='<?= $this->validation->ip ?>'>
							<?= $this->validation->ip_error ?>
						</td>
					</tr>
					<tr><th>Username: </th>
						<td nowrap>
							<input type=text name='username' 
									value='<?= $this->validation->username ?>'>
							<?= $this->validation->username_error ?>
						</td>
					</tr>
					<tr><th>Password: </th>
						<td nowrap>
							<input type=text name='password' 
									value='<?= $this->validation->password ?>'>
							<?= $this->validation->password_error ?>
						</td>
					</tr>
					<tr><th>Notes: </th>
						<td><textarea name="notes" rows=5 cols=40></textarea></td>
					</tr>
					<tr><td></td>
						<td>
							<input type=submit name='pcu_register' value='Add New PCU'>
						</td>
					</tr>
				</tbody>
				</table>
	</form>
</div>
<?php else: ?>
	<table border=0 cellpadding=3>
		<tbody>
			<tr><td></td><td><h3>PCU Added Successfully!!!!</h3></td></tr>
			<?php if ( ! empty($error) ): ?>
			<tr><td></td><td><?= $error ?></td></tr>
			<?php endif; ?>
		</tbody>
	</table>
<?php endif; ?>
	<br /><p>
		<a href='/db/sites/index.php?id=<?= $default_site_list[0] ?>'>Back to Home Site</a>
