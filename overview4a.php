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

$context = context_course::instance($courseid);
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/block/pintar_analytic/overview4a.php'));
$PAGE->set_pagelayout('course');
$PAGE->set_title($SITE->fullname);
# $string['pluginname']='Greetings';
# $PAGE->set_heading(get_string('pluginname','block_pintar_analytic'));
$PAGE->set_heading('Evaluasi Pre & Post Test');

echo $OUTPUT->header();

//--
// Utk sumberdata chart 
//
  $Qmudah = 0;
  $Qsedang = 0;
  $Qsulit = 0;
  $Kelipatansoal=12;
  $QmudahPre = 4;
  $QsedangPre = 0; 
  $QsulitPre = 0;
  $QmudahPost= 0; 
  $QsedangPost= 0; 
  $QsulitPost= 0;
//--


if (isloggedin()) {
    # echo '<h2>PIC: ' . fullname($USER) . '</h2>';
} else {
    echo '<h2>Anda belum login</h2>';
}

echo '<div class="container">';
echo '<div class="row">';
echo '<div class="col-md-8">';
# echo $OUTPUT->render(report_course($id,$course->shortname));
header_report($id,$course->shortname);

//--
// Data-data prepost quiz
//

report_prepost($id);

//--
echo $OUTPUT->render(displayChart());

echo '</div></div></container>';
echo $OUTPUT->footer();

// Kumpulan fungsi
// ===============

// function membuat chart sebuah course
//

function header_report($id,$shortname) {
    $url = new moodle_url('/course/view.php',array('id'=>$id,'sm'=>1));
    echo '<h2>Course: <a href='.$url.'>'. $shortname.'</a></h2>';
}

function displayChart() {
	global $Qmudah, $Qsedang, $Qsulit;
	global $QmudahPre, $QsedangPre, $QsulitPre;
	global $QmudahPost, $QsedangPost, $QsulitPost;

        $arrlabels = array('Soal mudah','Soal sedang','Soal sulit');

        $chart = new core\chart_bar();
            $chart->set_horizontal(true);

            $serie1 = new core\chart_series('Rata-rata pre', [$QmudahPre, $QsedangPre,$QsulitPre]);
            $serie2 = new core\chart_series('Rata-rata post', [$QmudahPost,$QsedangPost ,$QsulitPost]);

            $chart->set_title('Analisa butir soal');
            $chart->add_series($serie1);
            $chart->add_series($serie2);


            $chart->set_labels($arrlabels);

            // legend position
            $chart->set_legend_options(['position' => 'bottom', 'reverse' => true]);
            # $chart->set_title_options(['fontsize' => '15']);

            return($chart);
}


function report_prepost($courseid) {
	global $DB;
	global $Qmudah, $Qsedang, $Qsulit;
	global $QmudahPre, $QsedangPre, $QsulitPre;
	global $QmudahPost, $QsedangPost, $QsulitPost;
	global $qpre;

	$Qid = array();
	$Qid2 = array();
	$sql1 = "Select * from {quiz} where course = :courseid AND (name like '[PRE%' OR name like '[POST%')";
	#  $sql1 = "Select * from {quiz} where course = :courseid ";
	# $prepostQuiz = $DB->get_records_sql($sql1,['courseid'=>$courseid]);
	$prepostQuizByCourse = $DB->get_records_sql($sql1,['courseid'=>$courseid]);
	 #  print_object($prepostQuizByCourse);
	 # die();
	$prepostQuizByCourseArray = json_decode(json_encode($prepostQuizByCourse),true);

	if(count($prepostQuizByCourseArray)<2){
		echo 'Mohon maaf, Anda tidak memiliki PreTest dan atau PostTest, atau kurang salah satunya';
		return;
	} 
	else 
	{
	echo 'Sumber data:<br>';
	foreach($prepostQuizByCourseArray as $prepostQuiz) {
		 $quizid = $prepostQuiz['id'];
		 # $quizname  = $prepostQuiz['id'].' - '.$prepostQuiz['name'];
		 $quizname  = $quizid.' - '.$prepostQuiz['name'];
		 echo $quizname.'<br>';
	}	
	echo "<br>";
	//--
	// Data setiap quiz Pre dan Post Test
	//
		
	$usageloop=1;
	$qpre=1;
	  # foreach($prepostQuizArray as $prepostQuiz) {
	foreach($prepostQuizByCourseArray as $prepostQuiz) {
	# $usageloop+=1;	
		# var_dump($prepostQuiz);
		# print_object($prepostQuiz);
		# die();	
		 $quizid = $prepostQuiz['id'];
		 # $quizname  = $prepostQuiz['id'].' - '.$prepostQuiz['name'];
		 $quizname  = $quizid.' - '.$prepostQuiz['name'];
		 ## echo $quizname.'<br>';
		 $questionList = questionsByQuiz($prepostQuiz['id']);
		 $totalQuestion = count($questionList);
  		 $Kelipatansoal=$totalQuestion;
		 ## echo 'Jumlah Soal: '.$totalQuestion.'<br>';
		 // Question List
		 # foreach ($questionList as $questionItem) { 
		 #         array_push($Qid, array("qattemptid"=>$questionItem->id,"quizid"=>$quizid,"qid"=>$questionItem->questionid));
		 #         # echo $questionItem->slot.'-'.$questionItem->questionid.'<br>';
		 # }
		 ## echo "<br>";
		# print_object($Qid);
	 	# die();

		 ## echo 'Data Attempt quiz : '.$quizid.'<br>';
		 $answer_each_questions = questions_answer_byQuiz2($quizid);

		 $slotid = 0;
		 $Qmudah = 0;
		 $Qsedang = 0;
		 $Qsulit= 0;
		 # $usageloop = 1;
		 # $QmudahPre = 0;
		 # $QsedangPre = 0;
		 # $QsulitPre= 0;
		 # $QmudahPost = 0;
		 # $QsedangPost = 0;
		 # $QsulitPost= 0;
		  foreach ($answer_each_questions  as $answer_questionItem) { 
			  $slotid+=1; 		
			  if ($slotid>$Kelipatansoal) {
				  $slotid=1;
				  # $usageloop+=1;
			  }
			  array_push($Qid2, array("slotid"=>$slotid,"qusage_id"=>$answer_questionItem->id,
				  "unique_id"=>$answer_questionItem->uniqueid,
				  "quizid"=>$answer_questionItem->quiz,
				  "questid"=>$answer_questionItem->questionid,
				  "fraction"=>$answer_questionItem->fraction
			  ));
		 	// Jika soal mudah	
			if ($slotid<3) {
			$Qmudah+=$answer_questionItem->fraction;
			}

		 	// Jika soal sedang	
			if ($slotid>2 and $slotid<11) {
			$Qsedang+=$answer_questionItem->fraction;
			}

		 	// Jika soal sulit
			if ($slotid>10 and $slotid<$Kelipatansoal+1) {
			$Qsulit+=$answer_questionItem->fraction;
			}

				}
		 ## echo "<br>";
		 ## echo "Soal mudah terjawab:".$Qmudah."<br>";
		 ## echo "Soal sedang terjawab:".$Qsedang."<br>";
		 ## echo "Soal sulit terjawab:".$Qsulit."<br>";
		 
			if($qpre==1) {
		           $QmudahPre = $Qmudah;
		           $QsedangPre = $Qsedang;
			   $QsulitPre= $Qsulit;
			   $qpre=2;
			} else {
		           $QmudahPost = $Qmudah;
		           $QsedangPost = $Qsedang;
		           $QsulitPost= $Qsulit;
			}

		 # print_object($answer_each_question);
	  }
		  # print_object($Qid2);
	}
}

