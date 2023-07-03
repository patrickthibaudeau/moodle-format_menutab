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

$string['collapsed'] = 'Section supérieure rétractée ?';
$string['collapsed_help'] = 'Should top section (in regular Moodle courses know as Section 0) be collapsed when students enter the course? Default Yes';
$string['course_title_color'] = 'Couleur du nom du cours';
$string['course_title_color_help'] = 'Select a color for the course name on the home page. This is important if you add an image.';
$string['course_title_position'] = 'Position du nom du cours';
$string['course_title_position_help'] = 'Select the postion the course name should be in.';
$string['currently_editing'] = 'You are currently editing your course';
$string['currentsection'] = 'This topic';
$string['custom_css'] = 'CSS personnalisé';
$string['custom_css_help'] = '<h4>Avertissement!</h4>Vous pouvez ajouter un CSS personnalisé pour ce format de cours ici. 
Cela remplacera tout autre CSS de ce cours. Si vous n\'êtes pas familier avec CSS, veuillez ne pas utiliser cette fonctionnalité. 
Vous pouvez casser votre cours.';
$string['darken_background_image'] = 'Foncer l\'image du cours';
$string['darken_background_image_help'] = 'Set to yes if the course name does not meat accessibility contrast ratios.';
$string['grid_view'] = 'Grid view';
$string['hidden'] = 'Unavailable for students';
$string['hidden_sections'] = 'Sections/modules cachés';
$string['hidden_sections_in_container'] = 'Regrouper les sections masquées';
$string['hidden_sections_in_container_help'] = 'Sélectionnez si vous souhaitez ou non regrouper les sections masquées dans un conteneur.
Si oui, les sections masquées seront regroupées dans un conteneur au bas de la page du cours et visibles uniquement en mode édition.';
$string['hidefromothers'] = 'Hide from others';
$string['jump_to_hidden_sections'] = 'Accéder aux sections masquées';
$string['showfromothers'] = 'Show from others';
$string['list_view'] = 'List view';
$string['main_menu'] = 'Course menu';
$string['numcolumns'] = 'Number of columns';
$string['numcolumns_help'] = 'Select the number of columns you would like per row on the course front page.';
$string['return'] = 'Course home';
$string['start_here'] = 'Start here';
$string['pluginname'] = 'Menu/Onglet';
$string['print_default_section_image'] = 'Utiliser une image par défaut pour les sections sans image';
$string['print_default_section_image_help'] = 'S\'il n\'y a pas d\'image pour une section, une image par défaut sera utilisée.';
$string['print_progress'] = 'Afficher la progression de l\'achèvement sur la carte';
$string['print_progress_help'] = 'If yes, and completion tracking is enabled, a progress bar will be displayed at the bottom of the section card on the course front page.';
$string['print_section_number'] = 'Afficher le numéro de section en haut de l\'image de la carte';
$string['print_section_number_help'] = 'If yes, the section number will be displayed on top of the image wihtin each section card on the front page';
$string['section0name'] = 'Commencer ici';
$string['section_zero_background_color'] = 'Couleur de fond de l\'en-tête de la section zéro';
$string['section_zero_background_color_help'] = 'Select a color for the section zero header color.';
$string['privacy:metadata'] = 'The Menu/Tab Course Format does not store any personal data.';
$string['section_number_text_color'] = 'Couleur du texte du numéro de section';
$string['section_number_text_color_help'] = 'Select the text color printed over the card images';
$string['selected'] = 'Selected';
$string['show_summary_single_section'] = 'Afficher le résumé de la section';
$string['show_summary_single_section_help'] = 'If yes, when viewing a section, the summary text for that section will be shown below the image.';
$string['stretch_columns'] = 'Étirer les colonnes';
$string['stretch_columns_help'] = 'If yes, when ever a row has less sections than the number of columns selected, the remaining columns will strech to fill the space. Otherwise, all columns will be the same size.';
$string['tab_background_color'] = 'Couleur de fond de l\'onglet';
$string['tab_background_help'] = 'Tab background colour';
$string['tab_background_color_help'] = 'Select a background color for the tabs in the section page';
$string['tab_background_colors'] = 'Tab background colors';
$string['tab_background_colors_help'] = 'Enter a color per line. Each color must use the hex value of the color seperated by a pipe (|) followed by the name of the color.';
$string['tab_text_color'] = 'Couleur du texte de l\'onglet';
$string['tab_text_color_help'] = 'Select a color for the text. Make sure that based on the above color, the contrast will be high enough.';
$string['use_edit_mode_reminder'] = 'Utiliser le rappel du mode d\'édition';
$string['use_edit_mode_reminder_help'] = 'Si oui, un rappel s\'affichera en haut de la page du cours en mode édition.
Notez que cela sera vrai pour tous les instructeurs.';
$string['use_image_css'] = 'Utiliser le CSS d\'image par défaut dans les sections';
$string['use_image_css_help'] = 'Cette option contrôle l\'affichage de l\'image du haut de chaque section. Si vous choisissez oui,
l\'image sera redimensionnée pour s\'adapter à la hauteur div de 160px, centrée et recadrée si nécessaire. Si vous choisissez non,
l\'image conservera sa taille et sa forme d\'origine, mais vous devez fournir des images pour toutes les sections.';
$string['your_section_progress'] = 'Your progress in this section is currently at ';
$string['your_section_progress'] = 'Your progress in this section is currently at ';
//New tag
$string['access'] = 'Access';
$string['toggle_course_menu'] = 'Toggle course menu';
//Required language strings
$string['addsections'] = 'Add section';
$string['currentsection'] = 'This section';
$string['deletesection'] = 'Delete section';
$string['editsection'] = 'Edit section';
$string['editsectionname'] = 'Edit section name';
$string['hidefromothers'] = 'Hide section';
$string['newsectionname'] = 'New name for section {$a}';
$string['sectionname'] = 'Section';
$string['showfromothers'] = 'Show section';
// Colors
$string['black'] = 'Noir';
$string['white'] = 'Blanc';
$string['yellow'] = 'Jaune';
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