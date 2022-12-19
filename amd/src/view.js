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
                $(document).ready(function () {
                    // Make sure we are in the menutab format
                    if ($('#page-course-view-menutab').length) {

                        if (!$('#format_menutab_grid').length) {
                            let course = getUrlVars()["id"];
                            // Remove hash tag
                            course = course.replace('#', '');
                            let html = '<div style="float: right;">&nbsp;&nbsp;\n' +
                                '                    <a href="#"\n' +
                                '                    title="Grid view" data-view="grid" data-course="' + course + '" class="local_menutab_grid_list_view">\n' +
                                '                        <i class="fa fa-th" style="font-size:0.5em"></i></a>\n' +
                                '                </div>';
                            $(".page-header-headings").closest('div').find('h1').append(html);
                        }
                    }


                    $('.local_menutab_grid_list_view').on('click', function () {
                        let view = $(this).data('view');
                        let course = $(this).data('course');

                        if (view == 'grid') {
                            $.ajax({
                                url: mdlcfg.wwwroot + '/course/format/menutab/update_view.php?course=' + course + '&table_contents=0',
                                success: function () {
                                    location.reload();
                                }
                            });
                        } else {
                            $.ajax({
                                url: mdlcfg.wwwroot + '/course/format/menutab/update_view.php?course=' + course + '&table_contents=1',
                                success: function () {
                                    location.reload();
                                }
                            });
                        }
                    });

                }); // document.ready
            }
        };

        function getUrlVars()
        {
            var vars = [], hash;
            var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
            for(var i = 0; i < hashes.length; i++)
            {
                hash = hashes[i].split('=');
                vars.push(hash[0]);
                vars[hash[0]] = hash[1];
            }
            return vars;
        }
    }
);