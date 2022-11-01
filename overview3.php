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
 */
global $DB;

require_once('../../config.php');
require_once('../../completion/classes/external.php');
require_login();

# $categories = get_category();

 # var_dump($courses);
 # var_dump($categories);
 # die();
#
// Ambil parameter id dari URL
$id = optional_param('id', 0, PARAM_INT);// Course ID.

$course = get_course($id);

 # var_dump($course->fullname);
 # die();
$courseid = $id;
// $courseid = $COURSE->id;
# $courseidx = $_GET('courseidx');
# echo "Test";
# echo $_REQUEST('courseidx');
# echo $courseidx;
# die();

# foreach ($courses as $courseid => $course){
 
# }
# 

$context = context_course::instance($courseid);
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/block/pintar_analytic/overview2.php'));
$PAGE->set_pagelayout('course');
$PAGE->set_title($SITE->fullname);
# $string['pluginname']='Greetings';
# $PAGE->set_heading(get_string('pluginname','block_pintar_analytic'));
$PAGE->set_heading('Peringkat Progress Peserta');

echo $OUTPUT->header();

if (isloggedin()) {
    # echo '<h2>PIC: ' . fullname($USER) . '</h2>';
} else {
    echo '<h2>Anda belum login</h2>';
}

# echo '<h2>Greetings, ' . fullname($USER) . '</h2>';

// 
// Membaca data yang dikirim melalui URL berupa array yang dikirim menggunakan 
// $url + http_build_query($dataid);
//
$idArray = explode('&',$_SERVER["QUERY_STRING"]);
foreach ($idArray as $index => $avPair) {
 list($ignore, $value) = explode('=',$avPair);
 $id[$index] = $value;
}

# echo $OUTPUT->render(report_course($id,$course->shortname));
report_course($id,$course->shortname);

echo $OUTPUT->footer();

// Kumpulan fungsi
// ===============

// function membuat chart sebuah course
//

function report_course($id,$fullname){
	# echo '<h2>Course ID: <a href="https://staging.pintartanoto.id/course/view.php?id='.$id.'">'. $id.'</a></h2>';
    $url = new moodle_url('/course/view.php',array('id'=>$id));
    echo '<h2>Course: <a href='.$url.'>'. $fullname.'</a></h2>';
    # echo '<h2>Course: <a href="https://staging.pintartanoto.id/course/view.php?id='.$id.'">'. $fullname.'</a></h2>';
    $coursecontext = context_course::instance($id);
    $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');

    $totalenrolledstudents = count($enrolledstudents);

    $already100=0;
    $already70=0;
    $still30=0;
    $persen100=0;
    $persen70=0;
    $persen30=0;
    $prosen_assignmentcompleted=0; //di atas 90%
    $total_assignmentcompleted=0; //jumlah user yang assignment completed di atas 90%
    $total_prosen_assignmentcompleted=0; //di atas 90%
    $total_assignmentNOTcompleted=0; //di atas 90%
    $prosen_total_assignmentcompleted=0;

	echo "<b>Peringkat 10 besar Peserta </b>";
    $peringkatusers = array();
    foreach ($enrolledstudents as $user) {
	# var_dump($user);
	# die();

	   //Menghitung status setiap user
        $course_user_stat = custom_get_user_course_completion($id,$user->id);

        $activities = $course_user_stat['statuses'];
	// Banyaknya aktivitas
	# var_dump($activities);
	# die();

        $totalactivities = count($activities);
        $usertotalactivities = 0; 
         foreach ($activities as $totalactivity){
		 if($totalactivity["timecompleted"]>0){
			 $usertotalactivities+=1;
		 };
	#   
	 }
	#if($usertotalactivities == 0){
		# echo '<li>'.$user->firstname.' '.$user->lastname.': '.$usertotalactivities.'</li>';
		# echo '<li>'.$user->firstname.' '.$user->lastname.'</li>';
		$fullname = $user->firstname.' '.$user->lastname;
		$peringkatusers += [$fullname => $usertotalactivities];	
	# }
    } 
	arsort($peringkatusers);
	   # var_dump($peringkatuser);
	   # die();
    //Cetak peringkat

    $urutan = 0;
    echo '<ol>';
    foreach($peringkatusers as $key => $value){
	    $urutan+=1;
	    echo '<li>'.$key.': '.number_format(($value/$totalactivities*100),2).'%</li>';
	    if($urutan >= 10){
		    $urutan=0;
		    break;
	    } 
    }

	echo '</ol>';

                // Nilai Prosentase
        

            return;
}

function custom_get_user_course_completion($courseid,$userid){
        $course = get_course($courseid);
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
