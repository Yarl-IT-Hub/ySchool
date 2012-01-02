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
include 'modules/Grades/DeletePromptX.fnc.php';
DrawBC("Gradebook > ".ProgramTitle());
#Search('student_id','','true');
Search('student_id');
echo '<style type="text/css">#div_margin { margin-top:-20px; _margin-top:-1px; }</style>';

if(isset($_REQUEST['student_id']) )
{
	$RET = DBGet(DBQuery("SELECT FIRST_NAME,LAST_NAME,MIDDLE_NAME,NAME_SUFFIX,SCHOOL_ID FROM STUDENTS,STUDENT_ENROLLMENT WHERE STUDENTS.STUDENT_ID='".$_REQUEST['student_id']."' AND STUDENT_ENROLLMENT.STUDENT_ID = STUDENTS.STUDENT_ID "));
	//$_SESSION['UserSchool'] = $RET[1]['SCHOOL_ID'];
        $count_student_RET=DBGet(DBQuery("SELECT COUNT(*) AS NUM FROM STUDENTS"));
        if($count_student_RET[1]['NUM']>1){
	DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname=Students/Student.php&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
	//DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname=Scheduling/Schedule.php&search_modfunc=list&next_modname=Scheduling/Schedule.php&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');



//DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) | <A HREF=Modules.php?modname='.$_REQUEST['modname'].'&search_modfunc=list&next_modname='.$_REQUEST['modname'].'&ajax=true&bottom_back=true&return_session=true target=body>Back to Student List</A>');
        }else if($count_student_RET[1]['NUM']==1){
        DrawHeaderHome( 'Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'&nbsp;'.$RET[1]['NAME_SUFFIX'].' (<A HREF=Side.php?student_id=new&modcat='.$_REQUEST['modcat'].'><font color=red>Remove</font></A>) ');
        }

	//echo '<div align="left" style="padding-left:16px"><b>Selected Student: '.$RET[1]['FIRST_NAME'].'&nbsp;'.($RET[1]['MIDDLE_NAME']?$RET[1]['MIDDLE_NAME'].' ':'').$RET[1]['LAST_NAME'].'</b></div>';
}
####################

if(UserStudentID())
{
    $student_id = UserStudentID();
    $mp_id = $_REQUEST['mp_id'];
    $tab_id = ($_REQUEST['tab_id']?$_REQUEST['tab_id']:'grades');
    if ($_REQUEST['modfunc']=='update' && $_REQUEST['removemp'] && $mp_id && DeletePromptX('Marking Period')){
            DBQuery("DELETE FROM STUDENT_MP_STATS WHERE student_id = $student_id and marking_period_id = $mp_id");
            unset($mp_id);
    }
    
    if ($_REQUEST['modfunc']=='update' && !$_REQUEST['removemp']){
	
        if ($_REQUEST['new_sms']) {
		
			// ------------------------ Start -------------------------- //
			$res=DBQuery("SELECT * FROM STUDENT_MP_STATS WHERE student_id=$student_id AND marking_period_id=".$_REQUEST['new_sms']);
			$rows = mysql_num_rows($res);
			
			if($rows==0)
			{
				DBQuery("INSERT INTO STUDENT_MP_STATS (student_id, marking_period_id) VALUES ($student_id, ".$_REQUEST['new_sms'].")");
			}
			elseif($rows!=0)
			{
				echo "<b>This Marking Periods has been updated.</b>";
			}
			// ------------------------- End --------------------------- //
            $mp_id = $_REQUEST['new_sms'];
            
        }

        if ($_REQUEST['SMS_GRADE_LEVEL'] && $mp_id) {
            $updatestats = "UPDATE STUDENT_MP_STATS SET grade_level_short = '".$_REQUEST['SMS_GRADE_LEVEL']."'
                            WHERE marking_period_id = $mp_id     
                            AND student_id = $student_id";
            DBQuery($updatestats);
        }    
        foreach($_REQUEST['values'] as $id=>$columns)
        {
            if($id!='new')
            {
                $sql = "UPDATE STUDENT_REPORT_CARD_GRADES SET ";

                foreach($columns as $column=>$value)
                    $sql .= $column."='".str_replace("\'","''",$value)."',";

                if($_REQUEST['tab_id']!='new')
                    $sql = substr($sql,0,-1) . " WHERE ID='$id'";
                else
                    $sql = substr($sql,0,-1) . " WHERE ID='$id'";
                DBQuery($sql);
            }
            else
            {
                $sql = 'INSERT INTO STUDENT_REPORT_CARD_GRADES ';
                $fields = 'SCHOOL_ID, SYEAR, STUDENT_ID, MARKING_PERIOD_ID, ';
                $values = UserSchool().", ".UserSyear().", $student_id, $mp_id, ";

                $go = false;
                foreach($columns as $column=>$value)
                    if(trim($value))
                    {
                        $fields .= $column.',';
                        $values .= '\''.str_replace("\'","''",$value).'\',';
                        $go = true;
                    }
                $sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';

                if($go && $mp_id && $student_id)
                    DBQuery($sql);
            }
        }
        unset($_REQUEST['modfunc']); 

    }
    if($_REQUEST['modfunc']=='remove')
    {
        if(DeletePromptX('Student Grade'))
        {
            DBQuery("DELETE FROM STUDENT_REPORT_CARD_GRADES WHERE ID='$_REQUEST[id]'");
        }
    }    
    if(!$_REQUEST['modfunc']){    
        $stuRET = DBGet(DBQuery("SELECT LAST_NAME, FIRST_NAME, MIDDLE_NAME, NAME_SUFFIX from STUDENTS where STUDENT_ID = $student_id"));
        $stuRET = $stuRET[1];
        $displayname = $stuRET['LAST_NAME'].(($stuRET['NAME_SUFFIX'])?$stuRET['suffix'].' ':'').', '.$stuRET['FIRST_NAME'].' '.$stuRET['MIDDLE_NAME'];
       
       $gquery = "SELECT mp.syear, mp.marking_period_id as mp_id, mp.title as mp_name, mp.post_end_date as posted, sms.grade_level_short as grade_level, 
       (sms.sum_weighted_factors/sms.count_weighted_factors)*s.reporting_gp_scale as weighted_gpa,
        sms.cum_weighted_factor*s.reporting_gp_scale as weighted_cum,
        (sms.sum_unweighted_factors/sms.count_unweighted_factors)*s.reporting_gp_scale as unweighted_gpa,
        sms.cum_unweighted_factor*s.reporting_gp_scale as unweighted_cum
       FROM MARKING_PERIODS mp, STUDENT_MP_STATS sms, SCHOOLS s
       WHERE sms.marking_period_id = mp.marking_period_id and
             s.id = mp.school_id and sms.student_id = $student_id
       AND mp.school_id = '".UserSchool()."' order by mp.post_end_date";
           
        $GRET = DBGet(DBQuery($gquery));
        
        $last_posted = null;
        $gmp = array(); //grade marking_periods
        $grecs = array();  //grade records
        if($GRET){
            foreach($GRET as $rec){
                if ($mp_id == null || $mp_id == $rec['MP_ID']){
                    $mp_id = $rec['MP_ID'];
                    $gmp[$rec['MP_ID']] = array('schoolyear'=>formatSyear($rec['SYEAR']),
                                                'mp_name'=>$rec['MP_NAME'],
                                                'grade_level'=>$rec['GRADE_LEVEL'],
                                                'weighted_cum'=>$rec['WEIGHTED_CUM'],
                                                'unweighted_cum'=>$rec['UNWEIGHTED_CUM'],
                                                'weighted_gpa'=>$rec['WEIGHTED_GPA'],
                                                'unweighted_gpa'=>$rec['UNWEIGHTED_GPA'],
                                                'gpa'=>$rec['GPA']);
                }
                if ($mp_id != $rec['MP_ID']){
                    $gmp[$rec['MP_ID']] = array('schoolyear'=>formatSyear($rec['SYEAR']),
                                                'mp_name'=>$rec['MP_NAME'],
                                                'grade_level'=>$rec['GRADE_LEVEL'],
                                                'weighted_cum'=>$rec['WEIGHTED_CUM'],
                                                'unweighted_cum'=>$rec['UNWEIGHTED_CUM'],
                                                'weighted_gpa'=>$rec['WEIGHTED_GPA'],
                                                'unweighted_gpa'=>$rec['UNWEIGHTED_GPA'],
                                                'gpa'=>$rec['GPA']);
                }    
            }
        } else {
            $mp_id = "0";
        }
        $mpselect = "<FORM action=Modules.php?modname=$_REQUEST[modname]&tab_id=".$_REQUEST['tab_id']." method=POST>";
        $mpselect .= "<SELECT name=mp_id onchange='this.form.submit();'>";
        foreach ($gmp as $id=>$mparray){
            $mpselect .= "<OPTION value=".$id.(($id==$mp_id)?' SELECTED':'').">".$mparray['schoolyear'].' '.$mparray['mp_name'].', Grade '.$mparray['grade_level']."</OPTION>";
        }
        $mpselect .= "<OPTION value=0 ".(($mp_id=='0')?' SELECTED':'').">Add another marking period</OPTION>";   
        $mpselect .= '</SELECT>';
        
        echo '</FORM>';

            echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=update&tab_id=$_REQUEST[tab_id]&mp_id=$mp_id method=POST>";
            DrawHeaderHome($mpselect);
           echo '<BR><table align=center ><tr><td align=center colspan=2><div class=student>'.$displayname.'</div><br><b>MARKING PERIOD</b><br>WEIGHTED GPA:'.sprintf('%0.3f',$gmp[$mp_id]['weighted_gpa']).'<br>UNWEIGHTED GPA:'.sprintf('%0.3f',$gmp[$mp_id]['unweighted_gpa']).'</td></tr>';
            
            
            $sms_grade_level = TextInput($gmp[$mp_id]['grade_level'],"SMS_GRADE_LEVEL","Grade Level",'size=15 maxlength=3 class=cell_floating');
            
            if ($mp_id=="0"){
                $syear = UserSyear();
                $sql = "SELECT MARKING_PERIOD_ID, SYEAR, TITLE, POST_END_DATE FROM MARKING_PERIODS WHERE SCHOOL_ID = '".UserSchool().
                        "' AND SYEAR BETWEEN ".sprintf('%d',$syear-5)." AND $syear ORDER BY POST_END_DATE";
                $MPRET = DBGet(DBQuery($sql));
                if ($MPRET){
                    $mpoptions = array();
                    foreach ($MPRET as $id=>$mp){
                        $mpoptions[$mp['MARKING_PERIOD_ID']] = formatSyear($mp['SYEAR']).' '.$mp['TITLE'];
                    } 
                   PopTable_grade_header('header');
                    echo "<TABLE align=center><TR><TD>";
                    echo SelectInput(null,'new_sms','New Marking Period',$mpoptions,false,null);
                    echo "</TD>";
					echo "<TD WIDTH=14%></TD>";
					echo "<TD>";
                    echo $sms_grade_level;
                    echo "</TD></TR></TABLE>";
					PopTable ('footer');
                } 
                
            } else {
                echo '<tr><td align=right width=50% valign=top>Grade:</td><td width=50% valign=top>'.$sms_grade_level.'</td></tr><tr><td class=clear></td></tr></table>';
                $tabs = array();
                $tabs[] = array('title'=>'Grades','link'=>"Modules.php?modname=$_REQUEST[modname]&tab_id=grades&mp_id=$mp_id");
                $tabs[] = array('title'=>'Credits','link'=>"Modules.php?modname=$_REQUEST[modname]&tab_id=credits&mp_id=$mp_id");
                echo '<CENTER>'.WrapTabs($tabs,"Modules.php?modname=$_REQUEST[modname]&tab_id=$tab_id&mp_id=$mp_id").'</CENTER>';
                
                $sql = 'SELECT * FROM STUDENT_REPORT_CARD_GRADES WHERE STUDENT_ID = '.$student_id.' AND MARKING_PERIOD_ID = '.$mp_id.' ORDER BY ID';
            
                //build forms based on tab selected
                if ($_REQUEST['tab_id']=='grades' || $_REQUEST['tab_id'] == ''){
                    $functions = array( 'COURSE_TITLE'=>'makeTextInput',
                                        'GRADE_PERCENT'=>'makeTextInput',
                                        'GRADE_LETTER'=>'makeTextInput',
                                        'WEIGHTED_GP'=>'makeTextInput',                  
                                        'UNWEIGHTED_GP'=>'makeTextInput',
                                        'GP_SCALE'=>'makeTextInput',
                                        );
                    $LO_columns = array('COURSE_TITLE'=>'Course Name',
                                        'GRADE_PERCENT'=>'Percentage',
                                        'GRADE_LETTER'=>'Letter Grade',
                                        'WEIGHTED_GP'=>'GP Value',
                                        'UNWEIGHTED_GP'=>'Unweighted GP Value',
                                        'GP_SCALE'=>'Grade Scale',
                                        );
                    $link['add']['html'] = array('COURSE_TITLE'=>makeTextInput('','COURSE_TITLE'),
                                        'GRADE_PERCENT'=>makeTextInput('','GRADE_PERCENT'),
                                        'GRADE_LETTER'=>makeTextInput('','GRADE_LETTER'),
                                        'WEIGHTED_GP'=>makeTextInput('','WEIGHTED_GP'),
                                        'UNWEIGHTED_GP'=>makeTextInput('','UNWEIGHTED_GP'),
                                        'GP_SCALE'=>makeTextInput('','GP_SCALE'),
                                        );
                } else {
                    $functions = array( 'COURSE_TITLE'=>'makeTextInput',
                                        'CREDIT_ATTEMPTED'=>'makeTextInput',
                                        'CREDIT_EARNED'=>'makeTextInput',
                                        'CREDIT_CATEGORY'=>'makeTextInput'
                                        );
                    $LO_columns = array('COURSE_TITLE'=>'Course Name',
                                        'CREDIT_ATTEMPTED'=>'Credit Attempted',
                                        'CREDIT_EARNED'=>'Credit Earned',
                                        'CREDIT_CATEGORY'=>'Credit Category'
                                        );
                    $link['add']['html'] = array('COURSE_TITLE'=>makeTextInput('','COURSE_TITLE'),
                                        'CREDIT_ATTEMPTED'=>makeTextInput('','CREDIT_ATTEMPTED'),
                                        'CREDIT_EARNED'=>makeTextInput('','CREDIT_EARNED'),
                                        'CREDIT_CATEGORY'=>makeTextInput('','CREDIT_CATEGORY')
                                        );
                                        
                }
                $link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove&mp_id=$mp_id";
                $link['remove']['variables'] = array('id'=>'ID');
                $link['add']['html']['remove'] = button('add');
                $LO_ret = DBGet(DBQuery($sql),$functions);
				echo '<div  id="div_margin">';
				PopTable_wo_header ('header');
				echo '</div>';
            	ListOutput($LO_ret,$LO_columns,'','',$link,array(),array('count'=>true,'download'=>true,'search'=>true));
				PopTable ('footer');
            }
            echo '<CENTER>';
            if (!$LO_ret){
                echo SubmitButton('Remove Marking Period', 'removemp','class=btn_large');
				echo '&nbsp;';
            }
            echo SubmitButton('Save','','class=btn_medium').'</CENTER>';
            echo '</FORM>';
    }
}
function makeTextInput($value,$name)
{    global $THIS_RET;

    if($THIS_RET['ID'])
        $id = $THIS_RET['ID'];
    else
        $id = 'new';
    if($name=='COURSE_TITLE')
        $extra = 'size=25 maxlength=25 class=cell_floating';
    elseif($name=='GRADE_PERCENT')
        $extra = 'size=6 maxlength=6 class=cell_floating';
    elseif($name=='GRADE_LETTER' || $name=='WEIGHTED_GP' || $name=='UNWEIGHTED_GP')
        $extra = 'size=5 maxlength=5 class=cell_floating';
    else
    $extra = 'size=10 maxlength=10 class=cell_floating';

    return TextInput($value,"values[$id][$name]",'',$extra);
}
function formatSyear($value){
    return substr($value,2).'-'.substr($value+1,2);
}
?>
