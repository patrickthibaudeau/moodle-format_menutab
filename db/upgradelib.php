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
 * Check if a course has any labels with h2 tags.
 *
 * @param int $courseid The course ID
 * @return bool True if h2 labels exist
 */
function format_menutab_check_for_h2_labels($courseid) {
    global $DB;

    // Get all sections for this course (excluding section 0).
    $sections = $DB->get_records_select('course_sections',
        'course = ? AND section > 0 AND component IS NULL',
        [$courseid],
        'section ASC'
    );

    foreach ($sections as $section) {
        $sequence = !empty($section->sequence) ? explode(',', $section->sequence) : [];

        foreach ($sequence as $cmid) {
            $cm = $DB->get_record('course_modules', ['id' => $cmid]);
            if (!$cm) {
                continue;
            }

            $module = $DB->get_record('modules', ['id' => $cm->module]);
            if ($module && $module->name == 'label') {
                $label = $DB->get_record('label', ['id' => $cm->instance]);
                if ($label && preg_match('#<\s*?h2\b[^>]*>(.*?)</h2\b[^>]*>#s', $label->intro)) {
                    return true;
                }
            }
        }
    }

    return false;
}

/**
 * Get information about h2 labels in a course.
 *
 * @param int $courseid The course ID
 * @return array Array of label information
 */
function format_menutab_get_h2_labels_info($courseid) {
    global $DB;

    $labels_info = [];

    $sections = $DB->get_records_select('course_sections',
        'course = ? AND section > 0 AND component IS NULL',
        [$courseid],
        'section ASC'
    );

    foreach ($sections as $section) {
        $sequence = !empty($section->sequence) ? explode(',', $section->sequence) : [];

        foreach ($sequence as $cmid) {
            $cm = $DB->get_record('course_modules', ['id' => $cmid]);
            if (!$cm) {
                continue;
            }

            $module = $DB->get_record('modules', ['id' => $cm->module]);
            if ($module && $module->name == 'label') {
                $label = $DB->get_record('label', ['id' => $cm->instance]);
                if ($label && preg_match('#<\s*?h2\b[^>]*>(.*?)</h2\b[^>]*>#s', $label->intro, $matches)) {
                    $h2_text = strip_tags($matches[1]);
                    $labels_info[] = [
                        'section_name' => $section->name ? $section->name : get_string('section') . ' ' . $section->section,
                        'h2_text' => $h2_text,
                        'section_num' => $section->section
                    ];
                }
            }
        }
    }

    return $labels_info;
}

/**
 * Convert h2 labels to subsections for a single course.
 *
 * @param int $courseid The course ID
 * @return bool True on success, false on failure
 */
