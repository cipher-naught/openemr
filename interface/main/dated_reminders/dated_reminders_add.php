  <?php          
//  ------------------------------------------------------------------------ //
//                OpenEMR Electronic Medical Records System                  //
//                 Copyright (c) 2012 tajemo.co.za                      //
//                     <http://www.tajemo.co.za/>                            //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA // 
// --------------------------------------------------------------------------//
// Original Author of this file: Craig Bezuidenhout (Tajemo Enterprises)     //
// Purpose of this file: Used for adding dated reminders                     //
// --------------------------------------------------------------------------// 
                                  
  $fake_register_globals=false;
  $sanitize_all_escapes=true;     
    
    require_once("../../globals.php"); 
    require_once("$srcdir/htmlspecialchars.inc.php");  
    require_once("$srcdir/dated_reminders.php"); 
  
  $dateRanges = array();
// $dateranges = array ( number_period => text to display ) == period is always in the singular 
// eg. $dateRanges['4_week'] = '4 Weeks From Now';  
  $dateRanges['1_day'] =  xl('1 Day From Now'); 
  $dateRanges['2_day'] = xl('2 Days From Now');   
  $dateRanges['3_day'] = xl('3 Days From Now');   
  $dateRanges['4_day'] = xl('4 Days From Now');   
  $dateRanges['5_day'] = xl('5 Days From Now');  
  $dateRanges['6_day'] = xl('6 Days From Now'); 
  $dateRanges['1_week'] = xl('1 Week From Now'); 
  $dateRanges['2_week'] = xl('2 Weeks From Now');
  $dateRanges['3_week'] = xl('3 Weeks From Now');
  $dateRanges['4_week'] = xl('4 Weeks From Now');
  $dateRanges['5_week'] = xl('5 Weeks From Now');
  $dateRanges['6_week'] = xl('6 Weeks From Now');
  $dateRanges['1_month'] = xl('1 Month From Now');   
  $dateRanges['2_month'] = xl('2 Months From Now');  
  $dateRanges['3_month'] = xl('3 Months From Now'); 
  $dateRanges['4_month'] = xl('4 Months From Now'); 
  $dateRanges['5_month'] = xl('5 Months From Now'); 
  $dateRanges['6_month'] = xl('6 Months From Now');  
  $dateRanges['7_month'] = xl('7 Months From Now'); 
  $dateRanges['8_month'] = xl('8 Months From Now'); 
  $dateRanges['9_month'] = xl('9 Months From Now');  
  $dateRanges['1_year'] = xl('1 Year From Now');   
  $dateRanges['2_year'] = xl('2 Years From Now');
  
// --- need to add a check to ensure the post is being sent from the correct location ??? 

// default values for $this_message    
    $this_message = array('message'=>'','message_priority'=>3,'dueDate'=>'');
    $forwarding = false;
    
// ---------------- FOR FORWARDING MESSAGES ------------->
if(isset($_GET['mID']) and is_numeric($_GET['mID'])){
  $forwarding = true;     
  $this_message = getReminderById($_GET['mID']); 
}

