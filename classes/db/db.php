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
|	db.php
|   ========================================
|	Database Class
+--------------------------------------------------------------------------
*/

if (class_exists('db')) {
	return;
}

class db {

	var $query;
	var $db;
	var $queryArray = array();
	var $showError = true;
	var $magic = null;
	var $escape = null;

	function db() {
		global $glob;

		$this->db = mysql_connect($glob['dbhost'], $glob['dbusername'], $glob['dbpassword']) or die(mysql_error());
		if (!$this->db) die($this->debug(true));

		$selectdb = mysql_select_db($glob['dbdatabase'], $this->db);
		if (!$selectdb) die ($this->debug());

	}

	private function exec($query) {
		if (isset($GLOBALS['config']['debug']) && $GLOBALS['config']['debug']) {
			## Only populate the queryArray if the debugger is enabled
			$this->queryArray[] = $query;
		}
		return mysql_query($query);
	}

	function select($query, $maxRows = 0, $pageNum = 0) {
		$this->query = $query;
		## start limit if $maxRows is greater than 0
		if ($maxRows > 0) {
			$startRow = $pageNum * $maxRows;
			$query = sprintf('%s LIMIT %d, %d', $query, $startRow, $maxRows);
		}
		$result = $this->exec($query);

		if ($this->error()) die ($this->debug());

		$max = mysql_num_rows($result);
		if ($max > 0) {
			for ($n = 0; $n < $max; ++$n) {
				$row = mysql_fetch_assoc($result);
				$output[$n] = $row;
			}
			return $output;
		} else {
			return false;
		}
	}

	function misc($query, $debug = true) {
		$this->query = $query;
		$result = $this->exec($query);
		if ($this->error() && $debug) die ($this->debug());
		return ($result) ? true : false;
	}

	function numrows($query) {
		// replace * with count(*) to optimize BUGGY so removed for now

		// if(stripos($query,"*",1)) {
		// 	$query = preg_replace("/SELECT */","SELECT count(*) as `count` ",$query);
		// 	$results = $this->select($query);
		// 	return $results[0]['count'];
		// } else {	
			$this->query = $query;
			$result = $this->exec($query);
			return mysql_num_rows($result);
		// }
		
	}

	function getRows($query) {
		$this->query = $query;
		$result = $this->exec($query);
		$tables = array();
		while (($row = mysql_fetch_row($result)) !== false) {
			$tables[] = $row;
		}
		return $tables;
	}

	function insert($tablename, $record) {
		if (!is_array($record)) die($this->debug('array', 'Insert', $tablename));

		foreach ($record as $field => $value) {
			$fields[] = "`$field`";
			$values[] = (string)$value;
		}

		if (strpos($tablename, '`') === false) {
			$tablename = '`'.$tablename.'`';
		}

		$this->query = sprintf('INSERT INTO %s (%s) VALUES (%s);', $tablename, implode(',', $fields), implode(',', $values));

		$this->exec($this->query);
		if ($this->error()) die ($this->debug());
		return ($this->affected() > 0) ? true : false;
	}

	function update($tablename, $record, $where = '') {
		if(!is_array($record)) die ($this->debug('array', 'Update', $tablename));

		foreach ($record as $field => $value) {
			$set[] = "`$field` = $value";
		}

		if(!empty($where)) {
			if (is_array($where)) {
				foreach ($where as $field => $value) {
					$whereArray[] = "`$field` = '$value'";
				}
				$where = 'WHERE '.implode(' AND ', $whereArray);
			} else {
				$where = 'WHERE '.$where;
			}
		}

		if (strpos($tablename, '`') === false) {
			$tablename = '`'.$tablename.'`';
		}

		$this->query = sprintf('UPDATE %s SET %s %s;', $tablename, implode(',', $set), $where);
		$this->exec($this->query);
		if ($this->error()) die ($this->debug());
		return ($this->affected() > 0) ? true : false;

	}

	function categoryNos($cat_id, $sign, $amount = 1) {
		global $glob;
		if ($cat_id > 0) {
			do {
				$record['noProducts'] = ' noProducts '.$sign.$amount;
				$where = '`cat_id` = '.$cat_id;
				$this->update($glob['dbprefix'].'CubeCart_category', $record, $where, '');
				$query = 'SELECT `cat_father_id` FROM '.$glob['dbprefix'].'CubeCart_category WHERE `cat_id` = '.$cat_id;
				$cfi = $this->select($query);
				$cat_id = $cfi['0']['cat_father_id'];
			}
			while ($cat_id > 0);
		}
	}

