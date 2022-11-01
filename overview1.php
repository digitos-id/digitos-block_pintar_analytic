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

foreach ($courses as $courseid => $course){
#         if($course->id==1)continue;
#         $coursecontext = context_course::instance($course->id);
#         $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
#         $already70='';
#         $still30='';
#         foreach ($enrolledstudents as $user) {
#                 $course_user_stat = core_completion_external::get_activities_completion_status($course->id,$user->id);
#                 $activities = $course_user_stat['statuses'];
#                 $totalactivities = count($activities);
#                 $completed = 0;
#                 foreach($activities as $activity){
#                         if($activity['timecompleted']!=0)$completed+=1;
#                 }
#                 $studentcompletion=($completed/$totalactivities)*100;
#                 if($studentcompletion>70)$already70+=1;
#                 else $still30 +=1;
# 
#         }
#         echo $course->fullname." diatas 70%: ".$already70."<br>";
#         echo $course->fullname." dibawah 30%: ".$still30."<br>";
# 
}
# 
# die();

$context = context_course::instance($courseid);
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/block/pintar_analytic/overview1.php'));
$PAGE->set_pagelayout('course');
$PAGE->set_title($SITE->fullname);
# $string['pluginname']='Greetings';
# $PAGE->set_heading(get_string('pluginname','block_pintar_analytic'));
$PAGE->set_heading('Tingkat Penyelesaian');

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
// $idArray = explode('&',$_SERVER["QUERY_STRING"]);
// foreach ($idArray as $index => $avPair) {
//  list($ignore, $value) = explode('=',$avPair);
//  $id[$index] = $value;
// }
$url = new moodle_url('/course/view.php',array('id'=>$courseid));
echo '<div><h2>Course: <a href='.$url.'>'. $fullname.'</a></h2></div>';
// echo '<div><h3>Course: <a href="https://staging.pintartanoto.id/course/view.php?id='.$id.'">'.$course->shortname.'</a></h3></div>';
echo '<div>Keterlibatan dan Keaktifan Peserta</div>';
echo '<div class="container">';
echo '<div class="row">';
echo '<div class="col-md-8">';
echo $OUTPUT->render(report_course($id,$course->shortname));
echo '</div></div></container>';

echo $OUTPUT->footer();

// Kumpulan fungsi
// ===============

// function membuat chart sebuah course
//

function report_course($id,$fullname){
    # echo '<h2>Course ID: <a href="https://staging.pintartanoto.id/course/view.php?id='.$id.'">'. $id.'</a></h2>';
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

   foreach ($enrolledstudents as $user) {
        //Menghitung status setiap user
        $course_user_stat = custom_get_user_course_completion($id,$user->id);

        $activities = $course_user_stat['statuses'];
        // Banyaknya aktivitas
        $totalactivities = count($activities);
               // nilai awal setiap user
                $completed = 0;
                $iscomplete = false;
                $jum_assignment = 0;
                $jum_assignmentcompleted = 0;
                $prosentase_assignmentcomplete = 0;

                foreach($activities as $activity){

                    if($activity['modname']=='assign')$jum_assignment+=1;
                     if($activity['timecompleted']!=0 &&
                        $activity['modname']=='assign')$jum_assignmentcompleted+=1;

                    # var_dump($activity['modname'],$assigncount);
                    # die();

                    if($activity['timecompleted']!=0)$completed+=1;
                }

                $prosen_assignmentcompleted = $jum_assignmentcompleted / $jum_assignment * 100;

                  # var_dump($completed, $jum_assignment, $jum_assignmentcompleted,$prosen_assignmentcompleted);
                  # die();
                if ($prosen_assignmentcompleted >=90)$total_assignmentcompleted+=1;

                if($totalactivities>0){
			$studentcompletion=($completed/$totalactivities)*100;
		} else {$studentcompletion=1;}
                # $studentcompletion=($completed/$totalactivities)*100;
                if($studentcompletion==100)$already100+=1;
                if($studentcompletion>69)$already70+=1;
                else $still30 +=1;


        }

	// End of hitung completion
     // Nilai Prosentase
        $total_assignmentNOTcompleted=$totalenrolledstudents-$total_assignmentcompleted;
        $prosen_total_assignmentcompleted = ($total_assignmentcompleted / $totalenrolledstudents)*100;
        $persen100 = $already100/$totalenrolledstudents*100;
        $persen70 = $already70/$totalenrolledstudents*100;
        $persen30 = $still30/$totalenrolledstudents*100;
        
        $arrpersen100 = array($persen100);
        $arrpersen30 = array($persen30);
        $arrpersen70 = array($persen70);
        $arrpta = array($prosen_total_assignmentcompleted);
	$arrlabels = array(['All Group']);

        $chart = new core\chart_bar();
            $serie1 = new core\chart_series('Penyelesaian <30%', $arrpersen30);
            $serie2 = new core\chart_series('Penyelesaian >70%', $arrpersen70);
            $serie3 = new core\chart_series('Penugasan >90%', $arrpta);
            # $serie2 = new core\chart_series('Penyelesaian >70%', [$persen70]);
            # $serie3 = new core\chart_series('Penugasan >90%', [$prosen_total_assignmentcompleted]);
            # $serie3 = new core\chart_series('Penugasan >90%', [16, 8.5,7.6,20.3 ]);

            # array_push($serie1, 20):
            # array_push($serie2, 20):
            # array_push($serie3, 20):

            # $chart->set_title('Keterlibatan dan Keaktifan Peserta');
            $chart->add_series($serie1);
            $chart->add_series($serie2);
            $chart->add_series($serie3);


            $chart->set_labels($arrlabels);
            # $yaxis = $chart->get_yaxis(1,true);
            # $yaxis->set_max(50);
            # $yaxis->set_min(0);
            # $yaxis->title(Dalam %');

            return($chart);
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