// ---------------END FORWARDING MESSAGES ----------------   

 
// --- add reminders 
      if($_POST){ 
// --- initialize $output as blank      
        $output = '';  
 
 // ------ fills an array with all recipients       
          $sendTo = array();
          foreach($_POST as $key=>$val){
          // check for recipients, make sure they are integers
            if(preg_match('/^sendTo_/',$key) and is_numeric($val)){
              $sendTo[] = intval($val);
              unset($_POST[$key]);
            }  
          } 
      
      // for incase of data error, this allows the previously entered data to re-populate the boxes
        $this_message['message'] = (isset($_POST['message']) ? $_POST['message'] : '');  
        $this_message['priority'] = (isset($_POST['priority']) ? $_POST['priority'] : '');
        $this_message['dueDate'] = (isset($_POST['dueDate']) ? $_POST['dueDate'] : '');
        
         
// --------------------------------------------------------------------------------------------------------------------------
// --- check for the post, if it is valid, commit to the database, close this window and run opener.Handeler 
         if(          
// ------- check sendTo is not empty 
           !empty($sendTo) and    
// ------- check dueDate, only allow valid dates, todo -> enhance date checker 
           isset($_POST['dueDate']) and preg_match('/\d{4}[-]\d{2}[-]\d{2}/',$_POST['dueDate']) and     
// ------- check priority, only allow 1-3 
           isset($_POST['priority']) and intval($_POST['priority']) <= 3 and       
// ------- check message, only up to 144 characters
           isset($_POST['message']) and strlen($_POST['message']) <= 144 and strlen($_POST['message']) > 0 and 
// ------- check if PatientID is set and in numeric
           isset($_POST['PatientID']) and is_numeric($_POST['PatientID'])                 
         ){   
           $dueDate = $_POST['dueDate'];
           $priority = intval($_POST['priority']);
           $message = $_POST['message'];
           $fromID = $_SESSION['authId']; 
           $patID = $_POST['PatientID'];   

            
// -------- Send the reminder           
           $ReminderSent = sendReminder($sendTo,$fromID,$message,$dueDate,$patID,$priority);
// --------------------------------------------------------------------------------------------------------------------------
           if(!$ReminderSent){ 
             $output .= '<div style="text-size:2em; text-align:center; color:red">* '.xlt('Please select a valid recipient').'</div> ';
           }else{    
// --------- echo javascript            
             echo '<html><body><script language="JavaScript">'; 
// ------------ 1) refresh parent window this updates if sent to self 
             echo '  if (opener && !opener.closed && opener.updateme) opener.updateme("new");';     
// ------------ 2) communicate with user      
             echo '   alert("'.addslashes(xl('Message Sent')).'");';                 
// ------------ 3) close this window 
             echo '  window.close();';
             echo '</script></body></html>';                 
// --------- stop script from executing further
             exit; 
           }  
// --------------------------------------------------------------------------------------------------------------------------
         }  
// -------------------------------------------------------------------------------------------------------------------------- 
         
         else{                
// ------- if POST error
           $output .= '<div style="text-size:2em; text-align:center; color:red">* '.xlt('Data Error').'</div> ';
         }           
// ------- if any errors, communicate with the user
         echo $output; 
      } 
    // end add reminders 
 
// get current patient, first check if this is a forwarded message, if it is use the original pid 
    if(isset($this_message['pid'])) $patientID = (isset($this_message['pid']) ? $this_message['pid'] : 0);  
    else $patientID = (isset($pid) ? $pid : 0);  
  ?>    
