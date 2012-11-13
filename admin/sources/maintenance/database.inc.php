<?php
/*
+--------------------------------------------------------------------------
|   CubeCart 4
|   ========================================
|	CubeCart is a registered trade mark of Devellion Limited
|   Copyright Devellion Limited 2010. All rights reserved.
|	UK Private Limited Company No. 5323904
|   ========================================
|   Web: http://www.cubecart.com
|   Email: sales@devellion.com
|	License Type: CubeCart is NOT Open Source. Unauthorized reproduction is not allowed.
|   Licence Info: http://www.cubecart.com/v4-software-license
+--------------------------------------------------------------------------
|	database.inc.php
|   ========================================
|	Database Tools
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

permission('maintenance','read',true);

$lang = getLang('admin'.CC_DS.'admin_misc.inc.php');

if(isset($_POST['action']) && is_array($_POST['tableName'])){

$sqlQuery = $_POST['action'].' TABLE ';


foreach($_POST['tableName'] as $value){

	$sqlQuery.= '`'.$value.'` ,';

}
$sqlQuery = substr($sqlQuery,0,strlen($sqlQuery) -2);
$results = $db->getRows($sqlQuery);

	$msg = "<p class='infoText'>".sprintf($lang['admin']['misc_db_success'],$_POST['action'])."</p>";

} elseif(isset($_POST['action'])) {
	$msg = "<p class='warnText'>".$lang['admin']['misc_db_none_selected']."</p>";
}

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');
?>
<p class="pageTitle"><?php echo sprintf($lang['admin']['misc_db_maintenance'],$glob['dbdatabase']);?> </p>
<?php
if(isset($msg)){
	echo msg($msg);
}
?>
<p class="copyText"><?php echo sprintf($lang['admin']['misc_db_info'],mysql_get_server_info(),$glob['dbhost'],$glob['dbusername'],$glob['dbhost']); ?> <a href="<?php echo $glob['adminFile'];?>?_g=maintenance/sql" class="txtLink">&raquo;</a></p>

<?php if(isset($_POST['action']) && is_array($_POST['tableName'])){  ?>
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
	<tr>
		<td align="center" class="tdTitle"><?php echo $lang['admin']['misc_db_table']; ?></td>
		<td align="center" class="tdTitle"><?php echo $lang['admin']['misc_db_operation']; ?></td>
		<td align="center" class="tdTitle"><?php echo $lang['admin']['misc_db_msg_type']; ?></td>
		<td align="center" class="tdTitle"><?php echo $lang['admin']['misc_db_msg_text']; ?></td>
	</tr>
	<?php
	if(is_array($results)){

	foreach($results as $result){
		++$i;
		$cellColor = cellColor($i);
	?>
	<tr>
		<td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $result[0]; ?></span></td>
		<td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $result[1]; ?></span></td>
		<td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $result[2]; ?></span></td>
		<td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $result[3]; ?></span></td>
	</tr>
	<?php
		}
	} ?>
</table>
	<?php } else { ?>
<form name="maintainDB" action="<?php echo $glob['adminFile']; ?>?_g=maintenance/database" enctype="multipart/form-data" method="post">
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
	<tr>
		<td width="10" class="tdTitle">&nbsp;</td>
		<td align="center" class="tdTitle"><?php echo $lang['admin']['misc_db_table']; ?></td>
		<td align="center" class="tdTitle"><?php echo $lang['admin']['misc_db_records']; ?></td>
		<td align="center" class="tdTitle"><?php echo $lang['admin']['misc_db_type']; ?></td>
		<td align="center" class="tdTitle"><?php echo $lang['admin']['misc_db_size']; ?></td>
		<td align="center" class="tdTitle"><?php echo $lang['admin']['misc_db_overhead']; ?></td>
	</tr>
	<?php
	$tables = $db->getRows("SHOW TABLE STATUS LIKE '".$glob['dbprefix']."CubeCart_%';");
	if(is_array($tables)){

		$totalRecords = 0;
		$totalSize = 0;
		$totalOverhead = 0;

		foreach($tables as $table){

		++$i;

		$cellColor = cellColor($i);
		$totalRecords = $totalRecords + $table[4];
		$totalSize = $totalSize + $table[8];
		$totalOverhead = $totalOverhead + $table[9];
			?>
			<tr>
				<td width="10" align="center" class="<?php echo $cellColor; ?>"><input type="checkbox" id="tableName" value="<?php echo $table[0]; ?>" name="tableName[]" /></td>
				<td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $table[0]; ?></span></td>
				<td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $table[4]; ?></span></td>
				<td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $table[1]; ?></span></td>
				<td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo format_size($table[8]); ?></span></td>
				<td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo format_size($table[9]); ?></span></td>
			</tr>
		<?php }   } ?>
		<tr>
				<td colspan="2" class="tdText">
				<img src="<?php echo $glob['adminFolder']; ?>/images/selectAll.gif" alt="" width="16" height="11" /> <a href="javascript:checkAll('tableName','true');" class="txtLink"><?php echo $lang['admin']['misc_db_check_all'];?></a> / <a href="javascript:checkAll('tableName','false');" class="txtLink"><?php echo $lang['admin']['misc_db_uncheck_all'];?></a>
				<select name="action" size="1" class="textbox" onchange="submitDoc('maintainDB');">
                  <option value=""><?php echo $lang['admin']['misc_db_with_sel'];?></option>
				  <option value="OPTIMIZE"><?php echo $lang['admin']['misc_db_optimise'];?></option>
                  <option value="REPAIR"><?php echo $lang['admin']['misc_db_repair'];?></option>
				  <option value="CHECK" ><?php echo $lang['admin']['misc_db_check'];?></option>
            	  <option value="ANALYZE" ><?php echo $lang['admin']['misc_db_analyze'];?></option>
                </select>
				</td>
				<td align="center" class="tdText"><strong><?php echo $totalRecords; ?></strong></td>
				<td align="center" class="tdText">&nbsp;</td>
				<td align="center" class="tdText"><strong><?php echo format_size($totalSize); ?></strong></td>
				<td align="center" class="tdText"><strong><?php echo format_size($totalOverhead); ?></strong></td>
			</tr>

	</table>
</form>
<?php
}
?>