function format_menutab_convert_course_labels_to_subsections($courseid) {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/course/lib.php');
    require_once($CFG->dirroot . '/mod/subsection/lib.php');

    try {
        // Get subsection module id.
        $subsection_module = $DB->get_record('modules', ['name' => 'subsection']);
        if (!$subsection_module) {
            debugging('mod_subsection not found. Cannot convert.', DEBUG_DEVELOPER);
            return false;
        }

        $course = $DB->get_record('course', ['id' => $courseid], '*', MUST_EXIST);

        // Get all sections for this course (excluding section 0).
        $sections = $DB->get_records_select('course_sections',
            'course = ? AND section > 0 AND component IS NULL',
            [$courseid],
            'section ASC'
        );

        foreach ($sections as $section) {
            // Get course modules for this section.
            $sequence = !empty($section->sequence) ? explode(',', $section->sequence) : [];

            if (empty($sequence)) {
                continue;
            }

            $subsections_data = [];
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
                $subsection_cms_to_add = [];

                // Create subsection modules and their delegated sections.
                foreach ($subsections_data as $subsection_data) {
                    // 1. Create the subsection instance record.
                    $subsection_instance = new stdClass();
                    $subsection_instance->course = $courseid;
                    $subsection_instance->name = $subsection_data['name'];
                    $subsection_instance->timecreated = time();
                    $subsection_instance->timemodified = time();

                    $subsection_instance->id = $DB->insert_record('subsection', $subsection_instance);

                    // 2. Create the course module for this subsection.
                    $newcm = new stdClass();
                    $newcm->course = $courseid;
                    $newcm->module = $subsection_module->id;
                    $newcm->instance = $subsection_instance->id;
                    $newcm->section = $section->id;
                    $newcm->visible = $subsection_data['visible'];
                    $newcm->visibleoncoursepage = $subsection_data['visible'];
                    $newcm->availability = $subsection_data['availability'];
                    $newcm->added = time();

                    $newcm->id = add_course_module($newcm);

                    // Track this subsection CM to add to parent sequence later.
                    $subsection_cms_to_add[] = $newcm->id;

                    // 3. Manually create the delegated section record.
                    // We do this manually to avoid cache refresh during conversion.
                    $delegated_section = new stdClass();
                    $delegated_section->course = $courseid;
                    $delegated_section->section = $DB->get_field_sql(
                        "SELECT MAX(section) + 1 FROM {course_sections} WHERE course = ?",
                        [$courseid]
                    );
                    $delegated_section->name = $subsection_data['name'];
                    $delegated_section->summary = '';
                    $delegated_section->summaryformat = FORMAT_HTML;
                    $delegated_section->visible = $subsection_data['visible'];
                    $delegated_section->availability = $subsection_data['availability'] ?? null;
                    $delegated_section->timemodified = time();
                    $delegated_section->component = 'mod_subsection';
                    $delegated_section->itemid = $subsection_instance->id;
                    $delegated_section->sequence = '';

                    $delegated_section->id = $DB->insert_record('course_sections', $delegated_section);

                    // 4. Move activities to the delegated section.
                    if (!empty($subsection_data['modules'])) {
                        $delegated_sequence = [];
                        foreach ($subsection_data['modules'] as $cmid) {
                            $cm = $DB->get_record('course_modules', ['id' => $cmid]);
                            if ($cm) {
                                $cm->section = $delegated_section->id;
                                $DB->update_record('course_modules', $cm);
                                $delegated_sequence[] = $cmid;
                            }
                        }

                        // Update the delegated section's sequence.
                        $DB->set_field('course_sections', 'sequence', implode(',', $delegated_sequence), ['id' => $delegated_section->id]);
                    }
                }

                // 5. Delete the h2 labels.
                foreach ($labels_to_delete as $cmid) {
                    $cm = $DB->get_record('course_modules', ['id' => $cmid]);
                    if ($cm) {
                        $DB->delete_records('label', ['id' => $cm->instance]);
                        $DB->delete_records('course_modules', ['id' => $cmid]);
                    }
                }

                // 6. Update parent section's sequence.
                $parent_sequence = !empty($section->sequence) ? explode(',', $section->sequence) : [];
                $parent_sequence = array_diff($parent_sequence, $modules_to_remove_from_parent);
                $parent_sequence = array_merge($parent_sequence, $subsection_cms_to_add);
                $DB->set_field('course_sections', 'sequence', implode(',', $parent_sequence), ['id' => $section->id]);
            }
        }

        // Rebuild the course cache ONCE at the end after all changes are complete.
        rebuild_course_cache($courseid, true);

        return true;
    } catch (Exception $e) {
        debugging('Error converting course: ' . $e->getMessage(), DEBUG_DEVELOPER);
        return false;
    }
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
                $subsection_cms_to_add = []; // Track subsection CMs we create

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

                    // Track this subsection CM to add to parent sequence later
                    $subsection_cms_to_add[] = $newcm->id;

                    // 3. Manually create the delegated section record.
                    // We do this manually to avoid cache refresh during conversion.
                    $delegated_section = new stdClass();
                    $delegated_section->course = $course->id;
                    $delegated_section->section = $DB->get_field_sql(
                        "SELECT MAX(section) + 1 FROM {course_sections} WHERE course = ?",
                        [$course->id]
                    );
                    $delegated_section->name = $subsection_data['name'];
                    $delegated_section->summary = '';
                    $delegated_section->summaryformat = FORMAT_HTML;
                    $delegated_section->visible = $subsection_data['visible'];
                    $delegated_section->availability = $subsection_data['availability'] ?? null;
                    $delegated_section->timemodified = time();
                    $delegated_section->component = 'mod_subsection';
                    $delegated_section->itemid = $subsection_instance->id;
                    $delegated_section->sequence = '';

                    $delegated_section->id = $DB->insert_record('course_sections', $delegated_section);
                    mtrace("    Created delegated section ID: {$delegated_section->id}");

                    // 4. Move activities to the delegated section.
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
                        $DB->set_field('course_sections', 'sequence', implode(',', $delegated_sequence), ['id' => $delegated_section->id]);
                    }
                }

                // 5. Delete the h2 labels.
                foreach ($labels_to_delete as $cmid) {
                    $cm = $DB->get_record('course_modules', ['id' => $cmid]);
                    if ($cm) {
                        $DB->delete_records('label', ['id' => $cm->instance]);
                        $DB->delete_records('course_modules', ['id' => $cmid]);
                        mtrace("    Deleted h2 label module {$cmid}");
                    }
                }

                // 6. Update parent section's sequence:
                // - Remove moved activities and deleted labels
                // - Add the new subsection modules
                $parent_sequence = !empty($section->sequence) ? explode(',', $section->sequence) : [];
                $parent_sequence = array_diff($parent_sequence, $modules_to_remove_from_parent);
                $parent_sequence = array_merge($parent_sequence, $subsection_cms_to_add);
                $DB->set_field('course_sections', 'sequence', implode(',', $parent_sequence), ['id' => $section->id]);

                mtrace("  Updated parent section sequence");
            }
        }

        // Rebuild the course cache.
        rebuild_course_cache($course->id, true);

        mtrace("Completed course: {$course->fullname}");
    }

    mtrace("Migration complete!");
}