<html>
  <head>
    <title><?php echo xlt('Send a Reminder') ?></title>  
    <link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">                                       
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/topdialog.js"></script>
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dialog.js"></script>  
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/common.js"></script>    
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-1.4.3.min.js"></script>   
    <script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/js/jquery-calendar.js"></script>
    <script language="JavaScript"> 
      $(document).ready(function (){   
        
        $('#timeSpan').change(function(){ 
          var value = $(this).val();
          var arr = value.split('_');
          var span = arr[1];
          var period = parseInt(arr[0]);   
          var d=new Date();      
          if(span == 'day'){ 
            d.setDate(d.getDate()+period);
          }
          else if(span == 'week'){
            var weekInDays = period * 7;
            d.setDate(d.getDate()+weekInDays);  
          }
          else if(span == 'month'){
            d.setMonth(d.getMonth()+period);
          }   
          else if(span == 'year'){
            var yearsInMonths = period * 12;
            d.setMonth(d.getMonth()+yearsInMonths); 
          } 
          var curr_date = d.getDate().toString();
          if(curr_date.length == 1){
            curr_date = '0'+curr_date;
          } 
          var curr_month = d.getMonth() + 1; //months are zero based 
          curr_month = curr_month.toString();    
          if(curr_month.length == 1){
            curr_month = '0'+curr_month;
          } 
          var curr_year = d.getFullYear(); 
          $('#dueDate').val(curr_year + "-" + curr_month + "-" + curr_date); 
        })    
        
        
        $("#sendButton").click(function(){
          $('#errorMessage').html('');
          errorMessage = ''; 
          var PatientID = $('#PatientID').val();
          var dueDate = $('#dueDate').val();
          var priority = $('#priority:checked').val();
          var message = $("#message").val();  
          // todo : check if PatientID is numeric , no rush as this is checked in the php after the post
          
          // check to see if a recipient has been set
          // todo : check if they are all numeric , no rush as this is checked in the php after the post
          noRecipient = true;
          $('.sendTo').each(function(index) {
              if($(this).is(":checked")){
                noRecipient = false;
              }
          });
          if(noRecipient){
             errorMessage = errorMessage + '* <?php echo xla('Please Select A Recipient') ?><br />';
          }  
          
          
          // Check if Date is set
          // todo : add check to see if dueDate is a valid date , no rush as this is checked in the php after the post
          if(dueDate == ''){
             errorMessage = errorMessage + '* <?php echo xla('Please enter a due date') ?><br />';
          }        
          
          // check if message is set                                   
          if(message == ''){
             errorMessage = errorMessage + '* <?php echo xla('Please enter a message') ?><br />';
          }  
              
          if(errorMessage != ''){
            // handle invalid queries
            $('#errorMessage').html(errorMessage);
          }
          else{
            // handle valid queries
            // post the form to self 
            top.restoreSession();
            $("#addDR").submit();
          }
          return false;
        }) 
         
        $("#removePatient").click(function(){
          $("#PatientID").val("0");   
          $("#patientName").val("<?php echo xla('Click to select patient'); ?>");   
          $(this).hide();
          return false;
        })
      })       
    
        function sel_patient(){ 
           window.open('../../main/calendar/find_patient_popup.php', '_newDRPat', '' + ",width="   + 500 + ",height="  + 400 + ",left="    + 25  + ",top="     + 25   + ",screenX=" + 25  + ",screenY=" + 25); 
        } 
        
        function setpatient(pid, lname, fname, dob){ 
              $("#patientName").val(fname +' '+ lname)  
              $("#PatientID").val(pid);   
              $("#removePatient").show();
              return false;
        } 
        
        function limitText(limitField, limitCount, limitNum) {
        	if (limitField.value.length > limitNum) {
        		limitField.value = limitField.value.substring(0, limitNum);
        	} else {
        		limitCount.value = limitNum - limitField.value.length;
        	}
      }
    </script> 
<link rel="stylesheet" href="<?php echo $css_header;?>" type="text/css">
  </head>
  <body class="body_top">    
