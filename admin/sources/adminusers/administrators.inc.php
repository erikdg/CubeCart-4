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
|	administrators.inc.php
|   ========================================
|	Manage Administrators
+--------------------------------------------------------------------------
*/

if (!defined('CC_INI_SET')) die("Access Denied");

$lang = getLang('admin'.CC_DS.'admin_adminusers.inc.php');

permission('administrators', 'read', true);

$rowsPerPage = 25;

if (isset($_GET['delete']) && $_GET['delete']>0){

	$where	= '`adminId` = '.$db->mySQLSafe($_GET['delete']);
	$delete	= $db->delete($glob['dbprefix'].'CubeCart_admin_users', $where);
	$deletePerms = $db->delete($glob['dbprefix'].'CubeCart_admin_permissions', $where);

	if ($delete) {
		$msg = '<p class="infoText">'.$lang['admin']['adminusers_del_success'].'</p>';
	} else {
		$msg = '<p class="warnText">'.$lang['admin']['adminusers_del_failed'].'</p>';
	}

} elseif (isset($_POST['adminId'])) {

	$record['name'] = $db->mySQLSafe($_POST['name']);
	$record['username'] = $db->mySQLSafe($_POST['adminUsername']);

	if(!empty($_POST['adminPassword']) && ($_POST['adminPassword'] == $_POST['adminPassword_verify'])){
		$salt = randomPass(6);
		$record['salt'] = $db->mySQLSafe($salt);
		$record['password'] = $db->mySQLSafe(md5(md5($salt).md5($_POST['adminPassword'])));
	}

	$record['notes'] = $db->mySQLSafe($_POST['notes']);
	$record['email'] = $db->mySQLSafe($_POST['email']);
	$record['isSuper'] = $db->mySQLSafe($_POST['isSuper']);

	if(!empty($_POST['adminPassword']) && ($_POST['adminPassword'] != $_POST['adminPassword_verify'])){
		$msg = "<p class='warnText'>".$lang['admin']['adminusers_password_missmatch']."</p>";
	} else {

		if($_POST['adminId']>0) {
			$where = '`adminId` = '.$db->mySQLSafe($_POST['adminId']);
			$update = $db->update($glob['dbprefix'].'CubeCart_admin_users', $record, $where);
			unset($record, $where);

			if($update){
				 $msg = "<p class='infoText'>'".$_POST['name']."' ".$lang['admin']['adminusers_update_success']."</p>";
			} else {
				$msg = "<p class='warnText'>".$lang['admin']['adminusers_update_fail']."</p>";
			}

		} else {
			$insert = $db->insert($glob['dbprefix'].'CubeCart_admin_users', $record);
			unset($record);

			if($insert) {
				$msg = "<p class='infoText'>'".$_POST['name']."' ".$lang['admin']['adminusers_add_success']."</p>";
			} else {
				$msg = "<p class='warnText'>".$lang['admin']['adminusers_add_failed']."</p>";
			}
		}

	}
}

if(!isset($_GET['mode'])){

	// make sql query
	if(isset($_GET['edit']) && (int)$_GET['edit']>0){
		$query = sprintf('SELECT * FROM '.$glob['dbprefix'].'CubeCart_admin_users WHERE `adminId` = %s', $db->mySQLSafe((int)$_GET['edit']));
	} else {

		$query = 'SELECT * FROM '.$glob['dbprefix'].'CubeCart_admin_users ORDER BY `isSuper` DESC';
	}

	if(isset($_GET['page'])){

		$page = $_GET['page'];

	} else {

		$page = 0;

	}

	// query database
	$results = $db->select($query, $rowsPerPage, $page);
	$numrows = $db->numrows($query);
	$pagination = paginate($numrows, $rowsPerPage, $page, 'page');
}

