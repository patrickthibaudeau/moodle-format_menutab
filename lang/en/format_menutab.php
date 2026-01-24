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
 * Plugin strings are defined here.
 *
 * @package     format_menutab
 * @category    string
 * @copyright   2022 UIT Innovation  <thibaud@yorku.ca>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['collapsed'] = 'Top section collapsed?';
$string['collapsed_help'] = 'Should top section (in regular Moodle courses know as Section 0) be collapsed when students enter the course? Default Yes';
$string['course_title_color'] = 'Course name color';
$string['content'] = 'Contents';
$string['course_title_color_help'] = 'Select a color for the course name on the home page. This is important if you add an image.';
$string['course_title_position'] = 'Course name position';
$string['course_title_position_help'] = 'Select the postion the course name should be in.';
$string['course_title_show'] = 'Display course name';
$string['course_title_show_help'] = 'Select whether or not you want the name of your course displayed. For example, if you are using an image with the title in it, you may want to remove the course name.';
$string['currently_editing'] = 'You are currently editing your course';
$string['currentsection'] = 'This topic';
$string['custom_css'] = 'Custom CSS';
$string['custom_css_help'] = '<h4>Warning!</h4>You can add custom CSS for this course format here. This will override any 
other CSS within these course. If you are not familiar with CSS, please do not use this feature. You can break your course.';
$string['darken_background_image'] = 'Darken course image';
$string['darken_background_image_help'] = 'Set to yes if the course name does not meat accessibility contrast ratios.';
$string['grid_view'] = 'Grid view';
$string['hidden'] = 'Unavailable for students';
$string['hidden_sections'] = 'Hidden sections/modules';
$string['hidden_sections_in_container'] = 'Group hidden sections';
$string['hidden_sections_in_container_help'] = 'Select whether or not you want to group hidden sections in a container. 
If yes, hidden sections will be grouped in a container at the bottom of the course page and only visible when in edit mode.';
$string['hidefromothers'] = 'Hide from others';
$string['jump_to_hidden_sections'] = 'Jump to hidden sections';
$string['showfromothers'] = 'Show from others';
$string['list_view'] = 'List view';
$string['main_menu'] = 'Course menu';
$string['numcolumns'] = 'Number of columns';
$string['numcolumns_help'] = 'Select the number of columns you would like per row on the course front page.';
$string['return'] = 'Course home';
$string['start_here'] = 'Start here';
$string['plugin_description'] = 'The course is divided into grid sections. Each section can then be divided into tabs.' .
' <b>Avoid using sub-sections</b> as they will not display properly.';
$string['pluginname'] = 'Menu/Tab';
$string['print_default_section_image'] = 'Use a default image for sections without an image';
$string['print_default_section_image_help'] = 'If there is no image for a section, a default image will be used.';
$string['print_overall_progress'] = 'Display course completion progress';
$string['print_overall_progress_help'] = 'If yes, the course completion percentage will be displayed in the center of the start bar.';
$string['print_progress'] = 'Display completion progress on card';
$string['print_progress_help'] = 'If yes, and completion tracking is enabled, a progress bar will be displayed at the bottom of the section card on the course front page.';
$string['print_section_number'] = 'Display section number on top of card image';
$string['print_section_number_help'] = 'If yes, the section number will be displayed on top of the image wihtin each section card on the front page';
$string['print_start_button'] = 'Display Start here button?';
$string['print_start_button_help'] = 'If yes, a Start here button will be displayed on the banner. The button is used to open/close the collapsed section.';
$string['privacy:metadata'] = 'The Menutab course format plugin does not store any personal data.';
$string['section0name'] = 'Start';
$string['section_zero_background_color'] = 'Section zero header background color';
$string['section_zero_background_color_help'] = 'Select a color for the section zero header color.';
$string['privacy:metadata'] = 'The Menu/Tab Course Format does not store any personal data.';
$string['section_number_text_color'] = 'Section number text color';
$string['section_number_text_color_help'] = 'Select the text color printed over the card images';
$string['selected'] = 'Selected';
$string['show_summary_single_section'] = 'Show section summary';
$string['show_summary_single_section_help'] = 'If yes, when viewing a section, the summary text for that section will be shown below the image.';
$string['start_section_number'] = 'Start section number';
$string['start_section_number_help'] = 'Enter the number of the section you wish the numbering to start. For example, ' .
'if you have 13 sections and the first 3 sections are more information based (not actual course modules) you can enter 4' .
' to have the section numbering being on card 4.';
$string['stretch_columns'] = 'Stretch columns';
$string['stretch_columns_help'] = 'If yes, when ever a row has less sections than the number of columns selected, the remaining columns will strech to fill the space. Otherwise, all columns will be the same size.';
$string['tab_background_color'] = 'Tab background colour';
$string['tab_background_help'] = 'Tab background colour';
$string['tab_background_color_help'] = 'Select a background color for the tabs in the section page';
$string['tab_background_colors'] = 'Tab background colors';
$string['tab_background_colors_help'] = 'Enter a color per line. Each color must use the hex value of the color seperated by a pipe (|) followed by the name of the color.';
$string['tab_text_color'] = 'Tab text colour';
$string['tab_text_color_help'] = 'Select a color for the text. Make sure that based on the above color, the contrast will be high enough.';
$string['use_edit_mode_reminder'] = 'Use edit mode reminder';
$string['use_edit_mode_reminder_help'] = 'If yes, a reminder will be displayed at the top of the course page when in edit mode.
Note, this will be true for all instrctors.';
$string['use_image_css'] = 'Use default image CSS within sections';
$string['use_image_css_help'] = 'This option controls how the top image in each section is displayed. If you choose yes, 
the image will be resized to fit the div height of 160px, centered and cropped if necessary. If you choose no, 
the image will keep its original size and shape, but you must provide images for all sections.';
$string['your_section_progress'] = 'Your progress in this section is currently at ';
//New tag
$string['access'] = 'Access';
$string['toggle_course_menu'] = 'Toggle course menu';
//Required language strings
$string['addsection'] = 'Add section';
$string['addsections'] = 'Add sections';
$string['currentsection'] = 'This section';
$string['deletesection'] = 'Delete section';
$string['editsection'] = 'Edit section';
$string['editsectionname'] = 'Edit section name';
$string['hidefromothers'] = 'Hide section';
$string['newsectionname'] = 'New name for section {$a}';
$string['sectionname'] = 'Section';
$string['showfromothers'] = 'Show section';
// Colors
$string['black'] = 'Black';
$string['white'] = 'White';
$string['yellow'] = 'Yellow';
// Alignment
$string['bottom_left'] = 'Bottom left';
$string['bottom_center'] = 'Bottom center';
$string['bottom_right'] = 'Bottom right';
$string['middle_left'] = 'Middle left';
$string['middle_center'] = 'Middle center';
$string['middle_right'] = 'Middle right';
$string['top_left'] = 'Top left';
$string['top_center'] = 'Top center';
$string['top_right'] = 'Top right';
// Legacy conversion
$string['convert_legacy'] = 'Convert legacy labels to tabs';
$string['convert_legacy_title'] = 'Convert Legacy Labels to Subsection Tabs';
$string['convert_legacy_warning_title'] = 'Warning: This Action Cannot Be Undone';
$string['convert_legacy_warning'] = 'This will convert all labels with &lt;h2&gt; tags into subsection tabs. The original labels will be deleted, and activities will be moved to the new tabs. Please backup your course before proceeding.';
$string['conversion_success'] = 'Legacy labels successfully converted to subsection tabs!';
$string['conversion_error'] = 'An error occurred during conversion. Please check the logs.';
$string['no_legacy_labels'] = 'No legacy labels found in this course.';
$string['what_will_happen'] = 'What will happen:';
$string['conversion_step1'] = 'Labels with &lt;h2&gt; tags will be identified';
$string['conversion_step2'] = 'New subsection modules (tabs) will be created with the &lt;h2&gt; text as the tab name';
$string['conversion_step3'] = 'Activities following each &lt;h2&gt; label will be moved into the corresponding tab';
$string['conversion_step4'] = 'The original &lt;h2&gt; labels will be deleted';
$string['conversion_step5'] = 'Course cache will be rebuilt to reflect changes';
$string['labels_to_convert'] = 'The following labels will be converted to tabs:';
$string['convert_confirm'] = 'Yes, Convert to Subsection Tabs';
$string['invalidcourseformat'] = 'This course is not using the Menu/Tab format';

