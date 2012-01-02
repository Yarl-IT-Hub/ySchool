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
DrawHeader(ProgramTitle());

if($_REQUEST['modfunc']=='update'){
    
    foreach($_REQUEST['year_values'] as $id=>$column)
    {
        foreach($column as $colname=>$colvalue)
        {
            if ($_REQUEST['day_values'][$id][$colname] && 
                $_REQUEST['month_values'][$id][$colname] &&
                $_REQUEST['year_values'][$id][$colname])
                $_REQUEST['values'][$id][$colname] = $_REQUEST['day_values'][$id][$colname].'-'.
                                                    $_REQUEST['month_values'][$id][$colname].'-'.
                                                    $_REQUEST['year_values'][$id][$colname];
        }
    }
    
    foreach($_REQUEST['values'] as $id=>$columns)
    {
        if($id!='new')
        {
            $sql = "UPDATE HISTORY_MARKING_PERIODS SET ";

            foreach($columns as $column=>$value)
                $sql .= $column."='".str_replace("\'","''",trim($value))."',";

            if($_REQUEST['tab_id']!='new')
                $sql = substr($sql,0,-1) . " WHERE MARKING_PERIOD_ID='$id'";
            else
                $sql = substr($sql,0,-1) . " WHERE MARKING_PERIOD_ID='$id'";
            DBQuery($sql);
        }
        else
        {
            #$id = DBGet(DBQuery("SHOW TABLE STATUS LIKE 'MARKING_PERIOD_ID_GENERATOR'"));
            #$MARKING_PERIOD_ID_VALUE= $id[1]['AUTO_INCREMENT'];

                                    DBQuery('INSERT INTO MARKING_PERIOD_ID_GENERATOR (id)VALUES (NULL)');
                                    $id_RET = DBGet(DBQuery('SELECT  max(id) AS ID from MARKING_PERIOD_ID_GENERATOR' ));
                                    $MARKING_PERIOD_ID_VALUE= $id_RET[1]['ID'];
            $sql = 'INSERT INTO HISTORY_MARKING_PERIODS ';
            $fields = 'MARKING_PERIOD_ID, SCHOOL_ID, ';
            $values = $MARKING_PERIOD_ID_VALUE . ", ".UserSchool().", ";
            


            $go = false;
            foreach($columns as $column=>$value)
                if($value)
                {
                    $fields .= $column.',';
                    $values .= '\''.str_replace("\'","''",$value).'\',';
                    $go = true;
                }
            $sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';

            if($go && trim($columns['NAME']))
                DBQuery($sql);
        }
    }
    unset($_REQUEST['modfunc']);
}
if($_REQUEST['modfunc']=='remove')
{
    if(DeletePromptX('History Marking Period'))
    {
        DBQuery("DELETE FROM HISTORY_MARKING_PERIODS WHERE MARKING_PERIOD_ID='$_REQUEST[id]'");
    }
}  

if(!$_REQUEST['modfunc']){
                echo "<FORM action=Modules.php?modname=$_REQUEST[modname]&modfunc=update&tab_id=$_REQUEST[tab_id]&mp_id=$mp_id method=POST>";
                            DrawHeader('',SubmitButton('Save','','class=btn_medium'));
                            echo '<BR>';
                $sql = 'SELECT * FROM HISTORY_MARKING_PERIODS WHERE SCHOOL_ID = '.UserSchool().' ORDER BY POST_END_DATE';
            
                    $functions = array( 'MP_TYPE'=>'makeSelectInput',
                                        'NAME'=>'makeTextInput',
                                        'POST_END_DATE'=>'makeDateInput',
                                        'SYEAR'=>'makeSchoolYearSelectInput'
                                        );
                    $LO_columns = array('MP_TYPE'=>'Type',
                                        'NAME'=>'Name',
                                        'POST_END_DATE'=>'Grade Post Date',
                                        'SYEAR'=>'School Year'
                                        );
                    $link['add']['html'] = array('MP_TYPE'=>makeSelectInput('','MP_TYPE'),
                                        'NAME'=>makeTextInput('','NAME'),
                                        'POST_END_DATE'=>makeDateInput('','POST_END_DATE'),
                                        'SYEAR'=>makeSchoolYearSelectInput('','SYEAR')
                                        );
                                        
//                }
                //$link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove&table=history_marking_periods";
                //$link['remove']['variables'] = array('id'=>'ID');
                $link['remove']['link'] = "Modules.php?modname=$_REQUEST[modname]&modfunc=remove";//&mp_id=$mp_id";
                $link['remove']['variables'] = array('id'=>'MARKING_PERIOD_ID');
                $link['add']['html']['remove'] = button('add');
                $LO_ret = DBGet(DBQuery($sql),$functions);
                ListOutput($LO_ret,$LO_columns,'History Marking Period','History Marking Periods',$link,array(),array('count'=>true,'download'=>false,'search'=>false));
                echo '<CENTER>';
                echo SubmitButton('Save','','class=btn_medium').'</CENTER>';
                echo '</FORM>';
}
function makeTextInput($value,$name)
{    global $THIS_RET;

    if($THIS_RET['MARKING_PERIOD_ID'])
        $id = $THIS_RET['MARKING_PERIOD_ID'];
    else
        $id = 'new';
        
//    if($name=='COURSE_TITLE')
//        $extra = 'size=25 maxlength=25';
//    elseif($name=='GRADE_PERCENT')
//        $extra = 'size=6 maxlength=6';
//    elseif($name=='GRADE_LETTER' || $name=='GP_VALUE' || $name=='UNWEIGHTED_GP_VALUE')
//        $extra = 'size=5 maxlength=5';
       
//    else
    $extra = 'size=20 maxlength=28';

    return TextInput($value,"values[$id][$name]",'',$extra);
}
function makeDateInput($value,$name)
{    global $THIS_RET;

    if($THIS_RET['MARKING_PERIOD_ID'])
        $id = $THIS_RET['MARKING_PERIOD_ID'];
    else
        $id = 'new';
    return DateInput($value,"values[$id][$name]",'');
}
function makeSelectInput($value,$name)
{    global $THIS_RET;

    if($THIS_RET['MARKING_PERIOD_ID'])
        $id = $THIS_RET['MARKING_PERIOD_ID'];
    else
        $id = 'new';

    $options = array('semester'=>'semester', 'quarter'=>'quarter');

    return SelectInput(trim($value),"values[$id][$name]",'',$options,false);
}
function makeSchoolYearSelectInput($value,$name)
{    global $THIS_RET;

    if($THIS_RET['MARKING_PERIOD_ID'])
        $id = $THIS_RET['MARKING_PERIOD_ID'];
    else
        $id = 'new';
    $options = array();
    foreach (range(UserSyear()-9, UserSyear()) as $year)
        $options[$year] = $year.'-'.($year+1);

    return SelectInput(trim($value),"values[$id][$name]",'',$options,false);
}
function formatSyear($value){
    return substr($value,2).'-'.substr($value+1,2);
}  
?>