require($glob['adminFolder'].CC_DS.'includes'.CC_DS.'header.inc.php');
?>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td nowrap='nowrap'><p class="pageTitle"><?php echo $lang['admin']['adminusers_administrators_title'];?></p></td>
     <?php if(!isset($_GET['mode']) && permission('users','add')){ ?><td align="right" valign="middle"><a href="<?php echo $glob['adminFile']; ?>?_g=adminusers/administrators&amp;mode=new" class="txtLink"><img src="<?php echo $glob['adminFolder']; ?>/images/buttons/new.gif" alt="" hspace="4" border="0" title="" /><?php echo $lang['admin_common']['add_new'];?></a></td><?php } ?>
  </tr>
</table>
<?php
if(isset($msg))
{
	echo msg($msg);
}

if(!isset($_GET['mode']) && !isset($_GET['edit'])){
?>
<p class="copyText"><?php echo $lang['admin']['adminusers_current_users'];?></p>
<p class="copyText"><?php echo $pagination; ?></p>
<table width="100%"  border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
  	<td class="tdTitle"><?php echo $lang['admin']['adminusers_id'];?></td>
    <td class="tdTitle"><?php echo $lang['admin']['adminusers_user_notes'];?></td>
	<td align="center" class="tdTitle"><?php echo $lang['admin']['adminusers_no_logins'];?></td>
    <td align="center" class="tdTitle"><?php echo $lang['admin']['adminusers_super_user'];?></td>
	<td align="center" class="tdTitle"><?php echo $lang['admin']['adminusers_email'];?></td>
	<td align="center" class="tdTitle"><?php echo $lang['admin']['adminusers_action'];?></td>
  </tr>
<?php
for($i = 0, $maxi = count($results); $i < $maxi; ++$i) {

	$cellColor = '';
	$cellColor = cellColor($i);
?>
  <tr>
  	<td class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['adminId']; ?>.</span></td>
    <td class="<?php echo $cellColor; ?>"><span class="copyText"><strong><?php echo $results[$i]['username']; ?></strong><?php if(!empty($results[$i]['notes'])) { echo ' - '.$results[$i]['notes']; } ?></span></td>
    <td align="center" class="<?php echo $cellColor; ?>"><span class="copyText"><?php echo $results[$i]['noLogins']; ?></span></td>
    <td align="center" class="<?php echo $cellColor; ?>"><img src="<?php echo $glob['adminFolder']; ?>/images/<?php echo $results[$i]['isSuper']; ?>.gif" alt="" title="" /></td>
	    <td align="center" class="<?php echo $cellColor; ?>"><a href="mailto:<?php echo $results[$i]['ipAddress']; ?>" class="txtLink"><?php echo $results[$i]['email']; ?></a></td>
	    <td align="center" class="<?php echo $cellColor; ?>">
		<?php if(permission('users','edit')){ ?>
		<a href="<?php echo $glob['adminFile']; ?>?_g=adminusers/administrators&amp;edit=<?php echo $results[$i]['adminId']; ?>" class="txtLink"><?php echo $lang['admin_common']['edit']; ?></a> /
		<?php }  if(permission('users','delete')) { ?>
		<a href="<?php echo $glob['adminFile']; ?>?_g=adminusers/administrators&amp;delete=<?php echo $results[$i]['adminId']; ?>" onclick="return confirm('<?php echo str_replace("\n", '\n', addslashes($lang['admin_common']['delete_q'])); ?>')" class="txtLink"><?php echo $lang['admin_common']['delete']; ?></a>
		<?php } if(permission('users','edit') && $results[$i]['isSuper']==0) { ?>
		 / <a href="<?php echo $glob['adminFile']; ?>?_g=adminusers/permissions&amp;adminId=<?php echo $results[$i]['adminId']; ?>" class="txtLink"><?php echo $lang['admin']['adminusers_permissions'];?></a> <?php } ?></td>
  </tr>
<?php } ?>

</table>
<p class="copyText"><?php echo $pagination; ?></p>


<?php
} elseif($_GET['mode']=='new' || (int)$_GET['edit']>0){

if(isset($_GET['edit']) && (int)$_GET['edit']>0){ $modeTxt = $lang['admin_common']['edit']; } else { $modeTxt = $lang['admin_common']['add']; }
?>
<p class="copyText"><?php echo $lang['admin']['adminusers_add_admin'];?></p>
<form action="<?php echo $glob['adminFile']; ?>?_g=adminusers/administrators" method="post" enctype="multipart/form-data" name="form1">
<table border="0" cellspacing="1" cellpadding="3" class="mainTable">
  <tr>
    <td colspan="2" class="tdTitle"><?php if(isset($_GET['edit']) && (int)$_GET['edit']>0){ echo $modeTxt; } else { echo $modeTxt;  }  echo ' '.$lang['admin']['adminusers_administrator']; ?></td>
  </tr>
  <tr>
    <td width="25%" class="tdText"><strong><?php echo $lang['admin']['adminusers_full_name']; ?></strong></td>
    <td>
      <input name="name" type="text" class="textbox" value="<?php if(isset($results[0]['name'])) echo $results[0]['name']; ?>" maxlength="255" />
    </td>
  </tr>
  <tr>
    <td width="25%" class="tdText"><strong><?php echo $lang['admin']['adminusers_username']; ?></strong><br />
</td>
    <td><input name="adminUsername" type="text" class="textbox" value="<?php if(isset($results[0]['username'])) echo $results[0]['username']; ?>" maxlength="255" /></td>
  </tr>
  <tr>
    <td width="25%" class="tdText"><strong><?php echo $lang['admin']['adminusers_email2']; ?></strong></td>
    <td><input name="email" value="<?php if(isset($results[0]['email'])) echo $results[0]['email']; ?>" type="text" class="textbox" /></td>
  </tr>
  <tr>
    <td class="tdText"><strong><?php echo $lang['admin']['adminusers_password']; ?></strong><br />
      <?php echo $lang['admin']['adminusers_pass_warn']; ?></td>
    <td class="tdText"><input type="password" autocomplete="off" name="adminPassword" class="textbox" /></td>
  </tr>
  <tr>
    <td class="tdText"><strong><?php echo $lang['admin']['adminusers_confirm_pass']; ?></strong></td>
    <td class="tdText"><input type="password" autocomplete="off" name="adminPassword_verify" class="textbox" /></td>
  </tr>
  <tr>
    <td class="tdText"><?php echo $lang['admin']['adminusers_make_super']; ?></td>
    <td class="tdText">
<?php echo $lang['admin_common']['yes']; ?>
<input name="isSuper" type="radio" value="1" <?php if(isset($results[0]['isSuper']) && $results[0]['isSuper']==1) { echo 'checked="checked"'; } ?> />
<?php echo $lang['admin_common']['no']; ?>
<input name="isSuper" type="radio" value="0" <?php if(isset($results[0]['isSuper']) && $results[0]['isSuper']==0) echo 'checked="checked"';  if(isset($_GET['mode']) && $_GET['mode']=='new') { echo 'checked="checked"'; } ?> /></td>
  </tr>
  <tr>
    <td align="left" valign="top" class="tdText"><strong><?php echo $lang['admin']['adminusers_notes']; ?></strong></td>
    <td><textarea name="notes" class="textbox" cols="60" rows="3" id="notes"><?php if(isset($results[0]['notes'])) echo $results[0]['notes']; ?></textarea></td>
  </tr>
  <tr>
    <td width="25%">&nbsp;</td>
    <td>
	<input type="hidden" name="adminId" value="<?php  if(isset($results[0]['adminId'])) echo $results[0]['adminId']; ?>" />
	<input name="Submit" type="submit" class="submit" value="<?php if(isset($_GET['edit']) && (int)$_GET['edit']>0){ echo $modeTxt; } else { echo $modeTxt;  } ?> User" /></td>
  </tr>
</table>
</form>
<?php
}
?>