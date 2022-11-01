<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>;.

/**
 * @package     blocks_pintar_analytic
 * @copyright   2022 Prihantoosa <toosa@digitos.id> 
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Rangking peserta yang menyelesaikan course activities nya
 *
 */
global $DB;

require_once('../../config.php');
require_once('../../completion/classes/external.php');
require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->dirroot.'/grade/querylib.php');
require_login();

# $categories = get_category();

 # var_dump($courses);
 # var_dump($categories);
 # die();
#
#

// Ambil parameter id dari URL
$id = optional_param('id', 0, PARAM_INT);// Course ID.
$catid = optional_param('catid', 0, PARAM_INT);// Category ID.i
$groupid = optional_param('group', 0, PARAM_INT);
# Public function untuk mengambil data course by id
#
# $course = get_course($id);
# 
# Hasilnya adalah: 
# " ["summaryformat"]=> string(1) "1" ["format"]=> string(6) "topics" ["showgrades"]=> string(1) "1" ["newsitems"]=> string(1) "5" ["startdate"]=> string(10) "1587661200" ["enddate"]=> string(10) "1619197200" ["relativedatesmode"]=> string(1) "0" ["marker"]=> string(1) "0" ["maxbytes"]=> string(1) "0" ["legacyfiles"]=> string(1) "0" ["showreports"]=> string(1) "0" ["visible"]=> string(1) "1" ["visibleold"]=> string(1) "1" ["downloadcontent"]=> NULL ["groupmode"]=> string(1) "0" ["groupmodeforce"]=> string(1) "0" ["defaultgroupingid"]=> string(1) "0" ["lang"]=> string(0) "" ["calendartype"]=> string(0) "" ["theme"]=> string(0) "" ["timecreated"]=> string(10) "1587611296" ["timemodified"]=> string(10) "1660553578" ["requested"]=> string(1) "0" ["enablecompletion"]=> string(1) "1" ["completionnotify"]=> string(1) "0" ["cacherev"]=> string(10) "1664764274" ["originalcourseid"]=> NULL ["showactivitydates"]=> string(1) "0" ["showcompletionconditions"]=> string(1) "1" }
#
#
#
$course = get_course($id);
$courseid = $id;

$coursecontext = context_course::instance($courseid);
# var_dump($course);
# die();

// 
// Set initial  variabel yang diperlukan
//

$totalactivities = 0;

//
// Set halaman/PAGE
//

$context = context_course::instance($courseid);
$PAGE->set_context($context);
# $PAGE->requires->jquery();
$PAGE->set_url(new moodle_url('/block/pintar_analytic/overview2a.php'));
$PAGE->set_pagelayout('course');
$PAGE->set_title($SITE->fullname);
# $PAGE->set_heading(get_string('pluginname','block_pintar_analytic'));
$PAGE->set_heading('Peserta Aktif/Tidak Aktif');
# $PAGE->requires->css('/block_pintar_analytic/style/datatable.css');
# $PAGE->requires->js_call_amd('/block_pintar_analytic/amd/src/datatable.css');
echo $OUTPUT->header();

if (isloggedin()) {
    # echo '<h2>PIC: ' . fullname($USER) . '</h2>';
} else {
    echo '<h2>Anda belum login</h2>';
}

// 
// Membaca data yang dikirim melalui URL berupa array yang dikirim menggunakan 
// $url + http_build_query($dataid);
// [deprecated]
//
$idArray = explode('&',$_SERVER["QUERY_STRING"]);
foreach ($idArray as $index => $avPair) {
 list($ignore, $value) = explode('=',$avPair);
 $id[$index] = $value;
}

# 
# Ambil data dan simpan di array $result 
# 
$result = report_course2($id,$course->shortname, $totalactivities, $groupid);
$result = array_values($result);

//
// Ambil data group
//

