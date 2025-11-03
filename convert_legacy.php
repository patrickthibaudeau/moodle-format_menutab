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
 * Convert legacy h2 labels to subsections for a single course.
 *
 * @package     format_menutab
 * @copyright   2025 UIT Innovation  <thibaud@yorku.ca>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once(__DIR__ . '/../../../config.php');

global $CFG, $PAGE, $OUTPUT, $DB;

require_once($CFG->dirroot . '/course/lib.php');


$courseid = required_param('courseid', PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_INT);

$course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);
$context = context_course::instance($courseid);

require_login($course);
require_capability('moodle/course:update', $context);

// Check course format.
if ($course->format !== 'menutab') {
    throw new moodle_exception('invalidcourseformat', 'format_menutab');
}

$PAGE->set_url('/course/format/menutab/convert_legacy.php', ['courseid' => $courseid]);
$PAGE->set_context($context);
$PAGE->set_pagelayout('course');
$PAGE->set_title(get_string('convert_legacy_title', 'format_menutab'));
$PAGE->set_heading($course->fullname);

// Check if there are any h2 labels to convert.
require_once($CFG->dirroot . '/course/format/menutab/db/upgradelib.php');
$has_h2_labels = format_menutab_check_for_h2_labels($courseid);

if (!$has_h2_labels) {
    redirect(
        new moodle_url('/course/view.php', ['id' => $courseid]),
        get_string('no_legacy_labels', 'format_menutab'),
        null,
        \core\output\notification::NOTIFY_INFO
    );
}

if ($confirm && confirm_sesskey()) {
    // Perform the conversion.
    require_once($CFG->dirroot . '/course/format/menutab/db/upgradelib.php');

    $success = format_menutab_convert_course_labels_to_subsections($courseid);

    if ($success) {
        redirect(
            new moodle_url('/course/view.php', ['id' => $courseid]),
            get_string('conversion_success', 'format_menutab'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        redirect(
            new moodle_url('/course/view.php', ['id' => $courseid]),
            get_string('conversion_error', 'format_menutab'),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }
}

// Show confirmation page.
echo $OUTPUT->header();

// Prepare data for template.
$h2_labels = format_menutab_get_h2_labels_info($courseid);

$templatedata = [
    'title' => get_string('convert_legacy_title', 'format_menutab'),
    'warning_title' => get_string('convert_legacy_warning_title', 'format_menutab'),
    'warning_message' => get_string('convert_legacy_warning', 'format_menutab'),
    'what_will_happen' => get_string('what_will_happen', 'format_menutab'),
    'conversion_steps' => [
        ['step' => get_string('conversion_step1', 'format_menutab')],
        ['step' => get_string('conversion_step2', 'format_menutab')],
        ['step' => get_string('conversion_step3', 'format_menutab')],
        ['step' => get_string('conversion_step4', 'format_menutab')],
        ['step' => get_string('conversion_step5', 'format_menutab')],
    ],
    'has_labels' => !empty($h2_labels),
    'labels_to_convert_heading' => get_string('labels_to_convert', 'format_menutab'),
    'h2_labels' => $h2_labels,
    'confirm_url' => (new moodle_url('/course/format/menutab/convert_legacy.php'))->out(false),
    'cancel_url' => (new moodle_url('/course/view.php', ['id' => $courseid]))->out(false),
    'confirm_button_text' => get_string('convert_confirm', 'format_menutab'),
    'cancel_button_text' => get_string('cancel'),
    'sesskey' => sesskey(),
    'courseid' => $courseid,
];

echo $OUTPUT->render_from_template('format_menutab/convert_legacy', $templatedata);

echo $OUTPUT->footer();

