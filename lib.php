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
 *  Format base class.
 *
 * @package     format_menutab
 * @copyright   2022 UIT Innovation  <thibaud@yorku.ca>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/course/format/lib.php');

class format_menutab extends core_courseformat\base
{

    /**
     * Returns true if this course format uses sections.
     *
     * @return bool
     */
    public function uses_sections()
    {
        return true;
    }

    public function uses_indentation(): bool
    {
        return false;
    }

    public function uses_course_index()
    {
        return true;
    }

    /**
     * Returns the information about the ajax support in the given source format.
     *
     * The returned object's property (boolean)capable indicates that
     * the course format supports Moodle course ajax features.
     *
     * @return stdClass
     */
    public function supports_ajax()
    {
        $ajaxsupport = new stdClass();
        $ajaxsupport->capable = true;
        return $ajaxsupport;
    }

    public function supports_components()
    {
        return true;
    }

    /**
     * Whether this format allows to delete sections.
     *
     * Do not call this function directly, instead use {@link course_can_delete_section()}
     *
     * @param int|stdClass|section_info $section
     * @return bool
     */
    public function can_delete_section($section)
    {
        return true;
    }

    /**
     * Indicates whether the course format supports the creation of a news forum.
     *
     * @return bool
     */
    public function supports_news()
    {
        return true;
    }

    /**
     * Returns the display name of the given section that the course prefers.
     *
     * This method is required for inplace section name editor.
     *
     * @param int|stdClass $section Section object from database or just field section.section
     * @return string Display name that the course format prefers, e.g. "Topic 2"
     */
    public function get_section_name($section)
    {
        $section = $this->get_section($section);
        if ((string)$section->name !== '') {
            return format_string(
                $section->name,
                true,
                ['context' => context_course::instance($this->courseid)]
            );
        } else {
            return $this->get_default_section_name($section);
        }
    }

    /**
     * Returns the default section name for the topics course format.
     *
     * If the section number is 0, it will use the string with key = section0name from the course format's lang file.
     * If the section number is not 0, the base implementation of course_format::get_default_section_name which uses
     * the string with the key = 'sectionname' from the course format's lang file + the section number will be used.
     *
     * @param stdClass $section Section object from database or just field course_sections section
     * @return string The default value for the section name.
     */
    public function get_default_section_name($section)
    {
        if ($section->section == 0) {
            // Return the general section.
            return get_string('section0name', 'format_menutab');
        } else {
            // Use course_format::get_default_section_name implementation which
            // will display the section name in "Topic n" format.
            return parent::get_default_section_name($section);
        }
    }

    /**
     * The URL to use for the specified course (with section).
     *
     * @param int|stdClass $section Section object from database or just field course_sections.section
     *     if omitted the course view page is returned
     * @param array $options options for view URL. At the moment core uses:
     *     'navigation' (bool) if true and section has no separate page, the function returns null
     *     'sr' (int) used by multipage formats to specify to which section to return
     * @return null|moodle_url
     */
    public function get_view_url($section, $options = [])
    {
        global $CFG;
        $course = $this->get_course();
        $url = new moodle_url('/course/view.php', ['id' => $course->id]);

        $sr = null;
        if (array_key_exists('sr', $options)) {
            $sr = $options['sr'];
        }
        if (is_object($section)) {
            $sectionno = $section->section;
        } else {
            $sectionno = $section;
        }
        if ($sectionno !== null) {
            $url->param('section', $sectionno);
        }
        return $url;
    }