$data_group = groups_get_all_groups($id);
$arr_group = [];
foreach ($data_group as $value) {
	// echo $value->id;
	$group_arr['group_id'] = $value->id;
	$group_arr['group_name'] = $value->name;
	$arr_group[] = $group_arr;
}
// echo '<pre>';var_dump($arr_group);echo '</pre>';exit;

# foreach ($results as $result){
#  var_dump($result);
#  die();
# echo $OUTPUT->render_from_template('templates/userranking.mustache', $result);
# echo $OUTPUT->render_from_template('block_pintar_analytic/userranking', $result);
# }

$form_action = new moodle_url('/blocks/pintar_analytic/overview2a.php', ['id' => $id]);
 
echo $OUTPUT->render_from_template('block_pintar_analytic/userranking2', [
	'result' => $result,
	'totalactivities' => $totalactivities,
	'data_group' => $arr_group,
	'form_action' => $form_action,
	'id' => $id
]);

echo $OUTPUT->footer();

// Kumpulan fungsi
// ===============
//
// function: membentuk data coursecompletion by user
//

function report_course2($id,$fullname,&$totalactivities,$groupid = 0){
//
// $id = course id
// 
    $url = new moodle_url('/course/view.php',array('id'=>$id));
    echo '<h2>Course: <a href='.$url.'>'. $fullname.'</a></h2>';
    $coursecontext = context_course::instance($id);
    $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports', $groupid);

	// echo '<pre>';var_dump($enrolledstudents);echo '</pre>';exit;
    
    $totalenrolledstudents = count($enrolledstudents);

    $prosen_assignmentcompleted=0; //di atas 90%
    $total_assignmentcompleted=0; //jumlah user yang assignment completed di atas 90%
    $total_prosen_assignmentcompleted=0; //di atas 90%
    $total_assignmentNOTcompleted=0; //di atas 90%
    $prosen_total_assignmentcompleted=0;
    $nourut = 0;
    $prosenuseractivities = 0;
    $hasil = array();
    $gradetotal=0;

     foreach ($enrolledstudents as $user) {
         //Menghitung status setiap user
         $course_user_stat = custom_get_user_course_completion($id,$user->id);
	 $activities = $course_user_stat['statuses'];

	 // Total activities yang ada keseluruhan
	 $totalactivities = count($activities);

	 // Total activities yang ada untuk setiap user
         $usertotalactivities = 0; 
         foreach ($activities as $totalactivity){
             if($totalactivity["timecompleted"]>0){
         	$usertotalactivities+=1;
         	}
	 }
	 $prosenuseractivities = number_format($usertotalactivities/$totalactivities*100,2);
# number_format(($value/$totalactivities*100),2)
          # if($usertotalactivities => 0){
         	# echo '<li>'.$user->firstname.' '.$user->lastname.': '.$usertotalactivities.'</li>';
       # $hasil = array(array("id"=>$nourut,"nama"=>$user->firstname));
       # $nourut+=1;
	 # }
     # var_dump($id);
      # var_dump($id);
# 	  die();
       // 
       // user grade
       //
        
	$resultkrb = grade_get_course_grades($id, $user->id);
	$grd = $resultkrb->grades[$user->id];
	$gradetotal = $grd->str_grade; 
	// echo $grd->str_grade;   
   
       //
       // End Of user grade
       //


	// Data group
	$data_usergroup = array[];
	$data_usergroup = get_user_group_by_userid_and_courseid($user->id,$id);

     # var_dump($data_usergroup['usergroupid']);
     # die();
	array_push($hasil,array("userid"=>$user->id,"user_groupid"=>groups_get_group_name($data_usergroup['usergroupid']),"nama"=>$user->firstname.' '.$user->lastname,"tactv"=>$usertotalactivities,"prouseractv"=>$prosenuseractivities,"gradetotal"=>$gradetotal));

       # array_push($hasil,array("userid"=>$user->id,"user_groupid"=>$data_usergroup['usergroupid'],"nama"=>$user->firstname.' '.$user->lastname,"tactv"=>$usertotalactivities,"prouseractv"=>$prosenuseractivities));
     } 
     # var_dump($hasil);
     # die();
// End of hitung completion

   return $hasil;
}

