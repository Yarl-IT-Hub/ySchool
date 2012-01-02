<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. It is  web-based, 
#  open source, and comes packed with features that include student 
#  demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  Copyright (C) 2007-2008, Open Solutions for Education, Inc.
#
#*************************************************************************
#  This program is free software: you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation, version 2 of the License. See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#**************************************************************************
include('../../Redirect_modules.php');
if(!$_REQUEST['modfunc'])
{
	$start_date = '01-'.strtoupper(date('M-y'));
        $end_date = DBDate();
        echo "<br><FORM name=log id=log action=Modules.php?modname=$_REQUEST[modname]&modfunc=generate method=POST>";
        PopTable('header','Log Details');
	echo '<div align=center style="padding-top:20px; font-size:12px;"><strong>Please Select Date Range</strong></div>
	<TABLE border=0 width=100% align=center><tr><TD valign=top style=padding-top:14px;>';
	echo '<strong>From :</strong> </TD><TD valign=middle>';
	DrawHeader(PrepareDate($start_date,'_start'));
	echo '</TD><TD valign=top style=padding-top:14px;><strong>To :</strong> </TD><TD valign=middle>';
	DrawHeader(PrepareDate($end_date,'_end'));
	echo '</TD></TR></TABLE><div style=height:10px></div>';
	echo '<center><input type="submit" class=btn_medium value="Generate" name="generate"></center>';
	PopTable('footer');
	echo '</FORM>';
}


         if($_REQUEST['day_start'] && $_REQUEST['month_start'] && $_REQUEST['year_start'])
	{
		$start_date = $_REQUEST['day_start'].'-'.$_REQUEST['month_start'].'-'.substr($_REQUEST['year_start'],2,4);
		$org_start_date = $_REQUEST['day_start'].'-'.$_REQUEST['month_start'].'-'.$_REQUEST['year_start'];

		 $conv_st_date = con_date($org_start_date);
	}

	if($_REQUEST['day_end'] && $_REQUEST['month_end'] && $_REQUEST['year_end'])
	{
		$end_date = $_REQUEST['day_end'].'-'.$_REQUEST['month_end'].'-'.substr($_REQUEST['year_end'],2,4);
		$org_end_date = $_REQUEST['day_end'].'-'.$_REQUEST['month_end'].'-'.$_REQUEST['year_end'];

		 $conv_end_date = con_date_end($org_end_date);
	}
if($_REQUEST['modfunc']=='generate')
{

   

 if(isset($conv_st_date) && isset($conv_end_date))
	{
	$alllogs_RET = DBGet(DBQuery("SELECT DISTINCT FIRST_NAME,LAST_NAME,LOGIN_TIME,PROFILE,FAILLOG_COUNT,FAILLOG_TIME,USER_NAME,IP_ADDRESS,STATUS FROM LOGIN_RECORDS WHERE LOGIN_TIME >='".$conv_st_date."' AND LOGIN_TIME <='".$conv_end_date."' AND SCHOOL_ID=".UserSchool()." OR STATUS='Failed'  ORDER BY LOGIN_TIME DESC"));
		

		if(count($alllogs_RET))
		{
			echo '<div>';
			#ListOutput($alllogs_RET,array('LOGIN_TIME'=>'Login Time','USER_NAME'=>'User Name','FIRST_NAME'=>'First Name','LAST_NAME'=>'Last Name','FAILLOG_COUNT'=>'Failure Count','FAILLOG_TIME'=>'Failed Login Time','STATUS'=>'Status','IP_ADDRESS'=>'IP Address'),'successfull login','successfull logins',array(),array(),array('count'=>true,'save'=>true));
			
		ListOutput($alllogs_RET,array('LOGIN_TIME'=>'Login Time','USER_NAME'=>'User Name','FIRST_NAME'=>'First Name','LAST_NAME'=>'Last Name','FAILLOG_COUNT'=>'Failure Count','STATUS'=>'Status','IP_ADDRESS'=>'IP Address'),'login record','login records',array(),array(),array('count'=>true,'save'=>true));

			echo '</div>';
		}
		else
		{
		
		echo '<table border=0 width=90%><tr><td class="alert"></td><td class="alert_msg"><b>No login records were found.</b></td></tr></table>';
		
		}
}
if((!isset($conv_st_date) || !isset($conv_end_date)))
	{
		echo '<center><font color="red"><b>You have to select date from the date range</b></font></center>';

	}
     
}

function con_date($date)
{
	$mother_date = $date;
	$year = substr($mother_date, 7);
	$temp_month = substr($mother_date, 3, 3);

		if($temp_month == 'JAN')
			$month = '01';
		elseif($temp_month == 'FEB')
			$month = '02';
		elseif($temp_month == 'MAR')
			$month = '03';
		elseif($temp_month == 'APR')
			$month = '04';
		elseif($temp_month == 'MAY')
			$month = '05';
		elseif($temp_month == 'JUN')
			$month = '06';
		elseif($temp_month == 'JUL')
			$month = '07';
		elseif($temp_month == 'AUG')
			$month = '08';
		elseif($temp_month == 'SEP')
			$month = '09';
		elseif($temp_month == 'OCT')
			$month = '10';
		elseif($temp_month == 'NOV')
			$month = '11';
		elseif($temp_month == 'DEC')
			$month = '12';

	$day = substr($mother_date, 0, 2);

	$select_date = $year.'-'.$month.'-'.$day.' '.'00:00:00';
	return $select_date;
}




function con_date_end($date)
{
	$mother_date = $date;
	$year = substr($mother_date, 7);
	$temp_month = substr($mother_date, 3, 3);

		if($temp_month == 'JAN')
			$month = '01';
		elseif($temp_month == 'FEB')
			$month = '02';
		elseif($temp_month == 'MAR')
			$month = '03';
		elseif($temp_month == 'APR')
			$month = '04';
		elseif($temp_month == 'MAY')
			$month = '05';
		elseif($temp_month == 'JUN')
			$month = '06';
		elseif($temp_month == 'JUL')
			$month = '07';
		elseif($temp_month == 'AUG')
			$month = '08';
		elseif($temp_month == 'SEP')
			$month = '09';
		elseif($temp_month == 'OCT')
			$month = '10';
		elseif($temp_month == 'NOV')
			$month = '11';
		elseif($temp_month == 'DEC')
			$month = '12';

	$day = substr($mother_date, 0, 2);

	$select_date = $year.'-'.$month.'-'.$day.' '.'23:59:59';
	return $select_date;
}
?>