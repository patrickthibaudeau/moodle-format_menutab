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
 *  Format base class.
 *
 * @package     format_menutab
 * @copyright   2022 UIT Innovation  <thibaud@yorku.ca>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace format_menutab\output;

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot . '/course/format/lib.php');
require_once("$CFG->libdir/resourcelib.php");  // To import RESOURCELIB_DISPLAY_POPUP.

use format_menutab\output\courseformat\content\cm as cm;
use format_menutab\output\courseformat\content\section;

/**
 * Tiles course format, main course output class to prepare data for mustache templates
 * @copyright 2018 David Watson
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_output implements \renderable, \templatable
{

    /**
     * Course object for this class
     * @var \stdClass
     */
    private $course;
    /**
     * Whether this class is called from AJAX
     * @var bool
     */
    private $fromajax;
    /**
     * The section number of the section we want to display
     * @var int
     */
    private $sectionnum;
    /**
     * The course renderer object
     * @var \renderer_base
     */
    private $courserenderer;
    /**
     * Array of display names to be used at the top of sub tiles depending
     * on resource type of the module.
     * e.g. 'mod/lti' => 'External Tool' 'mod/resource','xls' = "Spreadsheet'
     * @var array
     */
    private $resourcedisplaynames;

    /**
     * Names of the modules for which modal windows should be used e.g. 'page'
     * @var array of resources and modules
     */
    private $usemodalsforcoursemodules;

    /**
     * User's device type e.g. DEVICE_TYPE_MOBILE ('mobile')
     * @var string
     */
    private $devicetype;

    /**
     * The course format.
     * @var
     */
    private $format;

    /**
     * @var \course_modinfo|null
     */
    private $modinfo;

    /**
     * @var bool
     */
    private $isediting;

    /**
     * @var bool
     */
    private $canviewhidden;

    /**
     * @var \context_course
     */
    private $coursecontext;

    /**
     * @var \completion_info
     */
    private $completioninfo;

    /**
     * @var bool
     */
    private $completionenabled;

    /**
     * @var mixed
     */
    public $courseformatoptions;

    /**
     * Are we showing activity completion conditions (Moodle 3.11+).
     * @var bool
     */
    private $showcompletionconditions;

    /**
     * course_output constructor.
     * @param \stdClass $course the course object.
     * @param bool $fromajax Whether we are rendering for AJAX request.
     * @param int $sectionnum the id of the current section
     * @param \renderer_base|null $courserenderer
     */
    public function __construct($course, $fromajax = false, $sectionnum = null, \renderer_base $courserenderer = null)
    {
        $this->course = $course;
        $this->fromajax = $fromajax;
        $this->sectionnum = $sectionnum;
        if (!$fromajax) {
            $this->courserenderer = $courserenderer;
        }
        $this->devicetype = \core_useragent::get_device_type();
        $this->format = course_get_format($course);
        $this->modinfo = $this->format->get_modinfo();

        // TODO this class is no longer used if the user is editing.  To be removed.
        $this->isediting = false;
        $this->coursecontext = \context_course::instance($this->course->id);
        $this->canviewhidden = has_capability('moodle/course:viewhiddensections', $this->coursecontext);
        if ($this->course->enablecompletion && !isguestuser()) {
            $this->completioninfo = new \completion_info($this->course);
        }
        $this->completionenabled = $course->enablecompletion && !isguestuser();
        $this->courseformatoptions = $this->get_course_format_options($this->fromajax);
        $this->showcompletionconditions = isset($course->showcompletionconditions) && $course->showcompletionconditions;
    }

    /**
     * Export the course data for the mustache template.
     * @param \renderer_base $output
     * @return array|\stdClass
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function export_for_template(\renderer_base $output)
    {
        if (!$this->courserenderer) {
            $this->courserenderer = $output;
        }
        $data = $this->get_basic_data();
        // We have assembled the "common data" needed for both single and multiple section pages.
        // Now we can go off and get the specific data for the single or course home page as required.
        if ($this->sectionnum) {
            // We are outputting a single section page.s
            return $this->append_single_section_page_data($output, $data);
        } else {
            // We are outputting multi section page.
            // Add section Zero. We only use section zero on the home page.
            $data = $this->append_section_zero_data($data, $output);
            return $this->append_home_page_data($data);
        }
    }

    /**
     * Get the basic data required to render (required whatever we are doing).
     * @return array data
     * @throws \coding_exception
     * @throws \dml_exception
     */
    private function get_basic_data()
    {
        global $SESSION;

        $print_section_number = false;

        if (get_config('format_menutab', 'print_section_number')) {
            $print_section_number = true;
        }

        $data = [];
        $data['canedit'] = has_capability('moodle/course:update', $this->coursecontext);
        $data['canviewhidden'] = $this->canviewhidden;
        $data['courseid'] = $this->course->id;
        $data['completionenabled'] = $this->completionenabled;
        $data['from_ajax'] = $this->fromajax;
        $data['ismobile'] = $this->devicetype == \core_useragent::DEVICETYPE_MOBILE;
        $data['editing'] = $this->isediting;
        $data['sesskey'] = sesskey();
        $data['print_section_number'] = $print_section_number;
        $data['course_image'] = $this->get_course_image();

        foreach ($this->courseformatoptions as $k => $v) {
            $data[$k] = $v;
        }
        // RTL support for nav arrows direction (Arabic/ Hebrew).
        $data['is-rtl'] = right_to_left();
        return $data;
    }

    /**
     * Get teh course image
     * @return string
     * @throws \coding_exception
     */
    private function get_course_image()
    {
        global $COURSE, $CFG;
        $url = '';
        require_once($CFG->libdir . '/filelib.php');

        $context = \context_course::instance($COURSE->id);
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'course', 'overviewfiles', 0);

        foreach ($files as $f) {
            if ($f->is_valid_image()) {
                $url = \moodle_url::make_pluginfile_url($f->get_contextid(), $f->get_component(), $f->get_filearea(), null, $f->get_filepath(), $f->get_filename(), false);
            }
        }

        if ($url) {
            return $url->out();
        }

        return false;
    }

    /**
     * Temporary function for Moodle 4.0 upgrade - todo to be replaced.
     * @param object $section
     * @return string
     */
    private function temp_format_summary_text($section)
    {
        $summarytext = file_rewrite_pluginfile_urls($section->summary, 'pluginfile.php',
            $this->coursecontext->id, 'course', 'section', $section->id);

        $options = new \stdClass();
        $options->noclean = false;
        $options->overflowdiv = true;
        return format_text($summarytext, $section->summaryformat, $options);
    }

    /**
     * Temporary function for Moodle 4.0 upgrade - todo to be replaced.
     * @param object $section
     * @return void
     * @throws \coding_exception
     */
    private function temp_section_activity_summary($section)
    {
        $widgetclass = $this->format->get_output_classname('content\\section\\cmsummary');
        $widget = new $widgetclass($this->format, $section);
        $this->courserenderer->render($widget);
    }

    /**
     * Temporary function for Moodle 4.0 upgrade - todo to be replaced.
     * @param object $section
     * @return bool|string
     * @throws \coding_exception
     */
    private function temp_section_availability_message($section)
    {
        $widgetclass = $this->format->get_output_classname('content\\section\\availability');
        $widget = new $widgetclass($this->format, $section);
        return $this->courserenderer->render($widget);
    }

    /**
     * Temporary function for Moodle 4.0 upgrade - todo to be replaced.
     * @param object $mod
     * @return bool|string
     * @throws \coding_exception
     */
    private function temp_course_section_cm_availability($mod)
    {
        $availabilityclass = $this->format->get_output_classname('content\\cm\\availability');
        $availability = new $availabilityclass(
            $this->format,
            $mod->get_section_info(),
            $mod,
        );
        return $this->courserenderer->render($availability);
    }

    /**
     * Append the data we need to render section zero.
     * @param [] $data
     * @param \renderer_base $output
     * @return mixed
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function append_section_zero_data($data, $output)
    {
        $course = $this->format->get_course();
        $seczero = $this->modinfo->get_section_info(0);
        $collapsed = false;

        if ($this->modinfo->get_section_info(0)->name) {
            $title = $this->modinfo->get_section_info(0)->name;
        } else {
            $title = get_string('section0name', 'format_menutab');
        }
        // Is section zero collapsed
        if ($course->collapsed) {
            $collapsed = true;
        }
        $data['section_zero']['summary'] = self::temp_format_summary_text($seczero);
        $data['section_zero']['content']['course_modules'] = $this->section_course_mods($seczero, $output);
        $data['section_zero']['secid'] = $this->modinfo->get_section_info(0)->id;
        $data['section_zero']['title'] = $title;
        $data['section_zero']['is_section_zero'] = true;
        $data['section_zero']['visible'] = true;
        $data['section_zero']['collapsed'] = $collapsed;
        return $data;
    }

    /**
     * Get the course format options (how depends on where we are calling from).
     * @param bool $fromajax is this request from AJAX.
     * @return array
     */
    private function get_course_format_options($fromajax)
    {

        $data = [];
        $data = $this->format->get_format_options();
        return $data;
    }

    /**
     * Take the "common data" supplied as the $data argument, and build on it
     * with data which is specific to single section pages, then return
     * the amalgamated data
     * @param \renderer_base $output the renderer for this format
     * @param array $data the common data
     * @return array the amalgamated data
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function append_single_section_page_data($output, $data)
    {
        // If we have nothing to output, don't.
        if (!($thissection = $this->modinfo->get_section_info($this->sectionnum))) {
            // This section doesn't exist.
            debugging('Unknown course section ' . $this->sectionnum, DEBUG_DEVELOPER);
            return $data;
        }
        if (!$thissection->uservisible) {
            // Can't view this section - in that case the template will just render 'Not available' and nothing else.
            $data['hidden_section'] = true;
            return $data;
        }

        // Data for the requested section page.
        $data['title'] = format_string(get_section_name($this->course, $thissection->section));
        if (get_config('format_menutab', 'enablelinebreakfilter')) {
            // No need to line break here as we have plenty of room, so remove the char by passing true.
            $data['title'] = $this->apply_linebreak_filter($data['title'], true);
        }
        $data['summary'] = self::temp_format_summary_text($thissection);
        $data['cardid'] = $thissection->section;
        $data['secid'] = $thissection->id;

        // If photo tile backgrounds are allowed by site admin, prepare the image for this section.
        if (get_config('format_menutab', 'allowphototiles')) {
            $tilephoto = new tile_photo($this->course->id, $thissection->id);
            $tilephotourl = $tilephoto->get_image_url();

            $data['phototileinlinestyle'] = 'style = "background-image: url(' . $tilephotourl . ');"';
            $data['hastilephoto'] = $tilephotourl ? 1 : 0;
            $data['phototileurl'] = $tilephotourl;
            $data['phototileediturl'] = new \moodle_url(
                '/course/format/tiles/editimage.php',
                array('courseid' => $this->course->id, 'sectionid' => $thissection->id)
            );
        }

        // Include completion help icon HTML.
        if ($this->completioninfo) {
            $data['completion_help'] = true;
        }

        $data['tabs'] = $this->get_section_tab_list($thissection, $output);

        // The list of activities on the page (HTML).
//        $data['course_modules'] = $this->section_course_mods($thissection, $output);

        $data['visible'] = $thissection->visible;
        // If user can view hidden items, include the explanation as to why an item is hidden.
        if ($this->canviewhidden) {
            $data['availabilitymessage'] = self::temp_section_availability_message($thissection);
        }
        return $data;
    }

    /**
     * Take the "common data" supplied as the $data argument, and build on it
     * with data which is specific to multiple section pages, then return
     * the amalgamated data
     * @param array $data the common data
     * @return array the amalgamated data
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function append_home_page_data($data)
    {
        global $CFG;
        $data['is_home_page'] = true;
        // If using completion tracking, get the data.
        if ($this->completionenabled) {
            $data['overall_progress']['num_complete'] = 0;
            $data['overall_progress']['num_out_of'] = 0;
        }
        $data['hasNoSections'] = true;

        // Before we start the section loop. get key vars for rows and columns
        $number_of_sections = (count($this->format->get_sections()) - 1); // remove section zero
        $number_of_rows = ceil($number_of_sections / $data['numcolumns']);

        $maxallowedsections = $this->format->get_max_sections();
        $sectioncountwarningissued = false;

        $countincludedsections = 0;
        $image_count = 0;
        foreach ($this->modinfo->get_section_info_all() as $sectionnum => $section) {
            // Show the section if the user is permitted to access it, OR if it's not available
            // but there is some available info text which explains the reason & should display,
            // OR it is hidden but the course has a setting to display hidden sections as unavilable.

            $showsection = $section->uservisible ||
                ($section->visible && !$section->available && !empty($section->availableinfo));
            if ($sectionnum != 0 && $showsection) {
                $longtitlelength = 65;
                // Only add if section is visible to user
                if ($section->uservisible) {
                    if ($section->name) {
                        $title = $section->name;
                    } else {

                        $title = get_string('sectionname', 'format_menutab') . ' ' . $section->section;
                    }

                    $summary = self::temp_format_summary_text($section);
                    //Get image for top of card
                    preg_match('/<img(.*)src(.*)=(.*)"(.*)"/U', $summary, $result);
                    if (isset($result[0])) {
                        $image = $result[0] . ' class="card-image-top"  style="height: 160px; width: 100%; object-position: center; object-fit: cover" alt="Image"/>';
                    } else {
                        // Use a default image
                        $image = '<img src="' . $CFG->wwwroot . '/course/format/menutab/images/' . $image_count . '.png" class="card-image-top"  style="height: 160px; width: 100%; object-position: center; object-fit: cover" alt="Image"/>';
                        $image_count++;
                        // Reset image count
                        if ($image_count > 6) {
                            $image_count = 0;
                        }
                    }
                    // Remove image from summary
                    $summary = preg_replace("/<img[^>]+\>/i", "", $summary);

                    $section_card = array(
                        'cardid' => $section->section,
                        'secid' => $section->id,
                        'courseid' => $section->course,
                        'available' => $section->available,
                        'availability' => $section->availability,
                        'title' => $title,
                        'summary' => $summary,
                        'image' => $image,
                        'current' => course_get_format($this->course)->is_section_current($section),
                        'uservisible' => $section->uservisible,
                        'visible' => $section->visible,
                        'restricted' => !($section->available),
                        'userclickable' => $section->available || $section->uservisible,
                        'activity_summary' => self::temp_section_activity_summary($section),
                        'titleclass' => strlen($title) >= $longtitlelength ? ' longtitle' : '',
                        'progress' => false,
                        'isactive' => $this->course->marker == $section->section,
                        'extraclasses' => '',
                    );

                    // Include completion tracking data for each section (if used).
                    if ($section->visible && $this->completionenabled) {
                        if (isset($this->modinfo->sections[$sectionnum])) {
                            $completionthissection = $this->section_progress($this->modinfo->sections[$sectionnum], $this->modinfo->cms);
                            // Keep track of overall progress so we can show this too - add this section's completion to the totals.
                            $data['overall_progress']['num_out_of'] += $completionthissection['outof'];
                            $data['overall_progress']['num_complete'] += $completionthissection['completed'];

                            // We only add the section values to the individual sections if courseshowsectionprogress is true.
                            // (Otherwise we only retain overall completion as above, not for each section).

                            $showaspercent = true;
                            if ($this->courseformatoptions['print_progress'] && $completionthissection['outof'] > 0) {
                                $section_card['progress'] = $this->completion_indicator(
                                    $completionthissection['completed'],
                                    $completionthissection['outof'],
                                    $showaspercent,
                                    false
                                );
                            }

                        }
                    }

                    // If item is restricted, user needs to know why.
                    $section_card['availabilitymessage'] = $section->availableinfo || !$section->visible
                        ? self::temp_section_availability_message($section) : '';

                    // See below about when "hide add cm control" is true.
                    $section_card['hideaddcmcontrol'] = false;
                    $section_card['single_sec_add_cm_control_html'] = $this->courserenderer->course_section_add_cm_control(
                        $this->course, $section->section, 0
                    );

                    $section_cards[] = $section_card;
                }

            } else if ($sectionnum == 0) {
                // Add in section zero completion data to overall completion count.
                if ($section->visible && $this->completionenabled) {
                    if (isset($this->modinfo->sections[$sectionnum])) {
                        $completionthissection = $this->section_progress($this->modinfo->sections[$sectionnum], $this->modinfo->cms);
                        // Keep track of overall progress so we can show this too - add this section's completion to the totals.
                        $data['overall_progress']['num_out_of'] += $completionthissection['outof'];
                        $data['overall_progress']['num_complete'] += $completionthissection['completed'];
                    }
                }
            }
            $countincludedsections++;
        }

        // Create rows for cards based on format number of rows
        $y = 0; //sectioncards array count
        for ($i = 0; $i < $number_of_rows; $i++) { // Loop through all rows
            for ($x = 0; $x < $data['numcolumns']; $x++) {
                if (($y - 1) <= $number_of_sections) {
                    if (isset($section_cards[$y])) {
                        $data['sectionrows'][$i]['sections'][] = $section_cards[$y];
                        $y++;
                    }
                }
            }

        }

        $data['sectioncards'] = $data['sectionrows'];
        $data['section_zero_add_cm_control_html'] = $this->courserenderer->course_section_add_cm_control($this->course, 0, 0);
        if ($this->completionenabled && $data['overall_progress']['num_out_of'] > 0) {
            if (get_config('format_menutab', 'print_progress')) {
                $data['overall_progress_indicator'] = $this->completion_indicator(
                    $data['overall_progress']['num_complete'],
                    $data['overall_progress']['num_out_of'],
                    true,
                    true
                );
                $data['overall_progress_indicator']['cardid'] = 0;
            }
        }
        $data['moodlefiltersconfig'] = $this->get_filters_config();

        return $data;
    }

    /**
     * Count the number of course modules with completion tracking activated
     * in this section, and the number which the student has completed
     * Exclude labels if we are using sub sections, as these are not checkable
     * Also exclude items the user cannot see e.g. restricted
     * @param array $sectioncmids the ids of course modules to count
     * @param array $coursecms the course module objects for this course
     * @return array with the completion data x items complete out of y
     */
    public function section_progress($sectioncmids, $coursecms)
    {
        $completed = 0;
        $outof = 0;
        foreach ($sectioncmids as $cmid) {
            $thismod = $coursecms[$cmid];
            if ($thismod->uservisible && !$thismod->deletioninprogress) {
                if ($this->completioninfo->is_enabled($thismod) != COMPLETION_TRACKING_NONE) {
                    $outof++;
                    $completiondata = $this->completioninfo->get_data($thismod, true);
                    if ($completiondata->completionstate == COMPLETION_COMPLETE ||
                        $completiondata->completionstate == COMPLETION_COMPLETE_PASS
                    ) {
                        $completed++;
                    }
                }
            }
        }
        return array('completed' => $completed, 'outof' => $outof);
    }

    /**
     * @param $section
     * @param $output
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function get_section_tab_list($section, $output)
    {
        if (!isset($section->section)) {
            debugging("section->section is not set", DEBUG_DEVELOPER);
        }

        if (!isset($this->modinfo->sections[$section->section]) || !$cmids = $this->modinfo->sections[$section->section]) {
            // There are no CMs for the section (i.e. section is empty) so we silently return.
            return [];
        }
        if (empty($cmids)) {
            // There are no CMs for the section (i.e. section is empty) so we silently return.
            return [];
        }
        $previouswaslabel = false;
        $sectioncontent = [];
        $tabs = [];
        $t = 0;
        // Create all tab objects
        foreach ($cmids as $index => $cmid) {
            $mod = $this->modinfo->get_cm($cmid);
            if ($mod->deletioninprogress) {
                continue;
            }

            // If no tab available, create a default tab
            if ($index == 0 && $mod->get_module_type_name()->get_component() != 'label') {
                $tabs[$t]['title'] = get_String('content', 'format_menutab');
                $tabs[$t]['tabid'] = $index;
                $tabs[$t]['cm_index_skip'] = -1;
                $tabs[$t]['cm_index_start'] = $index;
            } else if ($mod->get_module_type_name()->get_component() == 'label') {
                preg_match("#<\s*?h2\b[^>]*>(.*?)</h2\b[^>]*>#s", $mod->get_formatted_content(), $matches);
                if ($title = strip_tags($matches[1])) {
                    $tabs[$t]['title'] = $title;
                    $tabs[$t]['tabid'] = $index;
                    // Because this mod is a label and contains a tab, get next module that follows to start the
                    // content list
                    $tabs[$t]['cm_index_skip'] = $index;
                    $tabs[$t]['cm_index_start'] = $index + 1;
                }
            }

            $t++;
        }
        // reset tabs index
        $tabs = array_values($tabs);
        // get number of course modules
        $cm_count = count($cmids);
        // Loop through tabs and add course modules
        for ($x = 0; $x < count($tabs); $x++) {
            if ($x == 0 ) {
                $tabs[$x]['class'] = 'show active';
                $tabs[$x]['active'] = 'active';
            } else {
                $tabs[$x]['class'] = '';
                $tabs[$x]['active'] = '';
            }
            // If there is an tab after this one, only print the modules for that tab
            if (isset($tabs[$x + 1])) {
                for ($i = $tabs[$x]['cm_index_start']; $i < $tabs[$x + 1]['cm_index_skip']; $i++) {
                    $mod = $this->modinfo->get_cm($cmids[$i]);
                    if (!$mod->deletioninprogress) {
                        $moduledata = $this->course_module_data(
                            $mod,
                            $section,
                            $previouswaslabel,
                            $index == 0,
                            $output
                        );
                        $tabs[$x]['course_modules'][] = $moduledata;
                    }

                }
            } else {
                for ($i = $tabs[$x]['cm_index_start']; $i < $cm_count; $i++) {
                    $mod = $this->modinfo->get_cm($cmids[$i]);
                    if (!$mod->deletioninprogress) {
                        $moduledata = $this->course_module_data(
                            $mod,
                            $section,
                            $previouswaslabel,
                            $index == 0,
                            $output
                        );
                        $tabs[$x]['course_modules'][] = $moduledata;
                    }

                }
            }

        }
        return $tabs;
    }

    /**
     * Watch for the word joiner character '&#8288;' in very long tile titles.
     * When encountered on a tile title, this char is changed to '- ' to allow the text to wrap.
     * This is useful on tiles with long words in the title (e.g. German language).
     * @param string $text
     * @param bool $remove if we want just to remove the flag (no need to line break), pass true.
     * @return string
     */
    private function apply_linebreak_filter(string $text, $remove = false)
    {
        $zerowidthspace = '&#8288;';
        $maxwidthfortilechars = 15;
        if (!$remove && strlen($text) > $maxwidthfortilechars) {
            // If the title is long, we want to line break with a -, so replace the zero width space with hyphen space.
            return format_string(str_replace($zerowidthspace, '- ', $text));
        } else {
            // If the title is short, we don't need to line break so delete the flag.
            return format_string(str_replace($zerowidthspace, '', $text));
        }
    }

    /**
     * Gets the data (context) to be used with the activityinstance template
     * @param object $section the section object we want content for
     * @param \renderer_base $output
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     * @see \core_completion\manager::get_activities() which covers similar ground
     * @see \core_course_renderer::course_section_cm_completion() which covers similar ground
     * In the snap theme, course_renderer::course_section_cm_list_item() covers similar ground
     * @see \cm_info for full detail of $mod instance variables
     */
    private function section_course_mods($section, $output)
    {
        if (!isset($section->section)) {
            debugging("section->section is not set", DEBUG_DEVELOPER);
        }

        if (!isset($this->modinfo->sections[$section->section]) || !$cmids = $this->modinfo->sections[$section->section]) {
            // There are no CMs for the section (i.e. section is empty) so we silently return.
            return [];
        }
        if (empty($cmids)) {
            // There are no CMs for the section (i.e. section is empty) so we silently return.
            return [];
        }
        $previouswaslabel = false;
        $sectioncontent = [];
        foreach ($cmids as $index => $cmid) {
            $mod = $this->modinfo->get_cm($cmid);
            if ($mod->deletioninprogress) {
                continue;
            }

            $moduledata = $this->course_module_data(
                $mod,
                $section,
                $previouswaslabel,
                $index == 0,
                $output
            );

            if (!empty($moduledata)) {
                $sectioncontent[] = $moduledata;
                $previouswaslabel = $mod->has_custom_cmlist_item();
            }

        }
        return $sectioncontent;
    }

    /**
     * Assemble and return the data to render a single course module.
     * @param \cm_info $mod
     * @param object $section
     * @param bool $previouswaslabel
     * @param bool $isfirst
     * @param \renderer_base $output
     * @return array
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    private function course_module_data($mod, $section, $previouswaslabel, $isfirst, $output)
    {
        global $PAGE, $CFG, $DB, $USER;
        $displayoptions = [];
        $obj = new \core_courseformat\output\local\content\section\cmitem($this->format, $section, $mod, $displayoptions);
        $moduleobject = (array)$obj->export_for_template($output);
        return $moduleobject;
    }

    /**
     * Get resource file type e.g. 'doc' from the icon URL e.g. 'document-24.png'
     * Not ideal but we already have icon name so it's efficient
     * Adapted from Snap theme
     * @param \cm_info $mod the mod info object we are checking
     * @return string the type e.g. 'doc'
     * @see mod_displayname() which gets the display name for the type
     *
     */
    private function get_resource_filetype(\cm_info $mod)
    {
        if ($mod->modname === 'resource') {
            $fs = get_file_storage();
            $files = $fs->get_area_files($mod->context->id, 'mod_resource', 'content');
            $extensions = array(
                'powerpoint' => 'ppt',
                'document' => 'doc',
                'spreadsheet' => 'xls',
                'archive' => 'zip',
                'application/pdf' => 'pdf',
                'mp3' => 'mp3',
                'mpeg' => 'mp4',
                'image/jpeg' => 'jpeg',
                'text/plain' => 'txt',
                'text/html' => 'html'
            );
            foreach ($files as $file) {
                if ($file->get_filesize() && $mimetype = $file->get_mimetype()) {
                    if (in_array($mimetype, array_keys($extensions))) {
                        return $extensions[$mimetype];
                    }
                }
            }
        }
        return '';
    }

    /**
     * Get the display name for each module or resource type
     * from the modname, to be displayed at the top of each tile
     * e.g. 'mod/lti' => 'External Tool' 'mod/resource','xls' = "Spreadsheet'
     * Once we have it , store it in instance var e.g. to avoid repeated check of 'pdf'
     * @param string $modname the name of the module e.g. 'resource'
     * @param string|null $resourcetype if this is a resource, the specific type eg. 'xls' or 'pdf'
     * @return string to be displayed on tile
     * @throws \coding_exception
     * @see get_resource_filetype()
     */
    private function mod_displayname($modname, $resourcetype = null)
    {
        if ($modname == 'resource') {
            if (isset($this->resourcedisplaynames[$resourcetype])) {
                return $this->resourcedisplaynames[$resourcetype];
            } else if (get_string_manager()->string_exists('displaytitle_mod_' . $resourcetype, 'format_menutab')) {
                $str = get_string('displaytitle_mod_' . $resourcetype, 'format_menutab');
                $this->resourcedisplaynames[$resourcetype] = $str;
                return $str;
            } else {
                $str = get_string('other', 'format_menutab');
                $this->resourcedisplaynames[$resourcetype] = $str;
                return $str;
            }
        } else {
            return get_string('modulename', 'mod_' . $modname);
        }
    }

    /**
     * Prepare the data required to render a progress indicator (.e. 2/3 items complete)
     * to be shown on the tile or as an overall course progress indicator
     * @param int $numcomplete how many items are complete
     * @param int $numoutof how many items are available for completion
     * @param boolean $aspercent should we show the indicator as a percentage or numeric
     * @param boolean $isoverall whether this is an overall course completion indicator
     * @return array data for output template
     */
    public function completion_indicator($numcomplete, $numoutof, $aspercent, $isoverall)
    {
        $percentcomplete = $numoutof == 0 ? 0 : round(($numcomplete / $numoutof) * 100, 0);
        //Set progressbar colors
        if ($percentcomplete <= 59) {
            $background_color = 'bg-danger';
        } else if ($percentcomplete >= 60 && $percentcomplete < 100) {
            $background_color = 'bg-warning';
        } else if ($percentcomplete == 100) {
            $background_color = 'bg-success';
        }
        $progressdata = array(
            'numComplete' => $numcomplete,
            'numOutOf' => $numoutof,
            'percent' => $percentcomplete,
            'isComplete' => $numcomplete > 0 && $numcomplete == $numoutof ? 1 : 0,
            'isOverall' => $isoverall,
            'backgroundColor' => $background_color,
        );
        if ($aspercent) {
            // Percent in circle.
            $progressdata['showAsPercent'] = true;
            $circumference = 106.8;
            $progressdata['percentCircumf'] = $circumference;
            $progressdata['percentOffset'] = round(((100 - $percentcomplete) / 100) * $circumference, 0);
        }
        $progressdata['isSingleDigit'] = $percentcomplete < 10 ? true : false; // Position single digit in centre of circle.
        return $progressdata;
    }


    /**
     * MathJax does not always seem to load (issue #60) so we assemble data so we can load it ourselves.
     * Also JS needs to know if "h5p" filter is being used, so we do that at the same time.
     * @return array
     */
    private function get_filters_config()
    {
        $activefilters = filter_get_active_in_context($this->coursecontext);
        $result = [];
        foreach ($activefilters as $filter => $v) {
            if ($filter === 'h5p') {
                // Need to know if we are using H5P filter as this may mean that we don't want to preload next sections.
                // If we did, when section pre-loads, any H5P filter activities set to 'complete on view' are complete.
                // This applies even if section is not ultimately viewed at all.
                $result[] = ['filter' => $filter];
            }
        }
        return $result;
    }

}