/**
 * Fix numsections count for all menutab format courses.
 *
 * This function recalculates the numsections value for all courses using the menutab format,
 * ensuring that subsections (delegated sections) are excluded from the count.
 * This prevents "orphaned sections" from appearing when courses have subsections.
 *
 * @return void
 */
function format_menutab_fix_numsections_count() {
    global $DB;

    mtrace("Fixing numsections count for menutab format courses...");

    // Get all courses using the menutab format.
    $courses = $DB->get_records('course', ['format' => 'menutab']);

    if (empty($courses)) {
        mtrace("No menutab format courses found.");
        return;
    }

    mtrace("Found " . count($courses) . " menutab course(s) to process.");

    foreach ($courses as $course) {
        mtrace("Processing course: {$course->fullname} (ID: {$course->id})");

        // Use direct database query to count sections (cannot use get_fast_modinfo during upgrade).
        // Count only regular sections, excluding section 0 and subsections (delegated sections).
        $sql = "SELECT COUNT(*)
                  FROM {course_sections}
                 WHERE course = :courseid
                   AND section > 0
                   AND (component IS NULL OR component = '')";

        $number_of_sections = (int)$DB->count_records_sql($sql, ['courseid' => $course->id]);
        mtrace("  Counted {$number_of_sections} regular section(s) (excluding section 0 and subsections)");

        // Get the current numsections value.
        $current_numsections = $DB->get_field('course_format_options', 'value',
            ['courseid' => $course->id, 'format' => 'menutab', 'name' => 'numsections']);

        // Cast to int for proper comparison (database values are strings).
        $current_numsections = $current_numsections !== false ? (int)$current_numsections : null;

        if ($current_numsections === null) {
            mtrace("  No numsections value found in database, inserting {$number_of_sections}");
        } else {
            mtrace("  Current numsections value in database: {$current_numsections}");
        }

        if ($current_numsections !== $number_of_sections) {
            // Update or insert the numsections value.
            $record = $DB->get_record('course_format_options',
                ['courseid' => $course->id, 'format' => 'menutab', 'name' => 'numsections']);

            if ($record) {
                $old_value = $record->value;
                $record->value = $number_of_sections;
                $DB->update_record('course_format_options', $record);
                mtrace("  ✓ Updated numsections from {$old_value} to {$number_of_sections}");
            } else {
                $record = new \stdClass();
                $record->courseid = $course->id;
                $record->format = 'menutab';
                $record->sectionid = 0;
                $record->name = 'numsections';
                $record->value = $number_of_sections;
                $DB->insert_record('course_format_options', $record);
                mtrace("  ✓ Inserted numsections with value {$number_of_sections}");
            }

            // Verify the update.
            $verify = $DB->get_field('course_format_options', 'value',
                ['courseid' => $course->id, 'format' => 'menutab', 'name' => 'numsections']);
            mtrace("  Verification: numsections is now {$verify} in database");
        } else {
            mtrace("  ✓ numsections already correct ({$number_of_sections}), no update needed");
        }
    }

    mtrace("Finished fixing numsections count!");
}

