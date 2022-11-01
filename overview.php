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


/**
 * Pintar analytic block overview page
 *
 * @package    block_pintar_analytic
 * @copyright  2022 Prihntoosa
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Include required files.
//
require_once(dirname(__FILE__) . '/../../config.php');
require_once($CFG->dirroot.'/notes/lib.php');
require_once($CFG->libdir.'/tablelib.php');

use block_pintar_analytic\pintar_analytic;

/**
 * Default number of participants per page.
 */
const DEFAULT_PAGE_SIZE = 20;

/**
 * An impractically high number of participants indicating 'all' are to be shown.
 */
const SHOW_ALL_PAGE_SIZE = 5000;

// Gather form data.
$id       = required_param('instanceid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$page     = optional_param('page', 0, PARAM_INT); // Which page to show.
$perpage  = optional_param('perpage', DEFAULT_PAGE_SIZE, PARAM_INT); // How many per page.
$group    = optional_param('group', 0, PARAM_ALPHANUMEXT); // Group selected.
$role     = optional_param('role', null, PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);

// Determine course and context.
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
//$context = context_course::instance($courseid);
$context = context_course::instance($courseid);

// Get specific block config and context.
$block = $DB->get_record('block_instances', array('id' => $id), '*', MUST_EXIST);
$blockcontext = context_block::instance($id);

$notesallowed = !empty($CFG->enablenotes) && has_capability('moodle/notes:manage', $context);
$messagingallowed = !empty($CFG->messaging) && has_capability('moodle/site:sendmessage', $context);
$bulkoperations = has_capability('moodle/course:bulkmessaging', $context) && ($notesallowed || $messagingallowed);

// Set up page parameters.
$PAGE->set_course($course);
$PAGE->set_url(
    '/blocks/pintar_analytic/overview.php',
    array(
        'instanceid' => $id,
        'courseid'   => $courseid,
        'page'       => $page,
        'perpage'    => $perpage,
        'group'      => $group,
        'sesskey'    => sesskey(),
        'role'       => $role,
    )
);
$PAGE->set_context($context);
$title = get_string('overview', 'block_pintar_analytic');
$PAGE->set_pagelayout('report');
$PAGE->set_title($title);
# $PAGE->set_heading(get_string('pluginname','block_pintar_analytic'));
$PAGE->set_heading($title);
$PAGE->navbar->add($title);
$PAGE->set_pagelayout('report');

$cachevalue = debugging() ? -1 : (int)get_config('block_pintar_analytic', 'cachevalue');
$PAGE->requires->css('/blocks/pintar_analytic/css.php?v=' . $cachevalue);



echo $OUTPUT->header();

echo $OUTPUT->footer();




