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
$context = context_course::instance($courseid);
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/blocks/pintar_analytic/overview5.php'));
$PAGE->set_pagelayout('course');

#   var_dump($SITE);
#   die();

$PAGE->set_title($SITE->fullname);
# $string['pluginname']='Greetings';
# $PAGE->set_heading(get_string('pluginname','block_pintar_analytic'));
# $PAGE->requires->js('/blocks/pintar_analytic/amd/src/highchart.js');
// $moodle_url = new moodle_url('amd/src/ahlan.js');
// var_dump($moodle_url);exit;
$PAGE->requires->js(new moodle_url('/test.js'));

$PAGE->set_heading('Tingkat Penyelesaian');
echo $OUTPUT->header();
echo '
<script src="highchart.js"></script>
<div id="container1" style="width:100%; height:400px;">xxx</div>
';


echo '<script>';
echo '
document.addEventListener("DOMContentLoaded", function () {
        const chart = Highcharts.chart("container1", {
            chart: {
                type: "bar"
            },
            title: {
                text: "Fruit Consumption"
            },
            xAxis: {
                categories: ["Apples", "Bananas", "Oranges"]
            },
            yAxis: {
                title: {
                    text: "Fruit eaten"
                }
            },
            series: [{
                name: "Jane",
                data: [1, 0, 4]
            }, {
                name: "John",
                data: [5, 7, 3]
            }]
        });
    });';
echo '</script>';



