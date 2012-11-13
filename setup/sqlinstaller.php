<?php

$sucessCount = 0;
$queryCount = 0;

//$sqlfile = ($_GET['sqlfile']) ? $_GET['sqlfile'] : 'upgrade/schema.sql';
if (!$sqlfile) die("No SQL file defined!");

//if (isset($_GET['prefix'])) $dbprefix = $_GET['prefix'];
if (!isset($dbprefix)) die("No database prefix defined!");

@ini_set('auto_detect_line_endings', true);

$sql = file_get_contents(dirname(__FILE__).CC_DS.$sqlfile);

if (!empty($dbprefix)) $sql = str_replace('`CubeCart_', '`'.$dbprefix.'CubeCart_', $sql);
$sql = str_replace('{this-database}', $dbprefix.$dbname, $sql);

$queryArray = explode('; #EOQ', $sql);
foreach ($queryArray as $query) {
	$query = trim($query);
	if (!empty($query)) { ## && !preg_match('/^#/iU', $query)) {
		## detect line endings, and split
		if (preg_match('#^(ALTER)#iuxmU', trim($query))) {
			$queryLines = explode("\n", trim($query));
			for ($i=0; $i<count($queryLines); $i++) {
				if ($i==0) {
					if (count($queryLines) == 1) {
						if ($db->misc($queryLines[0], false)) {
							$successCount++;
							$querylog['success'][] = $queryLines[0];
						} else {
							$querylog['fail'][] = $queryLines[0];
						}
					} else {
						$prefix = $queryLines[0];
					}
				} else {
					$queryCount++;
					$queryTemp = sprintf('%s %s', $prefix, preg_replace('#,$#iu', ';', $queryLines[$i]));
					if ($db->misc($query, false)) {
						$successCount++;
						$querylog['success'][] = $query;
					} else {
						$querylog['fail'][] = $query;
					}
					unset($queryTemp);
				}
			}
		} else {
			$queryCount++;
			$query = str_replace(array("\n", "\r"), '', trim($query)).';';
			
			if ($db->misc($query, false)) {
				$successCount++;
				$querylog['success'][] = $query;
			} else {
				$querylog['fail'][] = $query;
			}
		}
	}
}

$updateString = sprintf('Successfully executed %d of %d queries.', $successCount, $queryCount);

?>