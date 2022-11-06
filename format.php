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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 *  Display the whole course.
 *
 * @package     format_menutab
 * @copyright   2022 UIT Innovation  <thibaud@yorku.ca>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/filelib.php');
require_once($CFG->libdir . '/completionlib.php');

// Retrieve course format option fields and add them to the $course object.
$format = course_get_format($course);
$course = $format->get_course();
$context = context_course::instance($course->id);
$isediting = $PAGE->user_is_editing();
$displaysection = optional_param('section', 0, PARAM_INT);
// Set section number
if (!empty($displaysection)) {
    $format->set_section_number($displaysection);
}

// Get format config
$config = get_config('format_menutab');

// Make sure section 0 is created.
course_create_sections_if_missing($course, 0);

// Get marker
if (($marker >= 0) && has_capability('moodle/course:setcurrentsection', $context) && confirm_sesskey()) {
    $course->marker = $marker;
    course_set_marker($course->id, $marker);
}

// Setup the format base instance.
$renderer = $PAGE->get_renderer('format_menutab');

// Display page based whether the user is editing or and course front page or within a section
if ($isediting) {
    // If user is editing, we render the page the new way.
    // TODO we will use this for non editing as well, but not yet.
    $outputclass = $format->get_output_classname('content');
    $widget = new $outputclass($format);

    echo $renderer->render($widget);
} else {
    if ($displaysection == 0) {
        $templateable = new \format_menutab\output\course_output($course, false, null, $renderer);
        $data = $templateable->export_for_template($renderer);
        echo $renderer->render_from_template('format_menutab/multi_section_page', $data);
    } else {
        $templateable = new \format_menutab\output\course_output($course, false, $displaysection, $renderer);
        $data = $templateable->export_for_template($renderer);
        echo $renderer->render_from_template('format_menutab/single_section_page', $data);
    }
}

// Output course content.
$outputclass = $format->get_output_classname('content');
$widget = new $outputclass($format);
echo $renderer->render($widget);