	function delete($tablename, $where = '', $limit = '') {

		if (!empty($where)) {
			if (is_array($where)) {
				foreach ($where as $field => $value) {
					$whereArray[] = "`$field` = '$value'";
				}
				$where = ' WHERE '.implode(' AND ', $whereArray);
			} else {
				$where = ' WHERE '.$where;
			}
		}

		if (strpos($tablename, '`') === false) {
			$tablename = '`'.$tablename.'`';
		}

		$query = 'DELETE FROM '.$tablename.$where;
		if (!empty($limit)) $query .= ' LIMIT ' . $limit;

		$this->query = $query;
		$this->exec($query);

		if ($this->error()) die ($this->debug());
		return ($this->affected() > 0) ? true : false;
	}

	function truncate($tablename) {
		if (strpos($tablename, '`') === false) {
			$tablename = '`'.$tablename.'`';
		}
		$this->query = 'TRUNCATE '.$tablename;
		$this->exec($this->query);
		if ($this->error()) die ($this->debug());
	}

	/*********************************************/
	## Clean SQL Variables (Security Function)
	/*********************************************/

	function mySQLSafe($value, $quote = "'") {
		//We are going to do this to keep the functions from contantly running
		if (empty($this->magic)) {
			$this->magic = (bool)get_magic_quotes_gpc();
		}
		if (empty($this->escape)) {
			if (function_exists('mysql_real_escape_string')) {
				$this->escape = 'mysql_real_escape_string';
			} else {
				$this->escape = 'mysql_escape_string';
			}
		}
		if (empty($value) && $value !== 0) {
    		return $quote.$quote;
		}

		## Stripslashes
		if ($this->magic) {
			$value = stripslashes($value);
		}
		## Strip quotes if already in
		$value = str_replace(array("\\'","'"), "&#39;", $value);

		## Quote value
		if ($this->escape == 'mysql_real_escape_string' && !empty($this->db)) {
			$value = mysql_real_escape_string($value, $this->db);
		} else {
			$value = mysql_escape_string($value);
		}
		$value = $quote . trim($value) . $quote;

		return $value;
	}

