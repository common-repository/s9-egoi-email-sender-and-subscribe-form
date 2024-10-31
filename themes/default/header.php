<!-- header -->
<style type="text/css">
	p   { font-size:12px; font-family:Arial, Helvetica, sans-serif; color:#333; }
	a   { color:#444; outline:none; }
	img { border:none; outline:none;}
</style>
<table width="580" cellpadding="0" cellspacing="0" border="0" align="center">
<tr>
<td align="center">
<?php 
	if (!empty($options['image_header'])) {
?>
	<table cellpadding="0" cellspacing="0" border="0">
    <tr>
	<td width="580" height="200"><a href="<?php echo $siteurl; ?>"><img src="<?php echo $options['image_header']?>" border="0" width="580" height="200" alt="header" /></a></td>
    </tr>
    </table>
<?		
	}
?>
