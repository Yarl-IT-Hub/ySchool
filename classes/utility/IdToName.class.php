<?php 
class IdToName{
private $properties = array();
var $mysqli;
function __get($property)
{
return $this->properties[$property];
}
function __set($property, $value)
{
$this->properties[$property]=$value;
}
function name(){
		if(isset($stmt)){
			unset($stmt);
			}
			include("classes/db/db.mysqli.class.php");
			$stmt = $mysqli->prepare("select $this->fetchTo from $this->table
									  where $this->fetchFrom=$this->id limit 1");
				
				$stmt->execute();
				$title='';
				$stmt->bind_result($title);
				$stmt->fetch();
				unset($stmt);
				$mysqli->close();
				unset($mysqli);
				return $title;	
			}
}
?>