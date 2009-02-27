<script language="javascript">
function updateContinueEnabled()
{
	document.fm.button_continue.disabled = false;
}
</script>

<div class="plroundedupdate">
<div class="plroundedwhite">

<h3>Download BootImage and Follow Steps Below</h3>

<?php
/*
	if( $can_gen_config ):
		if (method_exists($api,"GetBootMedium")):
			$preview=$api->GetBootMedium($node_id,"node-preview",""); ?>
			<h4>Current node configuration contents</h4>
			<pre><?= $preview ?></pre>
			<hr />
	endif; 
*/
?>

<dl>
	<dt>Step 1</dt>
	 <?php  if ( $action == "download-node-floppy-with-iso" ): ?>                             
		<dd>Download the <a href="/download/<?= $generic_iso_name 
			?>.iso">Generic ISO image</a>.  Then,
			use the 'Download' button below to get the configuration file for your
			floppy disk. </dd>
	 <?php  elseif ( $action == "download-node-floppy-with-usb" ): ?>
		<dd>Download the <a
			href="/download/<?= $generic_iso_name ?>.usb">Generic USB image</a>.  Then,
			use the 'Download' button below to get the configuration file for your
			floppy disk.  </dd>
	 <?php  else: ?>
		<dd>Download your BootImage using 'Download' button below.</dd>
	 <?php  endif; ?>

	<dt>Step 2</dt><dd>Burn or copy the BootImage to the <a target="_blank" href="http://www.planet-lab.org/doc/guides/bootcdsetup">appropriate read-only media</a></dd>
	<dt>Step 3</dt><dd>Install the BootImage in your machine. Turn the machine on and allow it to boot.</dd>
	<dt>Step 4</dt><dd>Continue to the next Stage.</dd>
</dl>


<p class='error'>WARNING: By using the download button below, we will generate
a new node key for your machine. Therefore, all previous configuration files or All-in-one BootImages that you have downloaded will be expired.  Only continue if you will complete the installation process.</p>

	<p>
	<?= form_open("download/stage6_download/$pcu_id/$site_id/$node_id", array('style' => 'display: inline; margin: 0px')) ?>
		<input type='hidden' name='action' value='<?= $action ?>'>
		<input type='hidden' name='download' value='1'>
		<input name='button_download' type='submit' value='Download <?= $format ?>' onclick='updateContinueEnabled();'>
	</form>
	<?= form_open("confirm/stage7_firstcontact/$pcu_id/$site_id/$node_id", array('name' => 'fm', 'style' => 'display: inline; margin: 0px')) ?>
		<input name='button_continue' disabled type='submit' value='Continue ->'>
	</form>
</div>
</div>
<?php /* 	else: 
   		<p><font color=red>Configuration file cannot be created.</font>
<?php	endif; */ ?>
