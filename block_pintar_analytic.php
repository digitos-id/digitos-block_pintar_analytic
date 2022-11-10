<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

// require_once('../../config.php'); //disesuaikan path nya

require_once($CFG->dirroot.'/completion/classes/external.php');
require_once($CFG->dirroot.'/grade/querylib.php');
require_once($CFG->libdir.'/gradelib.php');
require_once($CFG->libdir.'/pagelib.php');

//require_once($CFG->wwwroot.'/completion/classes/external.php');
// require_once dirname(dirname(dirname(FILE))).'/completion/classes/external.php);
/**
 * Pintar Analytic Dashboard block definition
 *
 * @package    block_pintar_analytic
 * @copyright  2022 Prihantoosa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//Trick untuk membatasi popup frequency
$sm = optional_param('sm', 0, PARAM_INT);

 use block_pintar_analytic\pintar_analytic;
 use block_pintar_analytic\defaults;

 /**
 * Pintar Analytic Dashboard block class
 *
 * @copyright 2022 Priihantoosa
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_pintar_analytic extends block_base {

    /**
     * Sets the block title
     *
     * @return void
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_pintar_analytic');
    }

    /**
     *  we have global config/settings data
     *
     * @return bool
     */
    public function has_config() {
        return true;
    }

    /**
     * Controls the block title based on instance configuration
     *
     * @return bool
     */
    public function specialization() {
        if (isset($this->config->progressTitle) && trim($this->config->progressTitle) != '') {
            $this->title = format_string($this->config->progressTitle);
        }
    }

    /**
     * Controls whether multiple instances of the block are allowed on a page
     *
     * @return bool
     */
    public function instance_allow_multiple() {
        return !self::on_site_page($this->page);
    }

    /**
     * Controls whether the block is configurable
     *
     * @return bool
     */
    public function instance_allow_config() {
        return !self::on_site_page($this->page);
    }

    /**
     * Defines where the block can be added
     *
     * @return array
     */
    public function applicable_formats() {
        return array(
            'course-view'    => true,
            'site'           => true,
            'mod'            => false,
            'my'             => true
        );
    }

    /**
     * Creates the blocks main content
     *
     * @return string
     */
    public function get_content() {
    global $COURSE, $DB, $OUTPUT,$USER;

    // mengambil nilai parameter sm
        //
        $url = 'https://'.$_SERVER['HTTP_HOST'];
        $url .= $_SERVER['REQUEST_URI'];
        $url_components = parse_url($url);
        parse_str($url_components['query'],$params);
        $sm = $params['sm'];
        # var_dump($url);
        # die();
        //
        //

    # var_dump($COURSE);
    # die();

        // If content has already been generated, don't waste time generating it again.
        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';
        // $barinstances = array();

        // Guests do not have any progress. Don't show them the block.
        if (!isloggedin() or isguestuser()) {
            return $this->content;
        }

        // if (self::on_site_page($this->page)) {
	// Rencana untuk memilih category
	//
		
        // if ($PAGE->id < 2) {
	if ($COURSE->id == 1) {

            $this->content->text = '';
	// ---
         #   $this->content->text .= '<em>Report by Category:</em><br><ul>';
	 #   $catlist = $DB->get_records('course_categories',[]);
	 #     foreach($catlist as $key){
	 #         $this->content->text .= '<li>'.$key->name.'</li>';    
	 #     }
	 #         $this->content->text .= '</ul>';    
            # $this->content->text .= '<em>Report by Category:</em><br><ul><li>Cat 1</li><li>Cat 2</li></ul>';
	    # $this->content->text .= 'Course ID:'.$COURSE->id;
	 // ---
            return $this->content;
        } else {
            # $this->content->text .= 'Course Analytics<br>';

            // Hitung completion
            $courseid = $COURSE->id;
            # $this->content->text .= 'Course id'.$courseid.'<br>';
            $coursecontext = context_course::instance($courseid);
            $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports');
            
            
            $this->content->text .= '<b>Tingkat Penyelesaian</b>';
            $url = new moodle_url('/blocks/pintar_analytic/overview0.php',array('id'=>$courseid));    
            $this->content->text .= '<ul><li><a href="'.$url.'">Persentase Peserta Selesai</a></li>';
            
            $url = new moodle_url('/blocks/pintar_analytic/overview1.php',array('id'=>$courseid));    
	        $this->content->text .= '<li><a href="'.$url.'">Persentase Penyelesaian Aktivitas</a></li></ul>';

	        $this->content->text .= '<b>Peserta</b>';
            $url = new moodle_url('/blocks/pintar_analytic/overview2.php',array('id'=>$courseid));    
	        $this->content->text .= '<ul><li><a href="'.$url.'">Peringkat Keaktifan Peserta </a></li></ul>';

            $this->content->text .= '<b>Analisa Pre/Post</b>';
            $url = new moodle_url('/blocks/pintar_analytic/overview4a.php',array('id'=>$courseid));
            $this->content->text .= '<ul><li><a href="'.$url.'">Analisa Butir Soal</a></li></ul>';

	    # $this->content->text .= "Sdr. ".$USER->firstname.",<br>";  
	     $datacourse = get_course($courseid);
	    # $this->content->text .= 'Course Anda akan berakhir pada:<br>'.date(" jS \of F Y",$datacourse->enddate);
	    # 
	    # $resultkrb = grade_get_course_grades($courseid, $USER->id);
	    # $grd = $resultkrb->grades[$USER->id]; 
	    # $this->content->text .= '<br>Grade: '.$grd->str_grade;
	    # $usercompletion = $this->custom_get_user_course_completion($courseid,$USER->id);
	    # $usercompletion = $this->report_course2($courseid,$USER->id);
	    # var_dump($usercompletion);
	    # die();

//----
        // Proses mengambil info untuk diletakkan di popup
        //
        $course_user_stat = $this->custom_get_user_course_completion($courseid,$USER->id);
        $activities = $course_user_stat['statuses'];

        // Total activities yang ada keseluruhan
        $totalactivities = count($activities);
        # var_dump($totalactivities);
        # die(); 

        // Total activities yang ada untuk setiap user saat ini
        $usertotalactivities = 0;
        foreach ($activities as $totalactivity){
            if($totalactivity["timecompleted"]>0){
               $usertotalactivities+=1;
               }
        }
        $prosenuseractivities = number_format($usertotalactivities/$totalactivities*100,2);
//
//----

	    $this->content->text .= '<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>';
	    $this->content->text .= '<script>';
	    $this->content->text .= 'let no = 0;';
	    $this->content->text .= 'function pesan() {';
	    $this->content->text .= 'if (no = 0) { swal("Sdr. '.$USER->firstname.'\nCourse Anda akan berakhir pada: \n'.date('jS \of F Y', $datacourse->enddate).'") }';
	    $this->content->text .= 'let no = 1;';
	    $this->content->text .= '}';
	    // $this->content->text .= '<script>swal("Sdr. '.$USER->firstname.'\nCourse Anda akan berakhir pada: \n'.date('jS \of F Y', $datacourse->enddate).'");</script>';
	    $this->content->text .= 'pesan();</script>';
	    #  var_dump($USER);
	    #  die();

	    # $url = new moodle_url('/blocks/pintar_analytic/overview3.php',array('id'=>$courseid));    
	    # $this->content->text .= '<li><a href="'.$url.'">Peringkat Peserta</a></li></ul>';

	    # $this->content->text .= '<b>Kategori</b>';
            # $url = new moodle_url('/blocks/pintar_analytic/overview1a.php',array('id'=>$courseid,'catid'=>$COURSE->category));    
	    # $this->content->text .= '<li><a href="'.$url.'">Kategori </a></li></ul>';


        }
	// Notice: Trying to get property 'wwwroot' of non-object in /var/www/lms.digitos.id/blocks/pintar_analytic/block_pintar_analytic.php on line 135
	//
        $this->tool_mytool_before_footer;	
	echo $OUTPUT->notification('<center>Perhatian</center>', 'warning');
        return $this->content;
    }
    
    
    public static function tool_mytool_before_footer() {
    global $PAGE;
    $PAGE->requires->js_init_code("alert('before_footer');");
         echo $OUTPUT->notification('Perhatian', 'warning');
    }