<!-- Required for the popup date selectors -->
<div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>


    <h1><?php echo xlt('Send a Reminder') ?></h1>
    <form id="addDR" style="margin-left:40px;" id="newMessage" method="post" onsubmit="return top.restoreSession()">
     <div style="text-size:2em; text-align:center; color:red" id="errorMessage"></div>   
     
        <?php echo xlt('Link To Patient') ?> :
        <input type='text' size='10' id='patientName' name='patientName' style='width:200px;cursor:pointer;cursor:hand' 
               value='<?php echo ($patientID > 0 ? attr(getPatName($patientID)) : xla('Click to select patient')); ?>' onclick='sel_patient()' 
               title='<?php xla('Click to select patient'); ?>' readonly /> 
        <input name="PatientID" id="PatientID" type="hidden" value="<?php echo (isset($patientID) ? attr($patientID) : 0) ?>" /> 
        <button <?php echo ($patientID > 0 ? '' : 'style="display:'.attr('none').'"') ?> id="removePatient"><?php echo xlt('unlink patient') ?></button> 
        
        
     <br /><br />  
     
        
     <?php echo xlt('Send to') ?> :  <p style="line-height:1.8em;">
               <input class="sendTo" type="checkbox" name="sendTo_me" value="<?php echo attr(intval($_SESSION['authId'])) ?>" id="me"><label for="me"><?php echo xlt('Myself') ?></label>&nbsp;&nbsp;&nbsp;&nbsp; 
                <?php //     
                    $uSQL = sqlStatement('SELECT id, fname,	mname, lname  FROM  `users` WHERE  `active` = 1 AND id != ?',array(intval($_SESSION['authId'])));
                    for($i=2; $uRow=sqlFetchArray($uSQL); $i++){  
                      echo '<input class="sendTo" type="checkbox" name="sendTo_',$i,'" id="sendTo_',$i,'" value="',attr($uRow['id']),'"><label for="sendTo_',$i,'">',text($uRow['fname'].' '.$uRow['mname'].' '.$uRow['lname']),'</label>&nbsp;&nbsp;&nbsp;&nbsp; ';
                      // line break for every 4 users
                      if($i % 4 == 0) echo "<br />";  
                    }
                ?>      
               </p> 
               
               
               
                   
      <?php echo xlt('Due Date') ?> : <input type='text' name='dueDate' id="dueDate" size='20' value="<?php echo ($this_message['dueDate'] == '' ? date('Y-m-d') : attr($this_message['dueDate'])); ?>" onkeyup='datekeyup(this,mypcc)' onblur='dateblur(this,mypcc)' title='<?php echo htmlspecialchars( xl('yyyy-mm-dd'), ENT_QUOTES); ?>' />      
      <?php echo xlt('OR') ?> 
      <?php echo xlt('Select a time span') ?> : <select id="timeSpan">
                                <option value="__BLANK__"> -- <?php echo xlt('Select a Time Span') ?> -- </option>
                                <?php 
                                  $optionTxt = '';
                                  foreach($dateRanges as $val=>$txt){   
                                    $optionTxt .= '<option value="'.attr($val).'">'.text($txt).'</option>';  
                                 } 
                                 echo $optionTxt;
                                ?>     
                             </select>    
      <br />         
      <br />
      <?php echo xlt('Priority') ?> :    
                 <input <?php echo ($this_message['message_priority'] == 3 ? 'checked="checked"' : '') ?> type="radio" name="priority" id="priority_3" value='3'> <label for="priority_3"><?php echo xlt('Low') ?></label> 
                 <input <?php echo ($this_message['message_priority'] == 2 ? 'checked="checked"' : '') ?> type="radio" name="priority" id="priority_2" value='2'> <label for="priority_2"><?php echo xlt('Medium') ?></label>   
                 <input <?php echo ($this_message['message_priority'] == 1 ? 'checked="checked"' : '') ?> type="radio" name="priority" id="priority_1" value='1'> <label for="priority_1"><?php echo xlt('High') ?></label> 
      <br />         
      <br />
      <?php echo xlt('Type Your message here') ?> : <br /><br />
      <font size="1">(<?php echo xlt('Maximum characters') ?>: 144)<br>
      <textarea onKeyDown="limitText(this.form.message,this.form.countdown,144);" 
                onKeyUp="limitText(this.form.message,this.form.countdown,144);" 
                style="width:98%; height:50px" name="message" id="message"><?php echo text($this_message['message']); ?></textarea>
      <br>
      <?php echo xlt('Characters Remaining') ?> : <input readonly type="text" name="countdown" size="3" value="144"> </font>                
      <br />
      <br />
      <input type="submit" id="sendButton" value="<?php echo xla('Send This Message') ?>" />
    </form>
  </body>  
<!-- stuff for the popup calendar -->
<style type="text/css">@import url(<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.css);</style>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar.js"></script>
<?php include_once("{$GLOBALS['srcdir']}/dynarch_calendar_en.inc.php"); ?>
<script type="text/javascript" src="<?php echo $GLOBALS['webroot'] ?>/library/dynarch_calendar_setup.js"></script>
<script language="Javascript"> 
  Calendar.setup({inputField:"dueDate", ifFormat:"%Y-%m-%d", button:"img_begin_date", showsTime:'false'}); 
</script>
</html>