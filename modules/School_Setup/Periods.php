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
    if(clean_param($_REQUEST['values'],PARAM_NOTAGS) && ($_POST['values'] || $_REQUEST['ajax']) && AllowEdit())
    {
        foreach($_REQUEST['values'] as $id=>$columns)
        {
            if(!(isset($columns['TITLE']) && trim($columns['TITLE'])==''))
            {
                if($columns['START_HOUR'])
                {
                    if (strlen($columns['START_MINUTE'])<2)
                    {
                        $sm= '0'.$columns['START_MINUTE'];
                        $columns['START_TIME'] = $columns['START_HOUR'].':'.$sm.' '.$columns['START_M'];
                    }
                    else 
                        $columns['START_TIME'] = $columns['START_HOUR'].':'.$columns['START_MINUTE'].' '.$columns['START_M'];

                    unset($columns['START_HOUR']);unset($columns['START_MINUTE']);unset($columns['START_M']);
                }
                if($columns['END_HOUR'])
                {
                    if (strlen($columns['END_MINUTE'])<2)
                    {
                        $em= '0'.$columns['END_MINUTE'];
                        $columns['END_TIME'] = $columns['END_HOUR'].':'.$em.' '.$columns['END_M'];
                    }
                    else
                        $columns['END_TIME'] = $columns['END_HOUR'].':'.$columns['END_MINUTE'].' '.$columns['END_M'];

                    unset($columns['END_HOUR']);unset($columns['END_MINUTE']);unset($columns['END_M']);
                }

                ###########################################################################################################3
                $checklength=0;
                #	if($id!='new')
                #	{       
                $sql_get_length1 = "select start_time, end_time from SCHOOL_PERIODS where period_id='$id'";
                $res_get_length1 = mysql_query($sql_get_length1);
                $row_get_length1 = mysql_fetch_array($res_get_length1);

                #$start_time2 = $row_get_length1['start_time'];
                #$end_time2 = $row_get_length1['end_time'];
                #echo '<br>';

                if(isset($columns['START_TIME']) )
                    $start_time1 =  $columns['START_TIME'];
                else
                    $start_time1 = $row_get_length1['start_time'];



                if(isset($columns['END_TIME']) )
                    $end_time1 =  $columns['END_TIME'];
                else
                    $end_time1 = $row_get_length1['end_time'];

                $start_time_mod1 = get_min($start_time1);                         
                $end_time_mod1 = get_min($end_time1);

                # $end_time_mod2 = get_min($end_time2);
                # if($end_time_mod2 < $start_time_mod2)
                #     $checklength = 1;

                #   $start_time1 =  $columns['START_TIME'];
                #$end_time1 = $columns['END_TIME'];
                #echo '<br>';
                #$start_time_mod1 = get_min($start_time1);
                #$end_time_mod1 = get_min($end_time1);


                if($end_time_mod1 < $start_time_mod1)
                    echo '<font color="red">'. "please select valid start and end time " .'</font>' ;
                else
                {
                ##############################################################################################################
                    if($id!='new')
                    {
                        $sql = "UPDATE SCHOOL_PERIODS SET ";

                        foreach($columns as $column=>$value)
                        {
                            $value=trim(paramlib_validation($column,$value));
                            $sql .= $column."='".str_replace("\'","''",$value)."',";
                        }
                        $sql = substr($sql,0,-1) . " WHERE PERIOD_ID='$id'";
                        //echo $sql.'<br>';
                        $sql = str_replace('&amp;', "", $sql);
                        $sql = str_replace('&quot', "", $sql);
                        $sql = str_replace('&#039;', "", $sql);
                        $sql = str_replace('&lt;', "", $sql);
                        $sql = str_replace('&gt;', "", $sql);
                        mysql_query($sql);

                        # -------------------------- Length Update Start -------------------------- #

                        $sql_get_length = "SELECT start_time, end_time from SCHOOL_PERIODS WHERE period_id='$id'";

                        $res_get_length = mysql_query($sql_get_length);
                        $row_get_length = mysql_fetch_array($res_get_length);				
                        $start_time = $row_get_length['start_time'];                                
                        $end_time = $row_get_length['end_time'];                                
                        #echo '<br>';
                        $start_time_mod = get_min($start_time);                               
                        $end_time_mod = get_min($end_time);
                        $length = ($end_time_mod - $start_time_mod);

                        $sql_length_update = "UPDATE SCHOOL_PERIODS set length = ".$length." where period_id='$id'";
                        $res_length_update = mysql_query($sql_length_update);

                        # --------------------------- Length Update End --------------------------- #

                    }
                    else
                    {
                            $sql = "INSERT INTO SCHOOL_PERIODS ";
                            $fields = 'SCHOOL_ID,SYEAR,';
                            $values = "'".UserSchool()."','".UserSyear()."',";
                            $go = 0;
                            foreach($columns as $column=>$value)
                            {
                                if(trim($value))
                                {
                                $value=trim(paramlib_validation($column,$value));
                                $fields .= $column.',';
                                $values .= "'".str_replace("\'","''",$value)."',";
                                $go = true;
                                }
                            }
                            $sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';

                            if($go)
                                DBQuery($sql);

                            # ----------------------------- Length Calculate start --------------------- #

                            $p_id = DBGet(DBQuery("SELECT max(PERIOD_ID) AS period_id FROM SCHOOL_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
                            $period_id = $p_id[1]['PERIOD_ID'];

                            $time_chk = DBGet(DBQuery("SELECT START_TIME,END_TIME FROM SCHOOL_PERIODS WHERE PERIOD_ID='$period_id' AND SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."'"));
                            $start_tm_chk = $time_chk[1][START_TIME];
                            $end_tm_chk = $time_chk[1][END_TIME];

                            $st_mod = get_min($start_tm_chk);
                            $et_mod = get_min($end_tm_chk);
                            $length = ($et_mod - $st_mod);
                            $sql_up = "update SCHOOL_PERIODS set length = ".$length." where period_id='$period_id' and syear='".UserSyear()."' and school_id='".UserSchool()."'";
                            $res_up = mysql_query($sql_up);

                            # -------------------------------------------------------------------------- #
                    }
                }
            }
        }
    }

DrawBC("School Setup > ".ProgramTitle());
#echo "Modules.php?modname=$_REQUEST[modname]";
if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='remove' && AllowEdit())
{
                  $prd_id=paramlib_validation($colmn=PERIOD_ID,$_REQUEST[id]);
	$has_assigned_RET=DBGet(DBQuery("SELECT COUNT(*) AS TOTAL_ASSIGNED FROM COURSE_PERIODS WHERE PERIOD_ID='$prd_id'"));
	$has_assigned=$has_assigned_RET[1]['TOTAL_ASSIGNED'];
	if($has_assigned>0){
	UnableDeletePrompt('Cannot delete because periods are associated.');
	}else{
	if(DeletePrompt_Period('period'))
	{
		DBQuery("DELETE FROM SCHOOL_PERIODS WHERE PERIOD_ID='$prd_id'");
		unset($_REQUEST['modfunc']);
	}
	}
}

if($_REQUEST['modfunc']!='remove')
{
	#$sql = "SELECT PERIOD_ID,TITLE,SHORT_NAME,SORT_ORDER,LENGTH,START_TIME,END_TIME,BLOCK,ATTENDANCE FROM SCHOOL_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER";

$sql = "SELECT PERIOD_ID,TITLE,SHORT_NAME,SORT_ORDER,LENGTH,START_TIME,END_TIME,ATTENDANCE,IGNORE_SCHEDULING FROM SCHOOL_PERIODS WHERE SYEAR='".UserSyear()."' AND SCHOOL_ID='".UserSchool()."' ORDER BY SORT_ORDER";
	$QI = DBQuery($sql);
	
	#$periods_RET = DBGet($QI,array('TITLE'=>'_makeTextInput','SHORT_NAME'=>'_makeTextInput','SORT_ORDER'=>'_makeTextInputMod','BLOCK'=>'_makeTextInput','LENGTH'=>'_makeTextInputMod','START_TIME'=>'_makeTimeInput','END_TIME'=>'_makeTimeInputEnd','ATTENDANCE'=>'_makeCheckboxInput'));
	
	#$periods_RET = DBGet($QI,array('TITLE'=>'_makeTextInput','SHORT_NAME'=>'_makeTextInput','SORT_ORDER'=>'_makeTextInputMod','BLOCK'=>'_makeTextInput','LENGTH'=>'LENGTH','START_TIME'=>'_makeTimeInput','END_TIME'=>'_makeTimeInputEnd','ATTENDANCE'=>'_makeCheckboxInput'));

$periods_RET = DBGet($QI,array('TITLE'=>'_makeTextInput','SHORT_NAME'=>'_makeTextInput','SORT_ORDER'=>'_makeTextInputMod','LENGTH'=>'LENGTH','START_TIME'=>'_makeTimeInput','END_TIME'=>'_makeTimeInputEnd','ATTENDANCE'=>'_makeCheckboxInput','IGNORE_SCHEDULING'=>'_makeCheckboxInput'));

#$columns = array('TITLE'=>'Title','SHORT_NAME'=>'Short Name','SORT_ORDER'=>'Sort Order','START_TIME'=>'Start Time','END_TIME'=>'End Time','LENGTH'=>'Length (minutes)','IGNORE_SCHEDULING'=>'Block','ATTENDANCE'=>'Used for Attendance');

$columns = array('TITLE'=>'Title','SHORT_NAME'=>'Short Name','SORT_ORDER'=>'Sort Order','START_TIME'=>'Start Time','END_TIME'=>'End Time','LENGTH'=>'Length <div></div>(minutes)','ATTENDANCE'=>'Used for <div></div>Attendance','IGNORE_SCHEDULING'=>'Ignore for <div></div>Scheduling');
	//,'START_TIME'=>'Start Time','END_TIME'=>'End Time'
	
	#$link['add']['html'] = array('TITLE'=>_makeTextInput('','TITLE'),'SHORT_NAME'=>_makeTextInput('','SHORT_NAME'),'LENGTH'=>_makeTextInputMod2('','LENGTH'),'SORT_ORDER'=>_makeTextInputMod2('','SORT_ORDER'),'BLOCK'=>_makeTextInput('','BLOCK'),'START_TIME'=>_makeTimeInput('','START_TIME'),'END_TIME'=>_makeTimeInputEnd('','END_TIME'),'ATTENDANCE'=>_makeCheckboxInput('','ATTENDANCE'));
	
	#$link['add']['html'] = array('TITLE'=>_makeTextInput('','TITLE'),'SHORT_NAME'=>_makeTextInput('','SHORT_NAME'),'SORT_ORDER'=>_makeTextInputMod2('','SORT_ORDER'),'BLOCK'=>_makeTextInput('','BLOCK'),'START_TIME'=>_makeTimeInput('','START_TIME'),'END_TIME'=>_makeTimeInputEnd('','END_TIME'),'ATTENDANCE'=>_makeCheckboxInput('','ATTENDANCE'));
	

$link['add']['html'] = array('TITLE'=>_makeTextInput('','TITLE'),'SHORT_NAME'=>_makeTextInput('','SHORT_NAME'),'SORT_ORDER'=>_makeTextInputMod2('','SORT_ORDER'),'START_TIME'=>_makeTimeInput('','START_TIME'),'END_TIME'=>_makeTimeInputEnd('','END_TIME'),'ATTENDANCE'=>_makeCheckboxInput('','ATTENDANCE'),'IGNORE_SCHEDULING'=>_makeCheckboxInput('','IGNORE_SCHEDULING'));
	
	$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove";
	$link['remove']['variables'] = array('id'=>'PERIOD_ID');
	
	echo "<FORM name=F1 id=F1 action=Modules.php?modname=$_REQUEST[modname]&modfunc=update method=POST>";
	#DrawHeader('',SubmitButton('Save'));
	
	ListOutput($periods_RET,$columns,'Period','Periods',$link);
	
	echo '<br><CENTER>'.SubmitButton('Save','','class=btn_medium onclick="formcheck_school_setup_periods();"').'</CENTER>';
	echo '</FORM>';
}

function _makeTextInput($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['PERIOD_ID'])
		$id = $THIS_RET['PERIOD_ID'];
	else
		$id = 'new';
	
	if($name!='TITLE')
		$extra = 'size=5 maxlength=10 class=cell_floating ';
		else # added else for the first textbox merlinvicki
		$extra = 'class=cell_floating';
	
	return TextInput_mod_a($value,'values['.$id.']['.$name.']','',$extra);
}

function _makeTextInputMod($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['PERIOD_ID'])
		$id = $THIS_RET['PERIOD_ID'];
	else
		$id = 'new';
	
	if($THIS_RET['SORT_ORDER']!='')
                        $extra = 'size=5 maxlength=10 class=cell_floating onkeydown=\"return numberOnly(event);\"';
	else
                         $extra = 'size=5 maxlength=10 class=cell_floating onkeydown="return numberOnly(event);"';
        
	return TextInput($value,'values['.$id.']['.$name.']','',$extra);
}

function _makeTextInputMod2($value,$name)
{ global $THIS_RET;

if($THIS_RET['PERIOD_ID'])
$id = $THIS_RET['PERIOD_ID'];
else
$id = 'new';

if($name!='TITLE')
$extra = 'size=5 maxlength=10 class=cell_floating onkeydown="return numberOnly(event);"';

return TextInput($value,'values['.$id.']['.$name.']','',$extra);
}

function _makeCheckboxInput($value,$name)
{	global $THIS_RET;
	
	if($THIS_RET['PERIOD_ID'])
		$id = $THIS_RET['PERIOD_ID'];
	else
		$id = 'new';
	
	return CheckboxInput($value,'values['.$id.']['.$name.']','','',($id=='new'?true:false),'<IMG SRC=assets/check.gif height=15>','<IMG SRC=assets/x.gif height=15>');
}

function _makeTimeInput($value,$name)
{	global $THIS_RET;

	if($THIS_RET['PERIOD_ID'])
		$id = $THIS_RET['PERIOD_ID'];
	else
		$id = 'new';
	$hour = substr($value,0,strpos($value,':'));
	$m = substr($value,0,strpos($value,''));
	
	for($i=1;$i<=12;$i++)
		$hour_options[$i] = $i;
	#$hour_options['0'] = '12';
         for($i=0;$i<=9;$i++)
		$minute_options[$i] = '0'.$i;
	for($i=10;$i<=59;$i++)
		$minute_options[$i] = $i;
	 #$m_options = array('AM'=>'AM','PM'=>'PM');
 $sql_ampm_s = 'SELECT START_TIME FROM SCHOOL_PERIODS WHERE period_id='.$id;
 $res_ampm_s = mysql_query($sql_ampm_s);
 $row_ampm_s = mysql_fetch_array($res_ampm_s);
 $ampm_s =$row_ampm_s['START_TIME'];
 $f_ampm_s = substr($ampm_s, -2);

 $min_s =$row_ampm_s['START_TIME'];
 $f_min_s = explode(":", $min_s);
 $fn_min_s =substr($f_min_s[1],0,2);
 if(!is_numeric($fn_min_s))
 	$fn_min_s =substr($f_min_s[1],0,1);
 	 
	 /*
	if($id!='new' && $value)
		return '<DIV id=time'.$id.'><div onclick=\'addHTML("<TABLE><TR><TD>'.str_replace('"','\"',SelectInput($hour,'values['.$id.'][START_HOUR]','',$hour_options,false,'',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($fn_min_s,'values['.$id.'][START_MINUTE]','',$minute_options,false,'',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($f_ampm_s,'values['.$id.'][START_M]','',array('AM'=>'AM','PM'=>'PM'),false,'',false)).'</TD></TR></TABLE>","time'.$id.'",true);\'>'.$value.'</div></DIV>';
	else
		return '<TABLE cellspacing=0 cellpadding=0><TR><TD>'.SelectInput($hour,'values['.$id.'][START_HOUR]','',$hour_options,false,'',false).'</TD><TD>'.SelectInput($fn_min_s,'values['.$id.'][START_MINUTE]','',$minute_options,false,'',false).'</TD><TD>'.SelectInput($f_ampm_s,'values['.$id.'][START_M]','',array('AM'=>'AM','PM'=>'PM'),false,'',false).'</TD></TR></TABLE>';
		*/
		
	if($id!='new' && $value)
	{
		# SelectInput($student['ETHNICITY'],'students[ETHNICITY]','',$ethnic_option,'N/A','');
		
		return '<DIV id=time'.$id.'><div onclick=\'addHTML("<TABLE><TR><TD>'.str_replace('"','\"',SelectInput($hour,'values['.$id.'][START_HOUR]','',$hour_options,false,'',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($fn_min_s,'values['.$id.'][START_MINUTE]','',$minute_options,false,'',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($f_ampm_s,'values['.$id.'][START_M]','',array('AM'=>'AM','PM'=>'PM'),false,'',false)).'</TD></TR></TABLE>","time'.$id.'",true);\'>'.$value.'</div></DIV>';
		
		#return '<DIV id=time'.$id.'><div onclick=\'addHTML("<TABLE><TR><TD>'.str_replace('"','\"',SelectInput($hour,'values['.$id.'][START_HOUR]','',$hour_options,false,'',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($fn_min_s,'values['.$id.'][START_MINUTE]','',$minute_options,false,'',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($f_ampm_s,'values['.$id.'][START_M]','',array('AM'=>'AM','PM'=>'PM'),false,'',false)).'</TD></TR></TABLE>","time'.$id.'",true);\'>'.$value.'</div></DIV>';
	}
	else
		return '<TABLE cellspacing=0 cellpadding=0><TR><TD>'.SelectInput($hour,'values['.$id.'][START_HOUR]','',$hour_options,'N/A','',false).'</TD><TD>'.SelectInput($fn_min_s,'values['.$id.'][START_MINUTE]','',$minute_options,'N/A','',false).'</TD><TD>'.SelectInput($f_ampm_s,'values['.$id.'][START_M]','',array('AM'=>'AM','PM'=>'PM'),'N/A','',false).'</TD></TR></TABLE>';
}



function _makeTimeInputEnd($value,$name)
{	global $THIS_RET;

	if($THIS_RET['PERIOD_ID'])
		$id = $THIS_RET['PERIOD_ID'];
	else
		$id = 'new';
	$hour = substr($value,0,strpos($value,':'));
	$m = substr($value,0,strpos($value,''));
	
	for($i=1;$i<=12;$i++)
		$hour_options[$i] = $i;
	#$hour_options['0'] = '12';
       for($i=0;$i<=9;$i++)
		$minute_options[$i] = '0'.$i;
	for($i=10;$i<=59;$i++)
		$minute_options[$i] = $i;
	 #$m_options = array('AM'=>'AM','PM'=>'PM');
 $sql_ampm = 'select end_time from SCHOOL_PERIODS where period_id='.$id;
 $res_ampm = mysql_query($sql_ampm);
 $row_ampm = mysql_fetch_array($res_ampm);
 $ampm =$row_ampm['end_time'];
 $f_ampm = substr($ampm, -2);
 
 $min =$row_ampm['end_time'];
 $f_min = explode(":", $min);
 $fn_min =substr($f_min[1],0,2);
 if(!is_numeric($fn_min))
 	$fn_min =substr($f_min[1],0,1);
 	 
	/*
	if($id!='new' && $value)
		return '<DIV id=etime'.$id.'><div onclick=\'addHTML("<TABLE><TR><TD>'.str_replace('"','\"',SelectInput($hour,'values['.$id.'][END_HOUR]','',$hour_options,false,'',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($fn_min,'values['.$id.'][END_MINUTE]','',$minute_options,false,'',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($f_ampm,'values['.$id.'][END_M]','',array('AM'=>'AM','PM'=>'PM'),false,'',false)).'</TD></TR></TABLE>","etime'.$id.'",true);\'>'.$value.'</div></DIV>';
	else
		return '<TABLE cellspacing=0 cellpadding=0><TR><TD>'.SelectInput($hour,'values['.$id.'][END_HOUR]','',$hour_options,false,'',false).'</TD><TD>'.SelectInput($fn_min,'values['.$id.'][END_MINUTE]','',$minute_options,false,'',false).'</TD><TD>'.SelectInput($f_ampm,'values['.$id.'][END_M]','',array('AM'=>'AM','PM'=>'PM'),false,'',false).'</TD></TR></TABLE>';
		*/
		
	if($id!='new' && $value)
		return '<DIV id=etime'.$id.'><div onclick=\'addHTML("<TABLE><TR><TD>'.str_replace('"','\"',SelectInput($hour,'values['.$id.'][END_HOUR]','',$hour_options,false,'',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($fn_min,'values['.$id.'][END_MINUTE]','',$minute_options,false,'',false)).'</TD><TD>'.str_replace('"','\"',SelectInput($f_ampm,'values['.$id.'][END_M]','',array('AM'=>'AM','PM'=>'PM'),false,'',false)).'</TD></TR></TABLE>","etime'.$id.'",true);\'>'.$value.'</div></DIV>';
	else
		return '<TABLE cellspacing=0 cellpadding=0><TR><TD>'.SelectInput($hour,'values['.$id.'][END_HOUR]','',$hour_options,'N/A','',false).'</TD><TD>'.SelectInput($fn_min,'values['.$id.'][END_MINUTE]','',$minute_options,'N/A','',false).'</TD><TD>'.SelectInput($f_ampm,'values['.$id.'][END_M]','',array('AM'=>'AM','PM'=>'PM'),'N/A','',false).'</TD></TR></TABLE>';
}

function get_min($time){
	$org_tm = $time;
	$stage = substr($org_tm,-2);
	$main_tm = substr($org_tm,0,5);
	$main_tm = trim($main_tm);
	$sp_time = split(':',$main_tm);
	$hr = $sp_time[0];
	$min = $sp_time[1];
	if($hr == 12){ 
		$hr = $hr;
	}
	else
	{
		if($stage == 'AM')
			$hr = $hr;
		if($stage == 'PM')
			$hr = $hr + 12;
	}
	$time_min = (($hr * 60) + $min);
	return $time_min;
}

?>
