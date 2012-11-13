<div style="margin-bottom: 500px;">
<?php
	if(substr(PHP_OS, 0, 3) == "WIN") {
		$osFlav = "Windows";
		$OS = TRUE;
	} elseif(PHP_OS=="Darwin"){
		$osFlav = "Apple Macintosh";
		$OS = TRUE;
	} elseif(PHP_OS=="Linux" || PHP_OS=="BSD" || PHP_OS=="FreeBSD" || PHP_OS=="NetBSD" || PHP_OS=="HP-UX" || PHP_OS=="OpenBSD" || PHP_OS=="SunOS") {
		$osFlav = "Linux / Unix";
		$OS = TRUE;
	}
if($OS == TRUE){
?>
<p style="padding-bottom: 20px;">
<?php echo $lang['setup']['opDectResultTrue']; ?> <strong><?php echo $osFlav; ?></strong>.
</p>
<?php 
} else { 
?>
<p style="padding-bottom: 20px;">
<?php echo $lang['setup']['opDectResultFalse']; ?>
</p>
<?php 
} 
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="2">
    <tr align="center">
      <td width="33%"><a href="?os=linux&amp;l=<?php echo $_GET['l']; ?>"><img src="../images/linux.gif" alt="" width="17" height="20" border="0" title="" /></a></td>
      <td width="33%"><a href="?os=win&amp;l=<?php echo $_GET['l']; ?>"><img src="../images/windows.gif" alt="" width="22" height="20" border="0" title="" /></a></td>
      <td width="33%"><a href="?os=mac&amp;l=<?php echo $_GET['l']; ?>"><img src="../images/apple.gif" alt="" width="17" height="20" border="0" title="" /></a></td>
    </tr>
    <tr align="center">
      <td width="33%"><a href="?os=linux&amp;l=<?php echo $_GET['l']; ?>">Linux / Unix</a></td>
      <td width="33%"><a href="?os=win&amp;l=<?php echo $_GET['l']; ?>">Windows</a></td>
      <td width="33%"><a href="?os=mac&amp;l=<?php echo $_GET['l']; ?>">Apple Macintosh</a></td>
    </tr>
    <tr align="center">
      <td>
	  <?php 
		if($osFlav == "Linux / Unix"){
		?>
			<img src='../images/arrowBlink.gif' alt='<?php echo $lang['setup']['suggestedOS']; ?>' title='<?php echo $lang['setup']['suggestedOS']; ?>' />
		<?php
		}
		?>
	  </td>
      <td>
	  <?php
	  if($osFlav == "Windows"){
	  ?>
			<img src='../images/arrowBlink.gif' alt='<?php echo $lang['setup']['suggestedOS']; ?>' title='<?php echo $lang['setup']['suggestedOS']; ?>' />
	  <?php
		}
	  ?>
	  </td>
      <td>
	  <?php
	  if($osFlav == "Apple Macintosh"){
			?>
		<img src='../images/arrowBlink.gif' alt='<?php echo $lang['setup']['suggestedOS']; ?>' title='<?php echo $lang['setup']['suggestedOS']; ?>' />
<?php
		}
	  ?>
	  </td>
    </tr>
  </table>
</div>