function custom_get_user_course_completion($courseid,$userid){
	$course = get_course($courseid);
	$grupname='';

	        $user = core_user::get_user($userid, '*', MUST_EXIST);
        core_user::require_active_user($user);

        $completion = new completion_info($course);
        $activities = $completion->get_activities();
        $result = array();
        foreach ($activities as $activity) {

        $cmcompletion = \core_completion\cm_completion_details::get_instance($activity, $user->id);
        # print_objec_($activity->modname);


        # var_dump($modtype);
        # die();
        $cmcompletiondetails = $cmcompletion->get_details();
        # $cmcompletiondetails = $cmcompletion->get_details('modname');

        # var_dump($cmcompletiondetails);
        # die();

        $details = [];
        foreach ($cmcompletiondetails as $rulename => $rulevalue) {
            $details[] = [
                'rulename' => $rulename,
                'rulevalue' => (array)$rulevalue,
            ];
        }

        $result[]=[
            'state'         => $cmcompletion->get_overall_completion(),
            'timecompleted' => $cmcompletion->get_timemodified(),
            'overrideby'    => $cmcompletion->overridden_by(),
            'hascompletion'    => $cmcompletion->has_completion(),
            'isautomatic'      => $cmcompletion->is_automatic(),
            'istrackeduser'    => $cmcompletion->is_tracked_user(),
            'overallstatus'    => $cmcompletion->get_overall_completion(),
            'modname'           => $activity->modname,
            'details'          => $details,
        ];


        # var_dump($result);
        # die();
        }

    $results = array(
        'statuses' => $result,
    );
    return $results;

}

//
// Mencari group dari user pada course tertentu
// return: array
//

function get_user_group_by_userid_and_courseid($userid,$courseid) {
	global $DB;
	// 
	// fungsi groups_get_course_data($courseid) ada core function dari gropsAPI
	//
	$coursedata = groups_get_course_data($courseid);
	$data_usergroup = [];
	# $grup = groups_get_group_by_name($courseid, $grupname)

	$totgroup = count($coursedata->groups);
	$groupsid = [];
	# echo $totgroup.'<br>';
	foreach ($coursedata->groups as $group) {
		# foreach ($group as $data) {
		# 	echo $data['id'];
	        #  die();
	        # }
		$data = json_decode(json_encode($group),true);
	# 		echo $data['id'];
		# $data_usergroup['groupid'] =  $data['id'];
		# echo $data['name'];
	  # echo group['id'];
	# var_dump($data_usergroup);
		 $groupsid [] = $data['id'];
	   	 # var_dump($data['id']);
	         # die();
	}

	$groupmembers = $DB->get_records('groups_members',['userid'=>$userid],$fields = 'id,userid');
	# oupmembers = $DB->get_fieldset_select('groups_members','id',$select,['groupid'=>'35']);
        $groupmember = json_decode(json_encode($groupmembers),true);
	foreach($groupmember as $agroupmember) {
	    $usergroupid  = $agroupmember['groupid'];
	}
	 # var_dump($groupmember[1]);

	 # var_dump($groupsid);
	 # var_dump($usergroupid);
	 # die();
	$usergroupid = $usergroupid;

	# $groupmembers = groups_get_members($usergroupid, $fields='u.firstname', $sort='lastname ASC');

	# var_dump($groupmembers);
	# die();
	# Untuk mendapatkan nama group berdasarkan groupid
	# echo groups_get_group_name($usergroupid);
	# 	die();
	
	
	$data_usergroup['userid'] = $userid;
	$data_usergroup['usergroupid'] = $usergroupid;
		#  var_dump($coursedata);
	#  var_dump($coursedata->groups['id']);
	# die();
	return($data_usergroup);

}
