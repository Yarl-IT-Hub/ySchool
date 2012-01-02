<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. web: www.os4ed.com
#
#  openSIS is  web-based, open source, and comes packed with features that 
#  include student demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  This program is released under the terms of the GNU General Public License as  
#  published by the Free Software Foundation, version 2 of the License. 
#  See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#***************************************************************************************
// example:
//
//	if(DeletePrompt('Title'))
//	{
//		DBQuery("DELETE FROM BOK WHERE id='$_REQUEST[benchmark_id]'");
//	}

function DeletePromptCommon($title,$action='delete')
{
   $tmp_REQUEST = $_REQUEST;

     unset($tmp_REQUEST['delete_ok']);

	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);
                  $PHP_tmp_SELF = str_replace(' ', '+' ,$PHP_tmp_SELF);

	if(!$_REQUEST['delete_ok'] && !$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header','Confirm'.(strpos($action,' ')===false?' '.ucwords($action):''));
		echo "<CENTER><h4>Are you sure you want to $action that $title?</h4><br><FORM action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST><INPUT type=submit class=btn_medium value=OK>&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='window.location=\"Modules.php?modname=".$_REQUEST['modname']."&category_id=".$_REQUEST['category_id']."&table=".$_REQUEST['table']."&include=".$_REQUEST['include']."&subject_id=".$_REQUEST['subject_id']."&course_id=".$_REQUEST['course_id']."&course_period_id=".$_REQUEST['course_period_id']."\"'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;
}
function DeletePrompt($title,$action='delete')
{
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);
                  $PHP_tmp_SELF = str_replace(' ', '+' ,$PHP_tmp_SELF);
	if(!$_REQUEST['delete_ok'] && !$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header','Confirm'.(strpos($action,' ')===false?' '.ucwords($action):''));
		echo "<CENTER><h4>Are you sure you want to $action that $title?</h4><br><FORM action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST><INPUT type=submit class=btn_medium value=OK>&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='javascript:history.go(-1);'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;
}



function DeletePromptAssignment($title,$pid=0,$action='delete')
{
	
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
	if($pdf==true)
		$tmp_REQUEST['_openSIS_PDF'] = true;
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] &&!$_REQUEST['delete_cancel'])
	{
		if($pid == 0)
		{
			echo '<BR>';
			PopTable('header',$title);
			echo "<CENTER><h4>Are you sure you want to $action that $title?</h4><FORM action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><BR><INPUT type=submit class=btn_medium value=OK>&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='window.location=\"Modules.php?modname=Grades/Assignments.php\"'></FORM></CENTER>";
			PopTable('footer');
			return false;
		}
		elseif($pid != 0)
		{
			echo '<BR>';
			PopTable('header',$title);
			echo "<CENTER><h4>Are you sure you want to $action that $title?</h4><FORM action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><BR><INPUT type=submit class=btn_medium value=OK>&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='window.location=\"Modules.php?modname=Grades/Assignments.php&assignment_type_id=$pid\"'></FORM></CENTER>";
			PopTable('footer');
			return false;
		}
	}
	else
		return true;	
}


function UnableDeletePrompt($title,$action='delete')
{
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] && !$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header','Unable to Delete');
		echo "<CENTER><h4>$title</h4><br><FORM action=Modules.php?modname=$_REQUEST[modname] METHOD=POST><INPUT type=submit class=btn_medium name=delete_cancel value=Ok></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;
}
//TODO:Use this instead of previous
function UnableDeletePromptMod($title,$action='delete',$queryString='')
{
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] && !$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header','Unable to Delete');
		echo "<CENTER><h4>$title</h4><br><FORM action=Modules.php?modname=$_REQUEST[modname]&$queryString METHOD=POST><INPUT type=submit class=btn_medium name=delete_cancel value=Ok></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;
}
function Prompt($title='Confirm',$question='',$message='',$pdf='')
{	

	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
	if($pdf==true)
		$tmp_REQUEST['_openSIS_PDF'] = true;
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] &&!$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header',$title);
		echo "<CENTER><h4>$question</h4><FORM action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><BR><INPUT type=submit class=btn_medium value=OK>&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='javascript:history.go(-1);'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;	
}

function Prompt_Home($title='Confirm',$question='',$message='',$pdf='')
{	

	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
	if($pdf==true)
		$tmp_REQUEST['_openSIS_PDF'] = true;
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] &&!$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header',$title);
		echo "<CENTER><h4>$question</h4><FORM action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST>$message<BR><BR><INPUT type=submit class=btn_medium value=OK>&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='window.location=\"Modules.php?modname=misc/Portal.php\"'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;	
}


function DeletePrompt_Portal($title,$action='delete')
{
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] && !$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header','Confirm'.(strpos($action,' ')===false?' '.ucwords($action):''));
		echo "<CENTER><h4>Are you sure you want to $action that $title?</h4><br><FORM action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST><INPUT type=submit class=btn_medium value=OK>&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='window.location=\"Modules.php?modname=School_Setup/PortalNotes.php\"'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;
}

function DeletePrompt_Period($title,$action='delete')
{
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] && !$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header','Confirm'.(strpos($action,' ')===false?' '.ucwords($action):''));
		echo "<CENTER><h4>Are you sure you want to $action that $title?</h4><br><FORM action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST><INPUT type=submit class=btn_medium value=OK>&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='window.location=\"Modules.php?modname=School_Setup/Periods.php\"'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;
}

function DeletePrompt_GradeLevel($title,$action='delete')
{
	$tmp_REQUEST = $_REQUEST;
	unset($tmp_REQUEST['delete_ok']);
		
	$PHP_tmp_SELF = PreparePHP_SELF($tmp_REQUEST);

	if(!$_REQUEST['delete_ok'] && !$_REQUEST['delete_cancel'])
	{
		echo '<BR>';
		PopTable('header','Confirm'.(strpos($action,' ')===false?' '.ucwords($action):''));
		echo "<CENTER><h4>Are you sure you want to $action that $title?</h4><br><FORM action=$PHP_tmp_SELF&delete_ok=1 METHOD=POST><INPUT type=submit class=btn_medium value=OK>&nbsp;<INPUT type=button class=btn_medium name=delete_cancel value=Cancel onclick='window.location=\"Modules.php?modname=School_Setup/GradeLevels.php\"'></FORM></CENTER>";
		PopTable('footer');
		return false;
	}
	else
		return true;
}

?>