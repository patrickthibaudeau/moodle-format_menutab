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

namespace format_menutab\task;

/**
 * Ad hoc task to migrate labels to subsections.
 *
 * @package   format_menutab
 * @author    UIT Innovation <thibaud@yorku.ca>
 * @author    Guillermo Gomez Arias <guillermogomez@catalyst-ca.net>
 * @copyright 2022 UIT Innovation <thibaud@yorku.ca> and Catalyst IT Canada, 2026
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class migrate_labels_to_subsections_adhoc extends \core\task\adhoc_task {
    /**
     * Run the task.
     */
    public function execute() {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->dirroot . '/mod/subsection/lib.php');

        $data = $this->get_custom_data();

        // Build only what the old code expects to exist.
        $course = (object)[
            'id' => (int)$data->courseid,
            'fullname' => $data->fullname,
        ];
        $subsectionmodule = (object)[
            'id' => (int)$data->subsectionmoduleid,
        ];

        mtrace("Processing course: {$course->fullname} (ID: {$course->id})");

        // Determine next available section number once per course.
        $maxsection = $DB->get_field_sql(
            "SELECT MAX(section) FROM {course_sections} WHERE course = ?",
            [$course->id]
        );

        $nextsection = $maxsection + 1;

        // Get all sections for this course (excluding section 0).
        $sections = $DB->get_records_select(
            'course_sections',
            'course = ? AND section > 0 AND component IS NULL',
            [$course->id],
            'section ASC',
        );

        foreach ($sections as $section) {
            // Get course modules for this section.
            $sequence = !empty($section->sequence) ? explode(',', $section->sequence) : [];

            if (empty($sequence)) {
                continue;
            }

            $subsectionsdata = []; // Array to hold subsection data.
            $labelstodelete = [];
            $modulestoremovefromparent = [];

            // First pass: identify h2 labels and group activities.
            $currentsubsection = null;
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
                        $h2content = strip_tags($matches[1]);

                        mtrace("  Found h2 label in section {$section->section}: {$h2content}");

                        // Start a new subsection.
                        $currentsubsection = [
                            'name' => $h2content,
                            'visible' => $cm->visible,
                            'availability' => $cm->availability,
                            'modules' => [],
                        ];
                        $subsectionsdata[] = $currentsubsection;

                        $labelstodelete[] = $cmid;
                        $modulestoremovefromparent[] = $cmid;
                    } else {
                        // Regular label, keep in current context.
                        if ($currentsubsection !== null) {
                            $subsectionsdata[count($subsectionsdata) - 1]['modules'][] = $cmid;
                            $modulestoremovefromparent[] = $cmid;
                        }
                    }
                } else {
                    // Regular activity.
                    if ($currentsubsection !== null) {
                        $subsectionsdata[count($subsectionsdata) - 1]['modules'][] = $cmid;
                        $modulestoremovefromparent[] = $cmid;
                    }
                }
            }

            // Only process if we found h2 labels.
            if (!empty($subsectionsdata)) {
                $subsectioncmstoadd = []; // Track subsection CMs we create.

                // Create subsection modules and their delegated sections.
                foreach ($subsectionsdata as $subsectiondata) {
                    // 1. Create the subsection instance record.
                    $subsectioninstance = new \stdClass();
                    $subsectioninstance->course = $course->id;
                    $subsectioninstance->name = $subsectiondata['name'];
                    $subsectioninstance->timecreated = time();
                    $subsectioninstance->timemodified = time();

                    $subsectioninstance->id = $DB->insert_record('subsection', $subsectioninstance);
                    mtrace("    Created subsection instance ID: {$subsectioninstance->id}");

                    // 2. Create the course module for this subsection.
                    $newcm = new \stdClass();
                    $newcm->course = $course->id;
                    $newcm->module = $subsectionmodule->id;
                    $newcm->instance = $subsectioninstance->id;
                    $newcm->section = $section->id;
                    $newcm->visible = $subsectiondata['visible'];
                    $newcm->visibleoncoursepage = $subsectiondata['visible'];
                    $newcm->availability = $subsectiondata['availability'];
                    $newcm->added = time();

                    $newcm->id = add_course_module($newcm);
                    mtrace("    Created course module ID: {$newcm->id}");

                    // Track this subsection CM to add to parent sequence later.
                    $subsectioncmstoadd[] = $newcm->id;

                    // 3. Manually create the delegated section record.
                    // We do this manually to avoid cache refresh during conversion.
                    $delegatedsection = new \stdClass();
                    $delegatedsection->course = $course->id;
                    $delegatedsection->section = $nextsection++;
                    $delegatedsection->name = $subsectiondata['name'];
                    $delegatedsection->summary = '';
                    $delegatedsection->summaryformat = FORMAT_HTML;
                    $delegatedsection->visible = $subsectiondata['visible'];
                    $delegatedsection->availability = $subsectiondata['availability'] ?? null;
                    $delegatedsection->timemodified = time();
                    $delegatedsection->component = 'mod_subsection';
                    $delegatedsection->itemid = $subsectioninstance->id;
                    $delegatedsection->sequence = '';

                    $delegatedsection->id = $DB->insert_record('course_sections', $delegatedsection);
                    mtrace("    Created delegated section ID: {$delegatedsection->id}");

                    // 4. Move activities to the delegated section.
                    if (!empty($subsectiondata['modules'])) {
                        $delegatedsequence = [];
                        foreach ($subsectiondata['modules'] as $cmid) {
                            $cm = $DB->get_record('course_modules', ['id' => $cmid]);
                            if ($cm) {
                                // Update the course module to point to the delegated section.
                                $cm->section = $delegatedsection->id;
                                $DB->update_record('course_modules', $cm);
                                $delegatedsequence[] = $cmid;

                                mtrace("      Moved module {$cmid} to delegated section");
                            }
                        }

                        // Update delegated section's sequence.
                        $DB->set_field(
                            'course_sections',
                            'sequence',
                            implode(',', $delegatedsequence),
                            ['id' => $delegatedsection->id]
                        );
                    }
                }

                // 5. Delete the h2 labels.
                foreach ($labelstodelete as $cmid) {
                    // Get the course module object.
                    if ($cm = get_coursemodule_from_id('label', $cmid)) {
                        // Use course_delete_module to ensure all related data is cleaned up.
                        course_delete_module($cmid);
                        mtrace("    Deleted h2 label module {$cmid}");
                    }
                }

                // 6. Update parent section's sequence:
                // - Remove moved activities and deleted labels
                // - Add the new subsection modules
                $parentsequence = !empty($section->sequence) ? explode(',', $section->sequence) : [];
                $parentsequence = array_diff($parentsequence, $modulestoremovefromparent);
                $parentsequence = array_merge($parentsequence, $subsectioncmstoadd);
                $DB->set_field('course_sections', 'sequence', implode(',', $parentsequence), ['id' => $section->id]);

                mtrace("  Updated parent section sequence");
            }
        }

        // Rebuild the course cache.
        rebuild_course_cache($course->id, true);

        mtrace("Completed course: {$course->fullname}");
    }
}
