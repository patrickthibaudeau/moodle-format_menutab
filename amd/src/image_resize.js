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

/* eslint space-before-function-paren: 0 */

/**
 * Javascript Module to handle changes which are made to the course > edit settings
 * form as the user changes various options
 * e.g. if user deselects one item, this deselects another linked one for them
 * if the user picks an invalid option it will be detected by format_tiles::edit_form_validation (lib.php)
 * but this is to help them avoid triggering that if they have JS enabled
 *
 * Plugin version and other meta-data are defined here.
 *
 * @package     format_menutab
 * @copyright   2022 UIT Innovation  <thibaud@yorku.ca>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *  */

define(["jquery", "core/config"],
    function ($, mdlcfg) {
        "use strict";
        return {
            init: function () {
                // get images on page
                const summary = document.getElementsByClassName('course-description-item summarytext');
                // loop through all sectin summaries
                for (let i = 0; i < summary.length; i++) {
                    const image = summary[i].getElementsByTagName('img')[0];
                    // only make changes if there is an image
                    if (image)  {
                        // remove image from summary
                        image.parentNode.removeChild(image);
                        let summaryText = summary[i].innerHTML;
                        let imageSrc = image.currentSrc;
                        // Remove image form source code
                        summaryText = summaryText.replace(/<img[^>]*>/gi,"");
                        // Delete summary
                        summary[i].innerHTML = '';
                        // Create style
                        let style = '<style>';
                        style += '.format_menuttab_section_summary_' + i + ' {';
                        style += 'width: 100%;';
                        style += 'height: 150px;';
                        style += 'background-image: url("' + imageSrc + '");';
                        style += 'background-repeat:no-repeat;';
                        style += 'background-position: center center;';
                        style += 'background-size: cover;';
                        style += '}';
                        style += '</style>';
                        // Create html element
                        let html = '<div class="format_menuttab_section_summary_' + i + '"></div>';
                        // Add sumamry text to html
                        html += summaryText;
                        // Add style to head
                        document.head.innerHTML += style;
                        // Add html
                        summary[i].innerHTML = html;
                    }
                };
            }
        };
    }
);