/**
     * Checks whether the given page is site-level (Dashboard or Front page) or not.
     *
     * @param moodle_page $page the page to check, or the current page if not passed.
     * @return boolean True when on the Dashboard or Site home page.
     */
    public static function on_site_page($page = null) {
        global $PAGE;   // phpcs:ignore moodle.PHP.ForbiddenGlobalUse.BadGlobal

        $page = $page ?? $PAGE; // phpcs:ignore moodle.PHP.ForbiddenGlobalUse.BadGlobal
        $context = $page->context ?? null;

        if (!$page || !$context) {
            return false;
        } else if ($context->contextlevel === CONTEXT_SYSTEM && $page->requestorigin === 'restore') {
            return false; // When restoring from a backup, pretend the page is course-level.
        } else if ($context->contextlevel === CONTEXT_COURSE && $context->instanceid == SITEID) {
            return true;  // Front page.
        } else if ($context->contextlevel < CONTEXT_COURSE) {
            return true;  // System, user (i.e. dashboard), course category.
        } else {
            return false;
        }
    }

/**
     * Menampilkan user-user yang enroled pada course tersebut.
     *
     * @return list_of_enrolled_userid.
     *   # Enroled users
     *   # by Toosa
     *  di awal hanya menampilkan course id nya saja
     */
    public static function siapasaja_enroled_users($courseid = null) {
        global $COURSE;   // 
      
        if ($courseid=null) {
            $courseid = $COURSE->id;
        } else {
            return true;
        }

        $context_course = context_course::instance($courseid);
        $enrolled_users = get_enrolled_users($context_course,'',0,'*');
        foreach ($enrolled_users as $enrolled_user) {
                $this->content->text .= $enrolled_user->id.'<br>';
                #echo "$enrolled_user";
            }

        
            # end of Enroled users
        
        return true;
        
    }    

    //
    // ---
    //
    //

