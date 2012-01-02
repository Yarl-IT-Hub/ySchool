<?php

function GetMP($mp,$column='TITLE')
{	global $_openSIS;

	// mab - need to translate marking_period_id to title to be useful as a function call from dbget
	// also, it doesn't make sense to ask for same thing you give
	if($column=='MARKING_PERIOD_ID')
		$column='TITLE';

	if(!$_openSIS['GetMP'])
	{
		$_openSIS['GetMP'] = DBGet(DBQuery("SELECT MARKING_PERIOD_ID,TITLE,POST_START_DATE,POST_END_DATE,'SCHOOL_QUARTERS'        AS `TABLE`,SORT_ORDER,SHORT_NAME,START_DATE,END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS FROM SCHOOL_QUARTERS         WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'
					UNION      SELECT MARKING_PERIOD_ID,TITLE,POST_START_DATE,POST_END_DATE,'SCHOOL_SEMESTERS'       AS `TABLE`,SORT_ORDER,SHORT_NAME,START_DATE,END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS FROM SCHOOL_SEMESTERS        WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'
					UNION      SELECT MARKING_PERIOD_ID,TITLE,POST_START_DATE,POST_END_DATE,'SCHOOL_YEARS'           AS `TABLE`,SORT_ORDER,SHORT_NAME,START_DATE,END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS FROM SCHOOL_YEARS            WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'
					UNION      SELECT MARKING_PERIOD_ID,TITLE,POST_START_DATE,POST_END_DATE,'SCHOOL_PROGRESS_PERIODS' AS `TABLE`,SORT_ORDER,SHORT_NAME,START_DATE,END_DATE,DOES_GRADES,DOES_EXAM,DOES_COMMENTS FROM SCHOOL_PROGRESS_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"),array(),array('MARKING_PERIOD_ID'));
	}
	if(substr($mp,0,1)=='E')
	{
		if($column=='TITLE' || $column=='SHORT_NAME')
			$suffix = ' Exam';
		$mp = substr($mp,1);
	}

	if($mp==0 && $column=='TITLE')
		return 'Full Year'.$suffix;
	else
		return $_openSIS['GetMP'][$mp][1][$column].$suffix;
}

function GetMPTable($mp_table)
{
	switch($mp_table)
	{
		case 'SCHOOL_YEARS':
			return 'FY';
		break;
		case 'SCHOOL_SEMESTERS':
			return 'SEM';
		break;
		case 'SCHOOL_QUARTERS':
			return 'QTR';
		break;
		case 'SCHOOL_PROGRESS_PERIODS':
			return 'PRO';
		break;
		default:
			return 'FY';
		break;
	}
}

function GetCurrentMP($mp,$date,$error=true)
{	global $_openSIS;

	switch($mp)
	{
		case 'FY':
			$table = 'SCHOOL_YEARS';
		break;

		case 'SEM':
			$table = 'SCHOOL_SEMESTERS';
		break;

		case 'QTR':
			$table = 'SCHOOL_QUARTERS';
		break;

		case 'PRO':
			$table = 'SCHOOL_PROGRESS_PERIODS';
		break;
	}

	if(!$_openSIS['GetCurrentMP'][$date][$mp])
	 	$_openSIS['GetCurrentMP'][$date][$mp] = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM $table WHERE '$date' BETWEEN START_DATE AND END_DATE AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));

	if($_openSIS['GetCurrentMP'][$date][$mp][1]['MARKING_PERIOD_ID'])
		return $_openSIS['GetCurrentMP'][$date][$mp][1]['MARKING_PERIOD_ID'];
	elseif(strpos($_SERVER['PHP_SELF'],'Side.php')===false && $error==true)
		ErrorMessage(array("You are not currently in a marking period"));
		//ShowErr("You are not currently in a marking period");
}
?>