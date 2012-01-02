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
include('../../Redirect_modules.php');
DrawBC("School Setup > ".ProgramTitle());

if(!$_REQUEST['marking_period_id'] && count($fy_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER")))==1 && !$_REQUEST['ajax'])
{
	$_REQUEST['marking_period_id'] = $fy_RET[1]['MARKING_PERIOD_ID'];
	$_REQUEST['mp_term'] = 'FY';
}

unset($_SESSION['_REQUEST_vars']['marking_period_id']);
unset($_SESSION['_REQUEST_vars']['mp_term']);

//include 'validation_markingperiods.php';
switch($_REQUEST['mp_term'])
{
	case 'FY':
		$table = 'SCHOOL_YEARS';
		if($_REQUEST['marking_period_id']=='new')
			$title = 'New Year';
	break;

	case 'SEM':
		$table = 'SCHOOL_SEMESTERS';
		if($_REQUEST['marking_period_id']=='new')
			$title = 'New Semester';
	break;

	case 'QTR':
		$table = 'SCHOOL_QUARTERS';
		if($_REQUEST['marking_period_id']=='new')
			$title = 'New Marking Period';
	break;

	case 'PRO':
		$table = 'SCHOOL_PROGRESS_PERIODS';
		if($_REQUEST['marking_period_id']=='new')
			$title = 'New Progress Period';
	break;
}

// UPDATING
if($_REQUEST['day_tables'] && ($_POST['day_tables'] || $_REQUEST['ajax']))
{
	foreach($_REQUEST['day_tables'] as $id=>$values)
	{
		if($_REQUEST['day_tables'][$id]['START_DATE'] && $_REQUEST['month_tables'][$id]['START_DATE'] && $_REQUEST['year_tables'][$id]['START_DATE'])
			$_REQUEST['tables'][$id]['START_DATE'] = $_REQUEST['day_tables'][$id]['START_DATE'].'-'.$_REQUEST['month_tables'][$id]['START_DATE'].'-'.$_REQUEST['year_tables'][$id]['START_DATE'];

		elseif(isset($_REQUEST['day_tables'][$id]['START_DATE']) && isset($_REQUEST['month_tables'][$id]['START_DATE']) && isset($_REQUEST['year_tables'][$id]['START_DATE']))
			$_REQUEST['tables'][$id]['START_DATE'] = '';

		if($_REQUEST['day_tables'][$id]['END_DATE'] && $_REQUEST['month_tables'][$id]['END_DATE'] && $_REQUEST['year_tables'][$id]['END_DATE'])
			$_REQUEST['tables'][$id]['END_DATE'] = $_REQUEST['day_tables'][$id]['END_DATE'].'-'.$_REQUEST['month_tables'][$id]['END_DATE'].'-'.$_REQUEST['year_tables'][$id]['END_DATE'];
		elseif(isset($_REQUEST['day_tables'][$id]['END_DATE']) && isset($_REQUEST['month_tables'][$id]['END_DATE']) && isset($_REQUEST['year_tables'][$id]['END_DATE']))
			$_REQUEST['tables'][$id]['END_DATE'] = '';

		if($_REQUEST['day_tables'][$id]['POST_START_DATE'] && $_REQUEST['month_tables'][$id]['POST_START_DATE'] && $_REQUEST['year_tables'][$id]['POST_START_DATE'])
			$_REQUEST['tables'][$id]['POST_START_DATE'] = $_REQUEST['day_tables'][$id]['POST_START_DATE'].'-'.$_REQUEST['month_tables'][$id]['POST_START_DATE'].'-'.$_REQUEST['year_tables'][$id]['POST_START_DATE'];
		elseif(isset($_REQUEST['day_tables'][$id]['POST_START_DATE']) && isset($_REQUEST['month_tables'][$id]['POST_START_DATE']) && isset($_REQUEST['year_tables'][$id]['POST_START_DATE']))
			$_REQUEST['tables'][$id]['POST_START_DATE'] = '';

		if($_REQUEST['day_tables'][$id]['POST_END_DATE'] && $_REQUEST['month_tables'][$id]['POST_END_DATE'] && $_REQUEST['year_tables'][$id]['POST_END_DATE'])
			$_REQUEST['tables'][$id]['POST_END_DATE'] = $_REQUEST['day_tables'][$id]['POST_END_DATE'].'-'.$_REQUEST['month_tables'][$id]['POST_END_DATE'].'-'.$_REQUEST['year_tables'][$id]['POST_END_DATE'];
		elseif(isset($_REQUEST['day_tables'][$id]['POST_END_DATE']) && isset($_REQUEST['month_tables'][$id]['POST_END_DATE']) && isset($_REQUEST['year_tables'][$id]['POST_END_DATE']))
			$_REQUEST['tables'][$id]['POST_END_DATE'] = '';
	}
	if(!$_POST['tables'])
		$_POST['tables'] = $_REQUEST['tables'];
}
	

if(clean_param($_REQUEST['tables'],PARAM_NOTAGS) && ($_POST['tables'] || $_REQUEST['ajax']) && AllowEdit())
{

	// ---------------------- Insert & Update Start ------------------------------ //
		foreach($_REQUEST['tables'] as $id=>$columns)
		{
                                            
                                                    if($id!='new')
                                                    {
                                                            $mp_RET=DBGet(DBQuery("SELECT START_DATE,END_DATE,POST_START_DATE,POST_END_DATE FROM $table WHERE MARKING_PERIOD_ID=$id"));
                                                            $mp_RET=$mp_RET[1];
     
                                                            //if((strtotime($columns['START_DATE'])>strtotime($columns['END_DATE']) && $columns['END_DATE']!='') || (strtotime($columns['START_DATE'])>strtotime($mp_RET['END_DATE']) && $mp_RET['END_DATE']!='') || (strtotime($mp_RET['START_DATE'])>strtotime($columns['END_DATE']) && $columns['END_DATE']!='')|| (isset ($columns['START_DATE']) && $columns['START_DATE']=='' && $columns['END_DATE']!='') ||($columns['START_DATE']=='' && $columns['END_DATE']=='') ||($columns['START_DATE']=='')|| ($columns['END_DATE']==''))
                                                            if((strtotime($mp_RET['START_DATE'])>strtotime($mp_RET['END_DATE']) && $mp_RET['END_DATE']!='' && $columns['END_DATE']!='' && $columns['START_DATE']!='') || (strtotime($columns['START_DATE'])>strtotime($columns['END_DATE']) && $columns['END_DATE']!='' && $mp_RET['END_DATE']!='' && $mp_RET['START_DATE']!='' ) || (strtotime($columns['START_DATE'])>strtotime($mp_RET['END_DATE']) && $mp_RET['END_DATE']!='' && $columns['END_DATE'] =='') || (strtotime($mp_RET['START_DATE'])>strtotime($columns['END_DATE']) && $columns['END_DATE']!='' && $columns['START_DATE']=='') || (isset ($columns['START_DATE']) && $columns['START_DATE']=='' && $columns['END_DATE']!=''))
                                                            {
                                                                    ShowErrPhp('Data not saved because start and end date is not valid');
                                                            }
                                                            else 
                                                            {
                                                                    if((strtotime($columns['POST_START_DATE'])>strtotime($columns['POST_END_DATE']) && $columns['POST_END_DATE']!='') || (strtotime($columns['POST_START_DATE'])>strtotime($mp_RET['POST_END_DATE']) && $mp_RET['POST_END_DATE']!='') || (strtotime($mp_RET['POST_START_DATE'])>strtotime($columns['POST_END_DATE']) && $columns['POST_END_DATE']!='')|| (isset ($columns['POST_START_DATE']) && $columns['POST_START_DATE']=='' && $columns['POST_END_DATE']!=''))
                                                                    {
                                                                        ShowErrPhp('Data not saved because grade post date is not valid');
                                                                    }
                                                                    else
                                                                    {
                                                                            $sql = "UPDATE $table SET ";

                                                                            foreach($columns as $column=>$value)
                                                                            {
                                                                                    $value=paramlib_validation($column,$value);
                                                                                    if($column=='START_DATE' || $column=='END_DATE' || $column=='POST_START_DATE' || $column=='POST_END_DATE')
                                                                                    {
                                                                                            if(!VerifyDate($value) && $value!='')
                                                                                            BackPrompt('Not all of the dates were entered correctly.');
                                                                                    }
                                                                                    $sql .= $column."='".str_replace("\'","''",$value)."',";
                                                                            }
                                                                            $sql = substr($sql,0,-1) . " WHERE MARKING_PERIOD_ID='$id'";
                                                                            $go = true;
                                                                    }
                                                            }
                                                    }
                                            
			else
			{
                                    // $id_RET = DBGet(DBQuery('SELECT '.db_seq_nextval('MARKING_PERIOD_SEQ').' AS ID'.FROM_DUAL));
                                   DBQuery('INSERT INTO MARKING_PERIOD_ID_GENERATOR (id)VALUES (NULL)');

                                    $id_RET = DBGet(DBQuery('SELECT  max(id) AS ID from MARKING_PERIOD_ID_GENERATOR' ));
                                    
				$sql = "INSERT INTO $table ";
				$fields = "MARKING_PERIOD_ID,SYEAR,SCHOOL_ID,";
				$values = "'".$id_RET[1]['ID']."','".UserSyear()."','".UserSchool()."',";
	
				$_REQUEST['marking_period_id'] = $id_RET[1]['ID'];
	
				switch($_REQUEST['mp_term'])
				{
					case 'SEM':
						$fields .= "YEAR_ID,";
						$values .= "'$_REQUEST[year_id]',";
					break;
	
					case 'QTR':
						$fields .= "SEMESTER_ID,";
						$values .= "'$_REQUEST[semester_id]',";
					break;
	
					case 'PRO':
						$fields .= "QUARTER_ID,";
						$values .= "'$_REQUEST[quarter_id]',";
					break;
				}
	
				$go = false;
				foreach($columns as $column=>$value)
				{
                    $value=paramlib_validation($column,$value);
					if($column=='START_DATE' || $column=='END_DATE' || $column=='POST_START_DATE' || $column=='POST_END_DATE')
					{
						if(!VerifyDate($value) && $value!='')
							BackPrompt('Not all of the dates were entered correctly.');
					}
					if($value)
					{
						$fields .= $column.',';
						$values .= "'".str_replace("\'","''",$value)."',";
						$go = true;
					}
				}
				$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
			}
	
			// CHECK TO MAKE SURE ONLY ONE MP & ONE GRADING PERIOD IS OPEN AT ANY GIVEN TIME
			$columns['START_DATE']=date("Y-m-d",strtotime($columns['START_DATE']));
			$columns['END_DATE']=date("Y-m-d",strtotime($columns['END_DATE']));
			$dates_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM $table WHERE (true=false"
				.(($columns['START_DATE'])?" OR '".$columns['START_DATE']."' BETWEEN START_DATE AND END_DATE":'')
				.(($columns['END_DATE'])?" OR '".$columns['END_DATE']."' BETWEEN START_DATE AND END_DATE":'')
				.(($columns['START_DATE'] && $columns['END_DATE'])?" OR START_DATE BETWEEN '".$columns['START_DATE']."' AND '".$columns['END_DATE']."'
				OR END_DATE BETWEEN '".$columns['START_DATE']."' AND '".$columns['END_DATE']."'":'')
				.") AND SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'".(($id!='new')?" AND SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND MARKING_PERIOD_ID!='$id'":'')
			));
			$posting_RET = DBGet(DBQuery("SELECT MARKING_PERIOD_ID FROM $table WHERE (true=false"
				.(($columns['POST_START_DATE'])?" OR '".$columns['POST_START_DATE']."' BETWEEN POST_START_DATE AND POST_END_DATE":'')
				.(($columns['POST_END_DATE'])?" OR '".$columns['POST_END_DATE']."' BETWEEN POST_START_DATE AND POST_END_DATE":'')
				.(($columns['POST_START_DATE'] && $columns['POST_END_DATE'])?" OR POST_START_DATE BETWEEN '".$columns['POST_START_DATE']."' AND '".$columns['POST_END_DATE']."'
				OR POST_END_DATE BETWEEN '".$columns['POST_START_DATE']."' AND '".$columns['POST_END_DATE']."'":'')
				.") AND SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."'".(($id!='new')?" AND MARKING_PERIOD_ID!='$id'":'')
			));
	
	
			if($go)
				DBQuery($sql);
//----------------------------------------------------------------------------------------------------------------------		
                                            if($go){
                                                $UserMp=GetCurrentMP('QTR',DBDate());
                                                $_SESSION['UserMP']=$UserMp;
                                                if(!$UserMp){
                                                    $UserMp=GetCurrentMP('SEM',DBDate());
                                                    $_SESSION['UserMP']=$UserMp;
                                                }
                                                if(!$UserMp){
                                                    $UserMp=GetCurrentMP('FY',DBDate());
                                                    $_SESSION['UserMP']=$UserMp;
                                                }
                                            }
//---------------------------------------------------------------------------------------------------------------------------
                                        }
		// ---------------------- Insert & Update End ------------------------------ //
	
	unset($_REQUEST['tables']);
	unset($_SESSION['_REQUEST_vars']['tables']);
}


if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete')
{
	$extra = array();
	switch($table)
	{
		case 'SCHOOL_YEARS':
			$name = 'year';
			$parent_term = ''; $parent_id = '';
            $year_id=paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST[marking_period_id]);
			$extra[] = "DELETE FROM SCHOOL_PROGRESS_PERIODS WHERE QUARTER_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SEMESTER_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE YEAR_ID='$year_id'))";
			$extra[] = "DELETE FROM SCHOOL_QUARTERS WHERE SEMESTER_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_SEMESTERS WHERE YEAR_ID='$year_id')";
			$extra[] = "DELETE FROM SCHOOL_SEMESTERS WHERE YEAR_ID='$year_id'";
		break;

		case 'SCHOOL_SEMESTERS':
			$name = 'semester';
			$parent_term = 'FY'; $parent_id = paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST['year_id']);
            $sems_id=paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST[marking_period_id]);
			$extra[] = "DELETE FROM SCHOOL_PROGRESS_PERIODS WHERE QUARTER_ID IN (SELECT MARKING_PERIOD_ID FROM SCHOOL_QUARTERS WHERE SEMESTER_ID='$sems_id')";
			$extra[] = "DELETE FROM SCHOOL_QUARTERS WHERE SEMESTER_ID='$sems_id'";
		break;

		case 'SCHOOL_QUARTERS':
			$name = 'quarter';
			$parent_term = 'SEM'; $parent_id = paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST['semester_id']);
            $qrt_id=paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST[marking_period_id]);
			$extra[] = "DELETE FROM SCHOOL_PROGRESS_PERIODS WHERE QUARTER_ID='$qrt_id'";
		break;

		case 'SCHOOL_PROGRESS_PERIODS':
			$name = 'progress period';
			$parent_term = 'QTR'; $parent_id = paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST['quarter_id']);
		break;
	}