# function report_course2($id,$groupid = 0){
# //
# // $id = course id
# // 
#     # $url = new moodle_url('/course/view.php',array('id'=>$id));
#     # echo '<h2>Course: <a href='.$url.'>'. $fullname.'</a></h2>';
#     $coursecontext = context_course::instance($id);
#     $enrolledstudents = get_enrolled_users($coursecontext, 'moodle/course:isincompletionreports', $groupid);
# 
#         // echo '<pre>';var_dump($enrolledstudents);echo '</pre>';exit;
# 
#     $totalenrolledstudents = count($enrolledstudents);
# 
#     $prosen_assignmentcompleted=0; //di atas 90%
#     $total_assignmentcompleted=0; //jumlah user yang assignment completed di atas 90%
#     $total_prosen_assignmentcompleted=0; //di atas 90%
#     $total_assignmentNOTcompleted=0; //di atas 90%
#     $prosen_total_assignmentcompleted=0;
#     $nourut = 0;
#     $prosenuseractivities = 0;
#     $hasil = array();
# 
#       # foreach ($enrolledstudents as $user) {
#          //Menghitung status setiap user
#          $course_user_stat = $this->custom_get_user_course_completion($id,$USER->id);
#          $activities = $course_user_stat['statuses'];
# 
#          // Total activities yang ada keseluruhan
#          $totalactivities = count($activities);
# 
#          // Total activities yang ada untuk setiap user
#          $usertotalactivities = 0;
#          foreach ($activities as $totalactivity){
#              if($totalactivity["timecompleted"]>0){
#                 $usertotalactivities+=1;
#                 }
#          }
#          $prosenuseractivities = number_format($usertotalactivities/$totalactivities*100,2);
#     # var_dump($id);
#      # var_dump($user->id);
#      # die();
#          $data_usergroup = get_user_group_by_userid_and_courseid($USER->id,$id);
# 
#        // 
#        // user grade
#        //
#         $resultkrb = grade_get_course_grades($id, $USER->id);
#         $grd = $resultkrb->grades[$USER->id];
#         $gradetotal = $grd->str_grade;
#         // echo $grd->str_grade;   
# 
#       // End Of user grade
#        //
#       # var_dump($data_usergroup['usergroupid']);
#       # var_dump($data_usergroup);
#       # die();
#        array_push($hasil,array("tactv"=>$usertotalactivities,"prouseractv"=>$prosenuseractivities,"gradetotal"=>$gradetotal));
#      # }
#       var_dump($hasil);
#       die();
#         // End of hitung completion
# 
#    return $hasil;
# }

       //
    //
    //---

    public static function custom_get_user_course_completion($courseid,$userid){
        $course = get_course($courseid);
        $user = core_user::get_user($userid, '*', MUST_EXIST);
        core_user::require_active_user($user);

        $completion = new completion_info($course);
        $activities = $completion->get_activities();
        $result = array();
        foreach ($activities as $activity) {

        $cmcompletion = \core_completion\cm_completion_details::get_instance($activity, $user->id);
        $cmcompletiondetails = $cmcompletion->get_details();

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
            'details'          => $details,
        ];
    }
    $results = array(
        'statuses' => $result,
    );
    return $results;

   }    
    
}