    /**
     * Definitions of the additional options that this course format uses for course
     *
     * Menu/Tab format uses the following options:
     * - coursedisplay
     * - numsections
     * - hiddensections
     *
     * @param bool $foreditform
     * @return array of options
     */
    public function course_format_options($foreditform = false)
    {
        global $COURSE;
        static $courseformatoptions = false;
        $context = context_course::instance($COURSE->id);
        if ($courseformatoptions === false) {
            $courseconfig = get_config('moodlecourse');
            $courseformatoptions = array(
                'numsections' => array(
                    'default' => $courseconfig->numsections,
                    'type' => PARAM_INT,
                ),
                'hiddensections' => array(
                    'default' => $courseconfig->hiddensections,
                    'type' => PARAM_INT,
                ),
                'coursedisplay' => array(
                    'default' => $courseconfig->coursedisplay,
                    'type' => PARAM_INT,
                ),
                'numcolumns' => array(
                    'default' => 3,
                    'type' => PARAM_INT,
                ),
                'collapsed' => array(
                    'default' => 1,
                    'type' => PARAM_INT,
                ),
                'section_zero_background_color' => array(
                    'default' => '#1b4c88',
                    'type' => PARAM_TEXT,
                ),
                'show_summary' => array(
                    'default' => 0,
                    'type' => PARAM_INT,
                ),
                'print_progress' => array(
                    'default' => 0,
                    'type' => PARAM_INT,
                ),
                'print_section_number' => array(
                    'default' => 0,
                    'type' => PARAM_INT,
                ),
                'tab_background_color' => array(
                    'default' => '#1b4c88',
                    'type' => PARAM_TEXT,
                ),
                'tab_text_color' => array(
                    'default' => '#ffffff',
                    'type' => PARAM_TEXT,
                ),
            );
        }
        if ($foreditform && !isset($courseformatoptions['coursedisplay']['label'])) {
            $courseconfig = get_config('moodlecourse');
            $max = (int)$courseconfig->maxsections;
            $sectionmenu = [];
            for ($i = 0; $i <= $max; $i++) {
                $sectionmenu[$i] = "$i";
            }
            $courseformatoptionsedit = array(
                'numsections' => array(
                    'label' => new \lang_string('numberweeks'),
                    'element_type' => 'select',
                    'element_attributes' => array($sectionmenu),
                ),
                'hiddensections' => array(
                    'label' => new \lang_string('hiddensections'),
                    'help' => 'hiddensections',
                    'help_component' => 'moodle',
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            0 => new lang_string('hiddensectionscollapsed'),
                            1 => new lang_string('hiddensectionsinvisible')
                        )
                    ),
                ),
                'coursedisplay' => array(
                    'label' => new lang_string('coursedisplay'),
                    'element_type' => 'hidden',
                    'element_attributes' => [[COURSE_DISPLAY_SINGLEPAGE => new \lang_string('coursedisplay_single')]]
                ),
                'numcolumns' => array(
                    'label' => new lang_string('numcolumns', 'format_menutab'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            3 => '3',
                            4 => '4',
                        )
                    ),
                    'help' => 'numcolumns',
                    'help_component' => 'format_menutab',
                ),
                'collapsed' => array(
                    'label' => new lang_string('collapsed', 'format_menutab'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            1 => get_string('yes'),
                            0 => get_string('no'),
                        )
                    ),
                    'help' => 'collapsed',
                    'help_component' => 'format_menutab',
                ),
                'section_zero_background_color' => array(
                    'label' => new lang_string('section_zero_background_color', 'format_menutab'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        $this->get_default_tab_background_colors()
                    ),
                    'help' => 'section_zero_background_color',
                    'help_component' => 'format_menutab',
                ),
                'show_summary' => array(
                    'label' => new lang_string('show_summary_single_section', 'format_menutab'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            1 => get_string('yes'),
                            0 => get_string('no'),
                        )
                    ),
                    'help' => 'show_summary_single_section',
                    'help_component' => 'format_menutab',
                ),
                'print_progress' => array(
                    'label' => new lang_string('print_progress', 'format_menutab'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            1 => get_string('yes'),
                            0 => get_string('no'),
                        )
                    ),
                    'help' => 'print_progress',
                    'help_component' => 'format_menutab',
                ),
                'print_section_number' => array(
                    'label' => new lang_string('print_section_number', 'format_menutab'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            1 => get_string('yes'),
                            0 => get_string('no'),
                        )
                    ),
                    'help' => 'print_section_number',
                    'help_component' => 'format_menutab',
                ),
                'tab_background_color' => array(
                    'label' => new lang_string('tab_background_color', 'format_menutab'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        $this->get_default_tab_background_colors()
                    ),
                    'help' => 'tab_background_color',
                    'help_component' => 'format_menutab',
                ),
                'tab_text_color' => array(
                    'label' => new lang_string('tab_text_color', 'format_menutab'),
                    'element_type' => 'select',
                    'element_attributes' => array(
                        array(
                            '#ffffff' => 'White',
                            '#000000' => 'Black'
                        )
                    ),
                    'help' => 'tab_text_color',
                    'help_component' => 'format_menutab',
                ),
            );
            $courseformatoptions = array_merge_recursive($courseformatoptions, $courseformatoptionsedit);
        }
        return $courseformatoptions;
    }

    /**
     * Returns an array of all default colors based on format settings
     * @return array
     */
    private function get_default_tab_background_colors() {
        global $CFG;
        $default_colors = explode("\n", $CFG->menutab_tab_background_colors);
        $colors = [];
        for ($i = 0; $i < count($default_colors); $i++) {
            $color = explode("|", $default_colors[$i]);
            $colors[$color[0]] = $color[1];
        }

        return $colors;
    }
    /**
     * Adds format options elements to the course/section edit form.
     *
     * This function is called from {@see course_edit_form::definition_after_data()}.
     *
     * @param MoodleQuickForm $mform form the elements are added to.
     * @param bool $forsection 'true' if this is a section edit form, 'false' if this is course edit form.
     * @return array array of references to the added form elements.
     * @throws HTML_QuickForm_Error
     * @throws coding_exception
     * @throws dml_exception
     */
    public function create_edit_form_elements(&$mform, $forsection = false) {
        global $COURSE, $PAGE, $DB, $USER;
        $elements = parent::create_edit_form_elements($mform, $forsection);

        // Call the JS edit_form_helper.js, which in turn will call edit_icon_picker.js.
        if ($forsection) {
            $sectionid = optional_param('id', 0, PARAM_INT);
            $section = $DB->get_field('course_sections', 'section', array('id' => $sectionid));
        } else {
            // We are on the course setting page so can ignore section.
            $section = 0;
            $sectionid = 0;
        }
        $jsparams = array(
            'pageType' => $PAGE->pagetype,
        );
        $PAGE->requires->js_call_amd('format_menutab/edit_form_helper', 'init', $jsparams);

        if (!$forsection && (empty($COURSE->id) || $COURSE->id == SITEID)) {
            // Add "numsections" to create course form - will force the course pre-populated with empty sections.
            // The "Number of sections" option is no longer available when editing course.
            // Instead teachers should delete and add sections when needed.

            $courseconfig = get_config('moodlecourse');
            $max = (int)$courseconfig->maxsections;
            $element = $mform->addElement('select', 'numsections', get_string('numberweeks'), range(0, $max ?: 52));
            $mform->setType('numsections', PARAM_INT);
            if (is_null($mform->getElementValue('numsections'))) {
                $mform->setDefault('numsections', $courseconfig->numsections);
            }
            array_unshift($elements, $element);
        }
        return $elements;
    }
}

/**
 * Implements callback inplace_editable() allowing to edit values in-place.
 *
 * This method is required for inplace section name editor.
 *
 * @param string $itemtype
 * @param int $itemid
 * @param mixed $newvalue
 * @return inplace_editable
 */
function format_menutab_inplace_editable($itemtype, $itemid, $newvalue)
{
    global $DB, $CFG;
    require_once($CFG->dirroot . '/course/lib.php');
    if ($itemtype === 'sectionname' || $itemtype === 'sectionnamenl') {
        $section = $DB->get_record_sql(
            'SELECT s.* FROM {course_sections} s JOIN {course} c ON s.course = c.id WHERE s.id = ? AND c.format = ?',
            [$itemid, 'menutab'],
            MUST_EXIST
        );
        $format = core_courseformat\base::instance($section->course);
        return $format->inplace_editable_update_section_name($section, $itemtype, $newvalue);
    }
}

