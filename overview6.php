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


require_once('../../config.php');




echo $OUTPUT->header();

$PAGE->requires->jquery();
$PAGE->requires->js(new moodle_url('PATH test.js'));
echo '
<script src="./s/highcharts.js"></script>


';//GANTI PATH



echo '



<div class="container1" id="container1" style="width:100%; height:400px;">xxgx</div>
';



echo $OUTPUT->footer();


?>


        
   