function questions_answer_byQuiz2 ($quizid){
        global $DB;

        $sql4 = "select mq.id, mqa.id, mqas.id, mqa2.id, mq.course , mqa2.quiz as quiz, mqa2.userid , mqa2.uniqueid, mqa.questionid , mqa.id, mqas.fraction  
  from {quiz_attempts} mqa2  
    left join {question_attempts} mqa on mqa2.uniqueid  = mqa.questionusageid 
      left join {question_attempt_steps} mqas on mqas.questionattemptid = mqa.id
         left join {quiz} mq on mq.id = mqa2.quiz 
   where mqas.sequencenumber =2 and quiz = ".$quizid;
        # $questions_answer_byQuiz = $DB->get_records_sql($sql4,['mqa2.quiz'=>$quizid]);
        $questions_answer_byQuiz = $DB->get_records_sql($sql4);
         # var_dump($questions_answer_byQuiz);
         # print_object($questions_answer_byQuiz);
         # die();
        return $questions_answer_byQuiz;
}

function questions_answer_byQuiz ($quizid){
	global $DB;

	$sql3 = "select mqs.id, mqas.id, mqa.id, mqs.quizid , mqa.questionusageid , mqs.slot  , mqs.questionid, mqas.fraction, mqas.userid   
  from {quiz_slots} mqs 
    left join {question_attempts} mqa on mqs.questionid = mqa.questionid
      left join {question_attempt_steps} mqas on mqa.id = mqas.questionattemptid 
   where mqs.quizid =".$quizid." and mqas.sequencenumber = 2";
	$questions_answer_byQuiz = $DB->get_records_sql($sql3,['quizid'=>$quizid]);
	# var_dump($questionsByQuiz);
	# die();
	return $questions_answer_byQuiz;
}

function questionsByQuiz ($quizid){
	global $DB;

	$sql2 = "SELECT * FROM {quiz_slots} where quizid = :quizid";
	$questionsByQuiz = $DB->get_records_sql($sql2,['quizid'=>$quizid]);
	# var_dump($questionsByQuiz);
	# die();
	return $questionsByQuiz;
}


function report_course($id,$shortname){
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
    //
   	# echo '<table border=1>'; 
   	# echo '<tr>'; 
        # echo '<td>Total students: '.$totalenrolledstudents.'</td>';
        # echo '<td>'.'Total activities:'.$totalactivities.'</td>';
   	# echo '</tr>'; 
   	# echo '</table>'; 
        # echo 'Total Complete Assignment:'.$total_assignmentcompleted."<br>";
        # echo 'Diatas 70%:'.$already70."<br>";
        # echo 'Dibawah 30%:'.$still30."<br>";
        # echo 'Total Penugasan > 90%:'.$total_assignmentcompleted." orang<br>";
        # echo 'Total Penugasan < 90%:'.$total_assignmentNOTcompleted." orang<br>";
        # echo 'Prosentase Penugasan > 90%: <b>'.number_format($prosen_total_assignmentcompleted,2)."</b> %<br>";
        
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


	$arrlabels = array('Soal sulit','Soal sedang','Soal mudah');

	$chart = new core\chart_bar();
	    $chart->set_horizontal(true);

            $serie1 = new core\chart_series('Rata-rata post', [16, 8.5,10]);
            $serie2 = new core\chart_series('Rata-rata pre', [10, 8.5, 2]);

            $chart->set_title('Analisa butir soal');
            $chart->add_series($serie1);
            $chart->add_series($serie2);


            $chart->set_labels($arrlabels);
	
	    // legend position
	    $chart->set_legend_options(['position' => 'bottom', 'reverse' => true]);
	    # $chart->set_title_options(['fontsize' => '15']);

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
