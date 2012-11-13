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
|	backup.inc.php
|   ========================================
|	Backup MySQL DB
+--------------------------------------------------------------------------
*/

if(!defined('CC_INI_SET')){ die("Access Denied"); }

permission('maintenance','read', true);

$lang = getLang('admin'.CC_DS.'admin_misc.inc.php');

if(isset($_POST['dbbackup'])){
	@ini_set('memory_limit', '512M');
	@set_time_limit(300);
	if(!isset($_POST['drop']) && !isset($_POST['structure']) && !isset($_POST['data'])){

		$msg = "<p class='warnText'>".$lang['admin']['misc_bkup_check_one']."</p>";

	} elseif($_POST['drop']==1 && !isset($_POST['structure'])){

		$msg = "<p class='warnText'>".$lang['admin']['misc_bkup_required_dep']."</p>";

	} else {

		$tables = $db->getRows('SHOW TABLE STATUS LIKE \''.$glob['dbprefix'].'CubeCart_%\';');
        
        $data = array();
        $data[] = '-- --------------------------------------------------------\n-- CubeCart SQL Dump\n-- version '.$ini['ver'].'\n-- http://www.cubecart.com\n-- [^] \n-- Host: '.$glob['dbhost'].'\n-- Generation Time: '.strftime($config['timeFormat'],time()).'\n-- Server version: '.mysql_get_server_info().'\n-- PHP Version: '.phpversion().'\n-- \n-- Database: `'.$glob['dbdatabase'].'`\n';
        
        foreach($tables as $table){
            $data[] = $db->sqldumptable($table,$_POST['drop'],$_POST['structure'],$_POST['data']);
        }
    
        $datalen = 0;
        for ($i=0; $i<count($data); $i++)
            $datalen += strlen($data[$i]);

        $filename = $glob['dbdatabase'].'_'.date("dMy").'.sql';
    
        header('Pragma: private');
        header('Cache-control: private, must-revalidate');
        header("Content-Disposition: attachment; filename=".$filename);
        header("Content-type: text/plain");
        header("Content-type: application/octet-stream");
        header("Content-length: ".$datalen);
        header("Content-Transfer-Encoding: binary");
        for ($i=0; $i<count($data); $i++) {
            print $data[$i];
            @flush();
        }
        exit; 
	}

}

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');

?>
<p class='pageTitle'><?php echo $lang['admin']['misc_bkup_title'];?></p>
<?php
if(isset($msg))
{
	echo msg($msg);
}
?>
<form method="post" action="<?php echo $glob['adminFile']; ?>?_g=maintenance/backup" enctype="multipart/form-data" name="dbbackup">
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
<tr>
<td class="tdTitle" colspan="2"><?php echo $lang['admin']['misc_bkup_title'];?></td>
</tr>
<tr>
<td width="33%" class="tdText"><?php echo $lang['admin']['misc_bkup_inc_drop'];?></td>
<td class="tdText"><input name="drop" type="checkbox" value="1" /></td>
</tr>
<tr>
  <td width="33%" class="tdText"><?php echo $lang['admin']['misc_bkup_inc_structure'];?> </td>
  <td class="tdText"><input type="checkbox" name="structure" value="1" checked="checked" /></td>
</tr>
<tr>
  <td width="33%" class="tdText"><?php echo $lang['admin']['misc_bkup_inc_data'];?> </td>
  <td class="tdText"><input type="checkbox" name="data" value="1" checked="checked" /></td>
</tr>
<tr>
<td width="33%" class="tdText"> </td>
<td class="tdText">
<input type="hidden" name="dbbackup" value="1" />
<input name="submit" type="submit" class="submit" value="<?php echo $lang['admin']['misc_download_now'];?>" /></td>
</tr>
</table>
</form>