<?php

function GetSchool($sch)
{	global $_openSIS;
	
	if(!$_openSIS['GetSchool'])
	{
		$QI=DBQuery("SELECT ID,TITLE FROM SCHOOLS");
		$_openSIS['GetSchool'] = DBGet($QI,array(),array('ID'));
	}

	if($_openSIS['GetSchool'][$sch])
		return $_openSIS['GetSchool'][$sch][1]['TITLE'];
	else
		return $sch;
}
?>