	function sqldumptable($tableData, $drop, $structure, $data) {

		$table = $tableData[0];

		$tabledump = '';
		if ($drop && $structure) {
			$tabledump .= "-- --------------------------------------------------------\n\nDROP TABLE IF EXISTS `".$table."`;\n\n";
		}
		if ($structure) {
			$tabledump .= "-- --------------------------------------------------------\n\n-- \n-- Table structure for table `".$table."`\n--\n\nCREATE TABLE `".$table."` (\n";
			$firstfield = true;
			$query = 'SHOW FIELDS FROM '.$table;
			$this->query = $query;
			## get columns and spec
			$fields = mysql_query($query);

			while (($field = mysql_fetch_array($fields)) !== false) {

				if (!$firstfield) {
					$tabledump .= ",\n";
				} else {
					$firstfield = 0;
				}
				$tabledump .= '   `'.$field['Field'].'` '.$field['Type'];

				$defaultValue = ($field['Default']=='CURRENT_TIMESTAMP') ? $field['Default'] : "'".$field['Default']."'";

				if (!empty($field['Default'])) { $tabledump .= ' DEFAULT '.$defaultValue; }
				if ($field['Null'] != 'YES') $tabledump .= ' NOT NULL';
				if (!empty($field['Extra']))  $tabledump .= ' '.$field['Extra'];
			}
			mysql_free_result($fields);

			## get keys list
			$keys = mysql_query('SHOW KEYS FROM '.$table);
			$index = array();
			while (($key = mysql_fetch_array($keys)) !== false) {
				$kname = $key['Key_name'];
				if ($kname != 'PRIMARY' && !$key['Non_unique']) $kname='UNIQUE|'.$kname;
				if (!is_array($index[$kname])) $index[$kname] = array();
				$index[$kname][] = $key['Column_name'];
			}
			mysql_free_result($keys);

			## get each key info
			while ((list($kname, $columns) = @each($index)) !== false) {
				$tabledump .= ",\n";
				$colnames = implode($columns,'`,`');

				if ($kname == 'PRIMARY') {
					// do primary key
					$tabledump .= ' PRIMARY KEY (`'.$colnames.'`)';
				} elseif(strtoupper($kname) == 'FULLTEXT') {
					$tabledump .= ' FULLTEXT KEY `fulltext` (`'.$colnames.'`)';
				} else {
					// do standard key
					if (substr($kname,0,6) == 'UNIQUE') {
						// key is unique
						$kname=substr($kname,7);
					}
					$tabledump .= ' KEY `'.$kname.'` (`'.$colnames.'`)';
				}
			}

			$tabledump .= "\n) ENGINE ".$tableData[1];

			$tabledump .= ' DEFAULT CHARSET=utf8';

			if($tableData[10]>0 ) {
				$tabledump .= ' AUTO_INCREMENT='.$tableData[10];
			}

			if(!empty($tableData[14])) {
				$tabledump .= ' COLLATE='.$tableData[14];
			}

			$tabledump .= " ;\n\n";
		}
		if ($data) {
			## get data
			$rows = mysql_query('SELECT * FROM '.$table);
			$numfields = mysql_num_fields($rows);
			if ($numfields > 0) $tabledump .="--\n-- Dumping data for table `".$table."`\n--\n\n";
			while (($row = mysql_fetch_array($rows)) !== false) {
				$tabledump .= 'INSERT INTO `'.$table.'` VALUES(';
				$fieldcounter = -1;
				$firstfield = true;

				## get each field's data
				while (++$fieldcounter<$numfields) {

					if (!$firstfield) {
						$tabledump.=', ';
					} else {
						$firstfield = 0;
					}
					if (!isset($row[$fieldcounter])) {
						$tabledump .= 'NULL';
					} else {
						$tabledump .= "'".mysql_escape_string($row[$fieldcounter])."'";
					}
				}
				$tabledump .= ");\n";
			}
			mysql_free_result($rows);
		}
		return $tabledump;
	}

	// This function has been built to prevent brute force attacks
	function blocker($user, $level, $time, $login, $loc) {
		global $glob;
		$expireTime = time()-($time*5);
		$this->delete($glob['dbprefix'].'CubeCart_blocker','`lastTime` < '.$expireTime);
		$query = "SELECT * FROM ".$glob['dbprefix']."CubeCart_blocker WHERE `browser` = ".$this->mySQLSafe($_SERVER['HTTP_USER_AGENT'])." AND `ip` = ".$this->mySQLSafe(get_ip_address())." AND `loc`= '".$loc."'";
		$blackList = $this->select($query);

		if ($blackList && $blackList[0]['blockTime']>time()) {
			// do nothing the user is still banned
			return true;
		} else if ($blackList && $blackList[0]['blockTime']>0 && $blackList[0]['blockTime']<time() && $blackList[0]['blockLevel'] == $level) {
			## delete the db row as user is no longer banned
			$this->delete($glob['dbprefix'].'CubeCart_blocker','`id` ='.$blackList[0]['id']);
			return false;
		} else if ($blackList && !$login && !$blackList[0]['blockTime']) {

			$newdata['lastTime'] = time();
			## If last attempt was more than the time limit ago we need to set the level to one
			## This stops a consecutive fail weeks later blocking on first attempt
			$timeAgo = time() - $time;
			$newdata['blockLevel'] = ($blackList[0]['lastTime']<$timeAgo) ? 1 : $blackList[0]['blockLevel']+1;

			if ($newdata['blockLevel']==$level) {
				$newdata['blockTime'] = time() + $time;
				$this->update($glob['dbprefix'].'CubeCart_blocker', $newdata, '`id` = '.$blackList[0]['id'],$stripQuotes='');
				return true;
			} else {
				$newdata['blockTime'] = 0;
				$this->update($glob['dbprefix'].'CubeCart_blocker', $newdata, '`id` = '.$blackList[0]['id'],$stripQuotes='');
				return false;
			}

		} else if (!$blackList && !$login) {
			## insert
			$newdata['blockLevel'] = 1;
			$newdata['blockTime'] = 0;
			$newdata['browser'] = $this->mySQLSafe($_SERVER['HTTP_USER_AGENT']);
			$newdata['ip'] = $this->mySQLSafe(get_ip_address());
			$newdata['username'] = $this->mySQLSafe($user);
			$newdata['loc'] = "'".$loc."'";
			$newdata['lastTime'] = time();
			$this->insert($glob['dbprefix'].'CubeCart_blocker', $newdata);
			return false;
		}
	}

