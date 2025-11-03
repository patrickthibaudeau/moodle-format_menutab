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
 * Plugin upgrade helper functions are defined here.
 *
 * @package     format_menutab
 * @category    upgrade
 * @copyright   2022 UIT Innovation  <thibaud@yorku.ca>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Helper function used by the upgrade.php file.
 */
function format_menutab_helper_function() {
    global $DB;

    // Please note: you can only use raw low level database access here.
    // Avoid Moodle API calls in upgrade steps.
    //
    // For more information please read {@link https://docs.moodle.org/dev/Upgrade_API}.
}

/**
 * Migrate h2 labels to subsections for menutab format courses.
 *
 * This function converts the legacy label-with-h2-tag approach to tabs
 * into the modern Moodle 5 subsection approach using mod_subsection.
 *
 * @return void
 */
function format_menutab_migrate_labels_to_subsections() {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/course/lib.php');
    require_once($CFG->dirroot . '/mod/subsection/lib.php');

    // Get subsection module id.
    $subsection_module = $DB->get_record('modules', ['name' => 'subsection']);
    if (!$subsection_module) {
        mtrace("ERROR: mod_subsection not found. Cannot migrate.");
        return;
    }

    // Get all courses using the menutab format.
    $courses = $DB->get_records('course', ['format' => 'menutab']);

    foreach ($courses as $course) {
        mtrace("Processing course: {$course->fullname} (ID: {$course->id})");

        // Get all sections for this course (excluding section 0).
        $sections = $DB->get_records_select('course_sections',
            'course = ? AND section > 0 AND component IS NULL',
            [$course->id],
            'section ASC'
        );

        foreach ($sections as $section) {
            // Get course modules for this section.
            $sequence = !empty($section->sequence) ? explode(',', $section->sequence) : [];

            if (empty($sequence)) {
                continue;
            }

            $subsections_data = []; // Array to hold subsection data.
            $labels_to_delete = [];
            $modules_to_remove_from_parent = [];

            // First pass: identify h2 labels and group activities.
            $current_subsection = null;
            foreach ($sequence as $cmid) {
                $cm = $DB->get_record('course_modules', ['id' => $cmid]);
                if (!$cm) {
                    continue;
                }

                $module = $DB->get_record('modules', ['id' => $cm->module]);

                // Check if this is a label module with h2.
                if ($module && $module->name == 'label') {
                    $label = $DB->get_record('label', ['id' => $cm->instance]);

                    if ($label && preg_match('#<\s*?h2\b[^>]*>(.*?)</h2\b[^>]*>#s', $label->intro, $matches)) {
                        $h2_content = strip_tags($matches[1]);

                        mtrace("  Found h2 label in section {$section->section}: {$h2_content}");

                        // Start a new subsection.
                        $current_subsection = [
                            'name' => $h2_content,
                            'visible' => $cm->visible,
                            'availability' => $cm->availability,
                            'modules' => []
                        ];
                        $subsections_data[] = $current_subsection;

                        $labels_to_delete[] = $cmid;
                        $modules_to_remove_from_parent[] = $cmid;
                    } else {
                        // Regular label, keep in current context.
                        if ($current_subsection !== null) {
                            $subsections_data[count($subsections_data) - 1]['modules'][] = $cmid;
                            $modules_to_remove_from_parent[] = $cmid;
                        }
                    }
                } else {
                    // Regular activity.
                    if ($current_subsection !== null) {
                        $subsections_data[count($subsections_data) - 1]['modules'][] = $cmid;
                        $modules_to_remove_from_parent[] = $cmid;
                    }
                }
            }

            // Only process if we found h2 labels.
            if (!empty($subsections_data)) {
                // Create subsection modules and their delegated sections.
                foreach ($subsections_data as $subsection_data) {
                    // 1. Create the subsection instance record.
                    $subsection_instance = new stdClass();
                    $subsection_instance->course = $course->id;
                    $subsection_instance->name = $subsection_data['name'];
                    $subsection_instance->timecreated = time();
                    $subsection_instance->timemodified = time();

                    $subsection_instance->id = $DB->insert_record('subsection', $subsection_instance);
                    mtrace("    Created subsection instance ID: {$subsection_instance->id}");

                    // 2. Create the course module for this subsection.
                    $newcm = new stdClass();
                    $newcm->course = $course->id;
                    $newcm->module = $subsection_module->id;
                    $newcm->instance = $subsection_instance->id;
                    $newcm->section = $section->id;
                    $newcm->visible = $subsection_data['visible'];
                    $newcm->visibleoncoursepage = $subsection_data['visible'];
                    $newcm->availability = $subsection_data['availability'];
                    $newcm->added = time();

                    $newcm->id = add_course_module($newcm);
                    mtrace("    Created course module ID: {$newcm->id}");

                    // 3. Create the delegated section via subsection_add_instance logic.
                    // Get the module instance that was created.
                    $moduleinstance = $DB->get_record('subsection', ['id' => $subsection_instance->id]);
                    $moduleinstance->visible = $subsection_data['visible'];
                    $moduleinstance->availability = $subsection_data['availability'];

                    // Create delegated section using core API.
                    $cmavailability = $subsection_data['availability'] ?? null;
                    if (empty($cmavailability)) {
                        $cmavailability = null;
                    }

                    $delegated_section = \core_courseformat\formatactions::section($course->id)->create_delegated(
                        'mod_subsection',
                        $subsection_instance->id,
                        (object)[
                            'name' => $subsection_data['name'],
                            'visible' => $subsection_data['visible'],
                            'availability' => $cmavailability,
                        ]
                    );

                    mtrace("    Created delegated section ID: {$delegated_section->id}");

                    // 4. Add the subsection CM to parent section's sequence.
                    $parent_sequence = !empty($section->sequence) ? explode(',', $section->sequence) : [];
                    $parent_sequence[] = $newcm->id;
                    $section->sequence = implode(',', $parent_sequence);

                    // 5. Move activities to the delegated section.
                    if (!empty($subsection_data['modules'])) {
                        $delegated_sequence = [];
                        foreach ($subsection_data['modules'] as $cmid) {
                            $cm = $DB->get_record('course_modules', ['id' => $cmid]);
                            if ($cm) {
                                // Update the course module to point to the delegated section.
                                $cm->section = $delegated_section->id;
                                $DB->update_record('course_modules', $cm);
                                $delegated_sequence[] = $cmid;

                                mtrace("      Moved module {$cmid} to delegated section");
                            }
                        }

                        // Update delegated section's sequence.
                        $delegated_section->sequence = implode(',', $delegated_sequence);
                        $DB->update_record('course_sections', $delegated_section);
                    }
                }

                // Delete the h2 labels.
                foreach ($labels_to_delete as $cmid) {
                    $cm = $DB->get_record('course_modules', ['id' => $cmid]);
                    if ($cm) {
                        $DB->delete_records('label', ['id' => $cm->instance]);
                        $DB->delete_records('course_modules', ['id' => $cmid]);
                        mtrace("    Deleted h2 label module {$cmid}");
                    }
                }

                // Update parent section's sequence (remove moved activities, keep subsection modules).
                $updated_sequence = array_diff(explode(',', $section->sequence), $modules_to_remove_from_parent);
                $section->sequence = implode(',', $updated_sequence);
                $DB->update_record('course_sections', $section);

                mtrace("  Updated parent section sequence");
            }
        }

        // Rebuild the course cache.
        rebuild_course_cache($course->id, true);

        mtrace("Completed course: {$course->fullname}");
    }

    mtrace("Migration complete!");
}

