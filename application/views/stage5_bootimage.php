<script language="javascript">
function updateContinueEnabled()
{
	document.fm.download_bootimage.disabled = false;
}
</script>
<h3>Choose a Boot Image</h3>
<div class="plroundedupdate">
<br>
<?= form_open("download/stage6_download/$pcu_id/$site_id/$node_id", array('name' => 'fm', 'style' => 'display: inline; margin: 0px')) ?>

<div class="plroundedwhite"
		id="bootcd-sidebar">
	<p> Pick one of the following options. Then, download the files needed for that option.</p>

	<table style="font-size: 100%">
	<tr>
	  <td><input type='radio' name='action' value='download-node-iso' onchange='updateContinueEnabled();'></td>
	  <td><img src="/registerwizard/images/all-in-one-cd.png"/></td><td><b>All-In-One ISO image</b>.  
	  		Includes plnode.txt.  No additional formatting required.</td>
	</tr>
	<tr>
	  <td><input type='radio' name='action' value='download-node-usb' onchange='updateContinueEnabled();'></td>
	  <td><img src="/registerwizard/images/all-in-one-usb.png" /></td><td><b>All-In-One USB image</b>. Includes plnode.txt, and filesystem only.  Requires additional USB stick formatting.**</td>
	</tr>
	<tr>
	  <td><input type='radio' name='action' value='download-node-usb-partition' onchange='updateContinueEnabled();'></td>
	  <td><img src="/registerwizard/images/all-in-one-usb.png" /></td><td><b>All-In-One partitioned, USB image</b>.  Includes, plnode.txt, MBR, partition table, and filesystem.  No additional formatting required.**</td>
	</tr>
	<!--tr>
	  <td><input type='radio' name='action' value='download-node-floppy-with-iso' onchange='updateContinueEnabled();'></td>
	  <td><img src="/misc/generic-cd-and-floppy.png" /></td><td><b>Generic CD</b>, and <b>plnode.txt</b> on Floppy</td>
	</tr>
	<tr>
	  <td><input type='radio' name='action' value='download-node-floppy-with-usb' onchange='updateContinueEnabled();'></td>
	  <td><img src="/misc/generic-usb-and-floppy.png" /></td><td><b>Generic USB</b>, and <b>plnode.txt</b> on Floppy</td>
	</tr-->
	</table>

	<p></p>
	<p>Additional directions are provided on the next page.</p>
	<p>NOTE:  ** USB images are not guaranteed to work on all systems or with all USB memory sticks.</p>
</div>
	<center>
	<input type=submit name='download_bootimage' disabled value='Select'> 
	</center>
</form>
</div>
