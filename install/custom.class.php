<?php
error_reporting(0);
class custom{
var $customQuery=array();
var $customQueryString=array();
function __construct($mysql_database){
mysql_connect($_SESSION['server'],$_SESSION['username'],$_SESSION['password']) or die() ;
mysql_select_db($mysql_database);
}
function set($res,$table){
	$res=mysql_query($res);

	while($row=mysql_fetch_assoc($res))
	{	
	 $this->customQuery[]=$row ;
	}
	
	foreach($this->customQuery as $value){
	$str="ALTER TABLE $table ADD $value[Field] $value[Type]";
	if($value['Null']=='YES'){
	$str.=" NULL ";
	}else if($value['Null']=='NO'){
	$str.=" NOT NULL ";
	}
	if($value['Default']){
	$str.=" DEFAULT '".$value['Default']."' ";
	}
	$this->customQueryString[]=$str;
	}
	
}
 function __destruct() {
       mysql_close();
   }
}

 ?>