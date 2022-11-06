<?php
require_once('../../config.php');
require_once('../../completion/classes/external.php');
require_login();

$id = $_POST['id'];

$course = context_course::instance($id);
$enrolled_users = get_enrolled_users($course, 'moodle/course:isincompletionreports');

$total_enrolled_user = count($enrolled_users);

$already100 = 0;
$already70 = 0;
$still30 = 0;
$persen100 = 0;
$persen70 = 0;
$persen30 = 0;
$prosen_assignmentcompleted = 0; //di atas 90%
$total_assignmentcompleted = 0; //jumlah user yang assignment completed di atas 90%
$total_prosen_assignmentcompleted = 0; //di atas 90%
$total_assignmentNOTcompleted = 0; //di atas 90%
$prosen_total_assignmentcompleted = 0;

foreach ($enrolled_user as $user) {
	//Menghitung status setiap user
	//$course_user_stat = custom_get_user_course_completion($id,$user->id);

        $activities = $course_user_stat['statuses'];
        // Banyaknya aktivitas
        $totalactivities = count($activities);
	// nilai awal setiap user
	$completed = 0;
	$iscomplete = false;
	$jum_assignment = 0;
	$jum_assignmentcompleted = 0;
	$prosentase_assignmentcomplete = 0;
	
	foreach($activities as $activity) {
		if($activity['modname']=='assign')$jum_assignment+=1;
		if($activity['timecompleted']!=0 &&
			$activity['modname']=='assign')$jum_assignmentcompleted+=1;
		
		if($activity['timecompleted']!=0)$completed+=1;
	}
	
	$prosen_assignmentcompleted = $jum_assignmentcompleted / $jum_assignment * 100;

	$total_assignmentcompleted = ($prosen_assignmentcompleted >= 90) ? $total_assignmentcompleted + 1 : 0;
	
	if ($totalactivities>0) {
		$studentcompletion=($completed/$totalactivities)*100;
	} else {
		$studentcompletion=1;
	}
	# $studentcompletion=($completed/$totalactivities)*100;
	if ($studentcompletion==100) {
		$already100+=1;
	}
	if ($studentcompletion>69) {
		$already70+=1;
	} else {
		$still30 +=1;
	}
}
// End of hitung completion
//
# echo 'Total students:'.$totalenrolledstudents."<br>";
# echo 'Total activities:'.$totalactivities."<br>";
# echo 'Selesai 100%:'.$already100."<br>";
# echo 'Dibawah 100%:'.($totalenrolledstudents-$already100).'<br>';

$persen100 = ($already100/$total_enrolled_user)*100;
$persenKurang100 = 100 - $persen100;
// echo 'Prosentase selesai 100%: <b>'.number_format($persen100,2)."</b> %<br>";exit;

// Nilai Prosentase
$persen70 = $already70/$totalenrolledstudents*100;
$persen30 = $still30/$totalenrolledstudents*100;

$arrpersen100 = array($persen100);
$arrpersen30 = array($persen30);
$arrpersen70 = array($persen70);
$arrpta = array($prosen_total_assignmentcompleted);
$arrlabels = array('Selesai 100%','Dibawah 100%');

echo json_encode([
	'status' => 'sukses',
	'total_enrolled_user' => $total_enrolled_user,
	'persen100' => $persen100,
	'persenKurang100' => $persenKurang100,
]);

function custom_get_user_course_completion($courseid,$userid) {
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