	function debug($type = '', $action = '', $tablename = '') {
		if(!$this->showError) return 'Error reporting is disabled in the database class. Please enable to debug.';
		switch ($type) {
			case 'connect':
				$message = 'MySQL Error Occured';
				$result = mysql_errno() . ': ' . mysql_error();
				$query = '';
				$output = 'Could not connect to the database. Be sure to check that your database connection settings are correct and that the MySQL server in running.';
				break;
			case 'array':
				$message = $action.' Error Occured';
				$result = 'Could not update '.$tablename.' as variable supplied must be an array.';
				$query = '';
				$output = 'Sorry an error has occured accessing the database. Be sure to check that your database connection settings are correct and that the MySQL server in running.';
				break;
			default:
				if (mysql_errno($this->db)) {
					$message = 'MySQL Error Occurred';
					$result =  mysql_errno($this->db) . ': ' .  mysql_error($this->db);
					$output = 'Sorry an error has occured accessing the database. Be sure to check that your database connection settings are correct and that the MySQL server in running.';
				} else {
					$message = 'MySQL Query Executed Succesfully.';
					$result = mysql_affected_rows($this->db) . ' Rows Affected';
					$output = 'view logs for details';
				}
				$linebreaks = array("\n", "\r");
				$query = (!empty($this->query)) ?  "<strong>SQL:</strong><br /> " . str_replace($linebreaks, " ", $this->query) : '';
		}
		$output = "<h1 style='font-family: Arial, Helvetica, sans-serif; color: #0B70CE;'>".$message."</h1>\n<p style='font-family: Arial, Helvetica, sans-serif; color: #000000;'><strong>Error Message:</strong><br/>".$result."</p>\n";

		if (!empty($query)) $output .= "<p style='font-family: Courier New, Courier, mono; border: 1px dashed #666666; padding: 10px; color: #000000;'>".$query."</p>\n";
		return $output;
	}

	function getFulltextIndex($table = 'inventory', $prefix = false) {
		global $glob;

		if (is_array($table)) {
			foreach ($table as $name) {
				$fieldlist[$name] = $this->getFulltextIndex($name);
			}
		} else {
			$sql = sprintf('SHOW INDEX FROM %sCubeCart_%s;', $glob['dbprefix'], $table);
			$query = $this->exec($sql);
			while(($index = mysql_fetch_assoc($query)) !== false) {
				if ($index['Index_type'] == 'FULLTEXT' && $index['Key_name'] == 'fulltext') {
					if ($prefix) {
						$fieldlist[] = sprintf('%s.%s', $prefix, $index['Column_name']);
					} else {
						$fieldlist[] = $index['Column_name'];
					}
				}
			}
		}
		return $fieldlist;
	}

	function getSearchWordLen() {
		if (($query = $this->select("SHOW VARIABLES LIKE 'ft_min_word_len'")) !== false) {
			if (isset($query[0]['Value']) && is_numeric($query[0]['Value'])) {
				return (int)$query[0]['Value'];
			}
		}
		return 4;
	}

	function serverVersion() {
		return mysql_get_server_info($this->db);
	}

	function error() {
		return (mysql_errno($this->db))? true : false;
	}
	function errorstring() {
		return mysql_error($this->db);
	}

	function insertid() {
		return mysql_insert_id($this->db);
	}

	function affected() {
		return mysql_affected_rows($this->db);
	}

	function close() {
		mysql_close($this->db);
	}

	## New for 4.1.x
	function getFields($table) {
		global $glob;
		$list = mysql_list_fields($glob['dbdatabase'], $table, $this->db);
		$cols = mysql_num_fields($list);
		for ($i = 0; $i < $cols; ++$i) {
			$array	= (array) mysql_fetch_field($list, $i);
			$return[$array['name']] = $array['name'];
		}
		return $return;
	}
}
?>