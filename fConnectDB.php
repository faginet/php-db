<?php
	#########################
	#	   fConnectDB		#
	#	Author: Beknazar	# 
	#	Date: 2016.12.06	#
	#	work PDO class		#
	#	std::function		#
	#########################
	class connection{
		const ATTR_ERRMODE = 2;
		const ATTR_FETCH = 0;
		
		private static $attr = array(
			'ATTR_ERRMODE' => array(PDO::ERRMODE_SILENT, PDO::ERRMODE_WARNING, PDO::ERRMODE_EXCEPTION),
			'ATTR_FETCH' => array(PDO::FETCH_ASSOC, PDO::FETCH_BOTH, PDO::FETCH_BOUND, PDO::FETCH_CLASS, PDO::FETCH_INTO, PDO::FETCH_LAZY, PDO::FETCH_NUM, PDO::FETCH_OBJ)
		);
		
		public static function connect(){
			try {
				# config connect to DB
				$config = array(
					'typeDB' => 'mysql', # mysql driver
					"host" => 'localhost', # host 
					"dbname" => 'you_db_name', 
					"user" => 'you_db_username', 
					"pass" => 'you_db_password'
				);
				# config
				$dsn = $config['typeDB'].":host=".$config['host'].";dbname=".$config['dbname'];
				$opt = array(
					PDO::ATTR_ERRMODE	=> self::$attr['ATTR_ERRMODE'][self::ATTR_ERRMODE],
					PDO::ATTR_DEFAULT_FETCH_MODE => self::$attr['ATTR_FETCH'][self::ATTR_FETCH],
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
				);
				return new PDO($dsn, $config['user'], $config['pass'], $opt);
			}  
			catch(PDOException $e) {  
				echo $e->getMessage();  
			}
		}
	}
	
	class DB extends connection{	
		public static $DBH;
		
		# std construct this connect DB
		function __construct(){
			$this->DBH = parent::connect();
		}# end construct
		
		# start static function
		public static function setup(){
			self::$DBH = parent::connect();
			return self::$DBH;
		}# end start static function 
		
		# order by add sql query
		public static function orderBy($f, $n=''){
			if (!empty($f)){
				switch($n){
					case 0: $s = "ORDER BY `$f` ASC";
						break;
					case 1: $s = "ORDER BY `$f` DESC";
						break;
					default: $s = '';
				} 
				return $s;
			}
			return '';
		}# end orderBy sql query
		
		#check array of data
		public static function checkArray($data){
			return $a = (!empty($data) && is_array($data)) ? 1 : 0;
		}# end check this array
		
		# выборка количества строк
		public static function getCount($name_table){
			return array_shift((!empty($name_table)) ? self::$DBH->query("SELECT COUNT(*) as total FROM `$name_table`")->fetchAll() : 'Error: empty name_table');
		}# end select count
		
		# выборка опр. данных
		public static function getData($name_table, $data='', $f='', $n=''){
			$orderBy = (!empty($f)) ? self::orderBy($f, $n) : '';
			$sql = (self::checkArray($data)) ? '`'.implode("`, `", $data).'`' : (empty($data)) ? '*': $data;
			$sh = (!empty($name_table)) ? self::$DBH->query("SELECT $sql FROM `$name_table` $orderBy")->fetchAll() : 'Error: empty name_table';
			return $arr = (count($sh) > 1) ? $sh : array_shift($sh);
		}# end select data
		
		# выборка всех полей по условиям WHERE
		public static function getDataWhere($name_table, $data, $operand, $f='', $n=''){
			$orderBy = (!empty($f)) ? self::orderBy($f, $n) : '';
			if (empty($operand)){ return 'Error: empty the operand'; }
			if (self::checkArray($data)){
				if (empty($name_table)){ return 'Error: empty name table'; }
				$sh = self::$DBH->prepare("SELECT * FROM `$name_table` WHERE `". key($data) ."` $operand ? $orderBy");
				$sh->execute(array_values($data));
				$sh = $sh->fetchAll();
				return (count($sh) > 1) ? $sh : array_shift($sh);
			} else {
				return 'Error: empty data';
			}	
		}# end select where
		
		# выборка всех полей по условиям WHERE IN
		public static function getDataWhereIN($name_table, $fields, $data, $f='', $n=''){
			$orderBy = (!empty($f)) ? self::orderBy($f, $n) : '';
			if (self::checkArray($data)){
				if (empty($name_table)) return 'Error: empty name table'; 
				if (empty($fields)) return 'Error: empty fields'; 
				$sh = self::$DBH->prepare("SELECT * FROM `$name_table` WHERE `$fields` IN (". rtrim(str_repeat('?,', count($data)), ',') .") $orderBy");
				$sh->execute(array_values($data));
				$sh = $sh->fetchAll();
				return (count($sh) > 1) ? $sh : array_shift($sh);
			} else {
				return 'Error: empty data';
			}	
		}# end select where IN
		
		# insert into
		public static function insertData($name_table, $data){
			if (self::checkArray($data)){
				return (!empty($name_table)) ? self::$DBH->prepare("INSERT INTO `$name_table`(`". implode("`, `", array_keys($data))."`) VALUES (". rtrim(str_repeat('?,', count($data)), ',') .")")->execute(array_values($data)) : 'Error: empty name table';
			} else {
				return 'Error: empty data';
			}	
		}# end insert		
		
		# delete from
		public static function deleteFrom($name_table){
			return (!empty($name_table)) ? self::$DBH->prepare("DELETE FROM `$name_table`")->execute() : 'Error: empty name table';
		} # end delete
		
		# delete from where
		public static function deleteFromWhere($name_table, $data, $operand){
			if (empty($operand)){ return 'Error: empty the operand'; }
			if (self::checkArray($data)){
				return (!empty($name_table)) ? self::$DBH->prepare("DELETE FROM `$name_table` WHERE `". key($data) ."` $operand ? ")->execute(array_values($data)) : 'Error: empty name table';
			} else {
				return 'Error: empty data';
			}
		}# end delete From
		
		# delete from where IN
		public static function deleteFromWhereIN($name_table, $fields, $data){
			if (empty($fields)) return 'Error: empty fields'; 
			if (self::checkArray($data)){
				return (!empty($name_table)) ? self::$DBH->prepare("DELETE FROM `$name_table` WHERE `$fields` IN (". rtrim(str_repeat('?,', count($data)), ',') .") ")->execute(array_values($data)) : 'Error: empty name table';
			} else {
				return 'Error: empty data';
			}
		}# end delete From IN
		
		# update
		public static function update($name_table, $data){
			if (self::checkArray($data)){
				return (!empty($name_table)) ? self::$DBH->prepare("UPDATE `$name_table` SET `".implode("`=?, `", array_keys($data))."`=?")->execute(array_values($data)) : 'Error: empty name table';
			} else {
				return 'Error: empty data';
			}
		}# end update 
		
		# update where ??
		public static function updateWhere($name_table, $data, $where, $operand){
			if (empty($operand)){ return 'Error: empty the operand'; }
			if (self::checkArray($data)){
				if (self::checkArray($where)){
					return (!empty($name_table)) ? self::$DBH->prepare("UPDATE `$name_table` SET `".implode("`=?, `", array_keys($data))."`=?  WHERE `". key($where) ."` $operand ?")->execute(array_merge( array_values($data), array_values($where) )) : 'Error: empty name table';
				} else {
					return 'Error: empty where';
				}
			} else {
				return 'Error: empty data';
			}
		}# end update where ???
		
		# update IN
		public static function updateWhereIN($name_table, $data, $fields){
			if (self::checkArray($data)){
				if (self::checkArray($fields)){
					return self::$DBH->prepare("UPDATE `$name_table` SET `".implode("`=?, `", array_keys($data))."`=?  WHERE `". key($fields) ."` IN (". rtrim(str_repeat('?,', count($fields)), ',') .")")->execute( array_merge( array_values($data), array_values($fields) ));
				} else {
					return 'Error: empty fields';
				}
			} else {
				return 'Error: empty data';
			}
		}# end update IN
		
		# destruct DB connection
		function __destruct(){
			$this->DBH = null;
		}# end destruct DB
		
	}# end class db 
	
?>