$has_assigned_RET=DBGet(DBQuery("SELECT COUNT(*) AS TOTAL_ASSIGNED FROM COURSE_DETAILS WHERE MARKING_PERIOD_ID='".paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST[marking_period_id])."' OR MARKING_PERIOD_ID IN(SELECT MARKING_PERIOD_ID FROM MARKING_PERIODS WHERE PARENT_ID='".paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST[marking_period_id])."')"));
	$has_assigned=$has_assigned_RET[1]['TOTAL_ASSIGNED'];
	if($has_assigned>0){
                        $queryString="mp_term=$_REQUEST[mp_term]&year_id=$_REQUEST[year_id]&semester_id=$_REQUEST[semester_id]&marking_period_id=$_REQUEST[marking_period_id]";
                        UnableDeletePromptMod('Marking period cannot be deleted because it has other associations.','',$queryString);
	}else{
	if(DeletePrompt($name))
	{
		foreach($extra as $sql)
			DBQuery($sql);
		DBQuery("DELETE FROM $table WHERE MARKING_PERIOD_ID='".paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST[marking_period_id])."'");
		unset($_REQUEST['modfunc']);
		$_REQUEST['mp_term'] = $parent_term;
		$_REQUEST['marking_period_id'] = $parent_id;
	}
	}
	unset($_SESSION['_REQUEST_vars']['modfunc']);
	
}

