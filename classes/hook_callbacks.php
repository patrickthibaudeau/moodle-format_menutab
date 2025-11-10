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
 * Hook callbacks for format_menutab.
 *
 * @package     format_menutab
 * @copyright   2024 UIT Innovation  <thibaud@yorku.ca>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_menutab;

defined('MOODLE_INTERNAL') || die();

/**
 * Hook callbacks for the menutab course format.
 */
class hook_callbacks {

    /**
     * Add breadcrumb navigation for subsections before the course is viewed.
     *
     * @param \core_course\hook\before_course_viewed $hook
     */
    public static function before_course_viewed(\core_course\hook\before_course_viewed $hook): void {
        global $PAGE, $DB;

        // Access the course property directly (not via get_course() method).
        $course = $hook->course;

        // Only apply to courses using the menutab format.
        if ($course->format !== 'menutab') {
            return;
        }

        // Check if we're viewing a specific section.
        $section_number = optional_param('section', 0, PARAM_INT);

        if ($section_number <= 0) {
            return;
        }

        $modinfo = get_fast_modinfo($course);
        $sectioninfo = $modinfo->get_section_info($section_number);

        // Check if this is a subsection (delegated section).
        if (empty($sectioninfo->component) || $sectioninfo->component !== 'mod_subsection') {
            return;
        }

        // This is a subsection, find its parent section.
        // The itemid contains the subsection module instance id.
        $subsectionid = $sectioninfo->itemid;

        // Get the subsection course module.
        $subsectioncm = $DB->get_record('course_modules', [
            'course' => $course->id,
            'module' => $DB->get_field('modules', 'id', ['name' => 'subsection']),
            'instance' => $subsectionid
        ]);

        if (!$subsectioncm) {
            return;
        }

        // Find the parent section by looking up which section contains this CM.
        $parentsection = $DB->get_record('course_sections', ['id' => $subsectioncm->section]);

        if (!$parentsection) {
            return;
        }

        $parentsectioninfo = $modinfo->get_section_info($parentsection->section);
        $parentsectionname = get_section_name($course, $parentsectioninfo);

        // Add breadcrumb for parent section.
        $PAGE->navbar->add(
            $parentsectionname,
            new \moodle_url('/course/view.php', ['id' => $course->id, 'section' => $parentsection->section])
        );

        // Add breadcrumb for the subsection.
        $subsectionname = get_section_name($course, $sectioninfo);
        $PAGE->navbar->add($subsectionname);
    }
}