if(!$_REQUEST['modfunc'])
{
	if($_REQUEST['marking_period_id']!='new')
		$delete_button = "<INPUT type=button class=btn_medium value=Delete onClick='javascript:window.location=\"Modules.php?modname=$_REQUEST[modname]&modfunc=delete&mp_term=$_REQUEST[mp_term]&year_id=$_REQUEST[year_id]&semester_id=$_REQUEST[semester_id]&quarter_id=$_REQUEST[quarter_id]&marking_period_id=$_REQUEST[marking_period_id]\"'>";

	// ADDING & EDITING FORM
	if($_REQUEST['marking_period_id'] && $_REQUEST['marking_period_id']!='new')
	{
		$sql = "SELECT TITLE,SHORT_NAME,SORT_ORDER,DOES_GRADES,DOES_EXAM,DOES_COMMENTS,
						START_DATE,END_DATE,POST_START_DATE,POST_END_DATE
				FROM $table
				WHERE MARKING_PERIOD_ID='".paramlib_validation($column=MARKING_PERIOD_ID,$_REQUEST[marking_period_id])."'";
		$QI = DBQuery($sql);
		$RET = DBGet($QI);
		$RET = $RET[1];
		$title = $RET['TITLE'];
	}

	if(clean_param($_REQUEST['marking_period_id'],PARAM_ALPHANUM))
	{
		echo "<FORM name=marking_period id=marking_period action=Modules.php?modname=$_REQUEST[modname]&mp_term=$_REQUEST[mp_term]&marking_period_id=$_REQUEST[marking_period_id]&year_id=$_REQUEST[year_id]&semester_id=$_REQUEST[semester_id]&quarter_id=$_REQUEST[quarter_id] method=POST>";
		PopTable ('header',$title);
		$header .= '<TABLE cellspacing=0 cellpadding=3 border=0>';
		

		$header .= '<TR><td class=lable >Title</td><TD>' . TextInput($RET['TITLE'],'tables['.$_REQUEST['marking_period_id'].'][TITLE]','','class=cell_floating') . '</TD></tr>';
		$header .= '<TR><td class=lable>Short Name</td><TD>' . TextInput($RET['SHORT_NAME'],'tables['.$_REQUEST['marking_period_id'].'][SHORT_NAME]','','class=cell_floating') . '</TD></tr>';
		
		if(clean_param($_REQUEST['marking_period_id'],PARAM_ALPHANUM)=='new')
			$header .= '<TR><td class=lable>Sort Order</td><TD>' . TextInput($RET['SORT_ORDER'],'tables['.$_REQUEST['marking_period_id'].'][SORT_ORDER]','','class=cell_small onKeyDown="return numberOnly(event);"') . '</TD></tr>';
		else
			$header .= '<TR><td class=lable>Sort Order</td><TD>' . TextInput($RET['SORT_ORDER'],'tables['.$_REQUEST['marking_period_id'].'][SORT_ORDER]','','class=cell_small onKeyDown=\"return numberOnly(event);\"') . '</TD></tr>';
			
		$header .= '<TR><td class=lable>Graded</td><TD>' . CheckboxInput($RET['DOES_GRADES'],'tables['.$_REQUEST['marking_period_id'].'][DOES_GRADES]','',$checked,$_REQUEST['marking_period_id']=='new','<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD></tr>';
		$header .= '<TR><td class=lable>Exam</td><TD>' . CheckboxInput($RET['DOES_EXAM'],'tables['.$_REQUEST['marking_period_id'].'][DOES_EXAM]','',$checked,$_REQUEST['marking_period_id']=='new','<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD></tr>';
		$header .= '<TR><td class=lable>Comments</td><TD>' . CheckboxInput($RET['DOES_COMMENTS'],'tables['.$_REQUEST['marking_period_id'].'][DOES_COMMENTS]','',$checked,$_REQUEST['marking_period_id']=='new','<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD></tr>';
		$header .= '<TR><td class=lable>Begins</td><TD>' . DateInput($RET['START_DATE'],'tables['.$_REQUEST['marking_period_id'].'][START_DATE]','') . '</TD></tr>';
		$header .= '<TR><td class=lable>Ends</td><TD>' . DateInput($RET['END_DATE'],'tables['.$_REQUEST['marking_period_id'].'][END_DATE]','') . '</TD></tr>';
		$header .= '<TR><td class=lable>Grade Posting Begins</td><TD>' . DateInput($RET['POST_START_DATE'],'tables['.$_REQUEST['marking_period_id'].'][POST_START_DATE]','') . '</TD></tr>';
		$str_srch='<TR><td class=lable>Comments</td><TD>' . CheckboxInput($RET['DOES_COMMENTS'],'tables['.$_REQUEST['marking_period_id'].'][DOES_COMMENTS]','',$checked,$_REQUEST['marking_period_id']=='new','<IMG SRC=assets/check.gif height=15 vspace=0 hspace=0 border=0>','<IMG SRC=assets/x.gif height=15 vspace=0 hspace=0 border=0>') . '</TD></tr>';
		$header .= '<TR><td class=lable>Grade Posting Ends</td><TD>' . DateInput($RET['POST_END_DATE'],'tables['.$_REQUEST['marking_period_id'].'][POST_END_DATE]','') . '</TD></tr>';

		
		$header .= '</TABLE>';
		DrawHeader($header);
		PopTable('footer');
		
		if(clean_param($_REQUEST['marking_period_id'],PARAM_ALPHANUM)=='new')
			DrawHeaderHome('','',AllowEdit()?$delete_button.'&nbsp;&nbsp;<INPUT type=submit value=Save class="btn_medium" onclick="formcheck_school_setup_marking();">':'');
		else
			DrawHeaderHome('','',AllowEdit()?$delete_button.'&nbsp;&nbsp;<INPUT type=submit name=btn_save id=btn_save value=Save class="btn_medium">':'');

		echo '</FORM>';
		unset($_SESSION['_REQUEST_vars']['marking_period_id']);
		unset($_SESSION['_REQUEST_vars']['mp_term']);
	}

	// DISPLAY THE MENU
	$LO_options = array('save'=>false,'search'=>false);

	echo '<TABLE cellpadding=3 width=100%><tr><td align="center"><br>';
	echo '<TABLE><TR>';

	// FY
	$sql = "SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_YEARS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' ORDER BY SORT_ORDER";
	$QI = DBQuery($sql);
	$fy_RET = DBGet($QI);

	if(count($fy_RET))
	{
		if($_REQUEST['mp_term'])
		{
			if($_REQUEST['mp_term']=='FY')
				$_REQUEST['year_id'] = $_REQUEST['marking_period_id'];

			foreach($fy_RET as $key=>$value)
			{
				if($value['MARKING_PERIOD_ID']==$_REQUEST['year_id'])
					$fy_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
			}
		}
	}

	echo '<TD valign=top>';
	$columns = array('TITLE'=>'Year');
	$link = array();
	$link['TITLE']['link'] = "#"." onclick='check_content(\"ajax.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&mp_term=FY\");'";
	$link['TITLE']['variables'] = array('marking_period_id'=>'MARKING_PERIOD_ID');
	if(!count($fy_RET))
		$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&mp_term=FY&marking_period_id=new";

	ListOutput($fy_RET,$columns,'Year','Years',$link,array(),$LO_options);
	echo '</TD>';

	// SEMESTERS
	if(($_REQUEST['mp_term']=='FY' && $_REQUEST['marking_period_id']!='new') || $_REQUEST['mp_term']=='SEM' || $_REQUEST['mp_term']=='QTR' || $_REQUEST['mp_term']=='PRO')
	{
		$sql = "SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_SEMESTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND YEAR_ID='".$_REQUEST['year_id']."' ORDER BY SORT_ORDER";
		$QI = DBQuery($sql);
		$sem_RET = DBGet($QI);

		if(count($sem_RET))
		{
			if($_REQUEST['mp_term'])
			{
				if($_REQUEST['mp_term']=='SEM')
					$_REQUEST['semester_id'] = $_REQUEST['marking_period_id'];

				foreach($sem_RET as $key=>$value)
				{
					if($value['MARKING_PERIOD_ID']==$_REQUEST['semester_id'])
						$sem_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
				}
			}
		}

		echo '<TD valign=top>';
		$columns = array('TITLE'=>'Semester');
		$link = array();
		$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&mp_term=SEM&year_id=$_REQUEST[year_id]";//." onclick='grabA(this); return false;'";
		$link['TITLE']['variables'] = array('marking_period_id'=>'MARKING_PERIOD_ID');
		$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&mp_term=SEM&marking_period_id=new&year_id=$_REQUEST[year_id]";


		ListOutput($sem_RET,$columns,'Semester','Semesters',$link,array(),$LO_options);
		echo '</TD>';

		// QUARTERS
		if(($_REQUEST['mp_term']=='SEM' && $_REQUEST['marking_period_id']!='new') || $_REQUEST['mp_term']=='QTR' || $_REQUEST['mp_term']=='PRO')
		{
			$sql = "SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_QUARTERS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND SEMESTER_ID='".$_REQUEST['semester_id']."' ORDER BY SORT_ORDER";
			$QI = DBQuery($sql);
			$qtr_RET = DBGet($QI);

			if(count($qtr_RET))
			{
				if(($_REQUEST['mp_term']=='QTR' && $_REQUEST['marking_period_id']!='new') || $_REQUEST['mp_term']=='PRO')
				{
					if($_REQUEST['mp_term']=='QTR')
						$_REQUEST['quarter_id'] = $_REQUEST['marking_period_id'];

					foreach($qtr_RET as $key=>$value)
					{
						if($value['MARKING_PERIOD_ID']==$_REQUEST['quarter_id'])
							$qtr_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
					}
				}
			}

			echo '<TD valign=top>';
			$columns = array('TITLE'=>'Quarter');
			$link = array();
			$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&mp_term=QTR&year_id=$_REQUEST[year_id]&semester_id=$_REQUEST[semester_id]";
			$link['TITLE']['variables'] = array('marking_period_id'=>'MARKING_PERIOD_ID');
			$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&mp_term=QTR&marking_period_id=new&year_id=$_REQUEST[year_id]&semester_id=$_REQUEST[semester_id]";

			ListOutput($qtr_RET,$columns,'Quarter','Quarters',$link,array(),$LO_options);
			echo '</TD>';

			// PROGRESS PERIODS
			if(($_REQUEST['mp_term']=='QTR' && $_REQUEST['marking_period_id']!='new') || $_REQUEST['mp_term']=='PRO')
			{
				$sql = "SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_PROGRESS_PERIODS WHERE SCHOOL_ID='".UserSchool()."' AND SYEAR='".UserSyear()."' AND QUARTER_ID='".$_REQUEST['quarter_id']."' ORDER BY SORT_ORDER";
				$QI = DBQuery($sql);
				$pro_RET = DBGet($QI);

				if(count($pro_RET))
				{
					if(($_REQUEST['mp_term']=='PRO' && $_REQUEST['marking_period_id']!='new'))
					{
						$_REQUEST['progress_period_id'] = $_REQUEST['marking_period_id'];

						foreach($pro_RET as $key=>$value)
						{
							if($value['MARKING_PERIOD_ID']==$_REQUEST['marking_period_id'])
								$pro_RET[$key]['row_color'] = Preferences('HIGHLIGHT');
						}
					}
				}

				echo '<TD valign=top>';
				$columns = array('TITLE'=>'Progress Period');
				$link = array();
				$link['TITLE']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=$_REQUEST[modfunc]&mp_term=PRO&year_id=$_REQUEST[year_id]&semester_id=$_REQUEST[semester_id]&quarter_id=$_REQUEST[quarter_id]";
				$link['TITLE']['variables'] = array('marking_period_id'=>'MARKING_PERIOD_ID');
				$link['add']['link'] = "Modules.php?modname=$_REQUEST[modname]&mp_term=PRO&marking_period_id=new&year_id=$_REQUEST[year_id]&semester_id=$_REQUEST[semester_id]&quarter_id=$_REQUEST[quarter_id]";
	$sql_mp_id = "SELECT MARKING_PERIOD_ID,TITLE FROM SCHOOL_PROGRESS_PERIODS";
				ListOutput($pro_RET,$columns,'Progress Period','Progress Periods',$link,array(),$LO_options);
				echo '</TD>';
			}
		}
	}

	echo '</TR></TABLE></td></tr></table>';
}
?>