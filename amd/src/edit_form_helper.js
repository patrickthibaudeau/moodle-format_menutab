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

define(["jquery", "core/notification", "core/str", "core/templates"],
    function ($, Notification, str, Templates) {
        "use strict";
        return {
            init: function (pageType) {
                $(document).ready(function () {


                    // Create clickable colour swatch for each colour in the select drop down to help user choose.
                    var tabBackgroundColorMenu = $("select#id_tab_background_color");
                    Templates.render("format_menutab/colour_picker", {
                        colours: tabBackgroundColorMenu.find("option").map(
                            function (index, option) {
                                var optselector = $(option);
                                var colour = optselector.attr("value");
                                return {
                                    colour: colour,
                                    colourname: optselector.text(),
                                    selected: colour === tabBackgroundColorMenu.val(),
                                    id: colour.replace("#", "") + "_tab_background"
                                };
                            }
                        ).toArray()
                    }).done(function (html) {
                        // Add the newly created colour picker next to the standard select menu.
                        $(html).insertAfter(tabBackgroundColorMenu);
                        // Now that users are using the colour circles we can hide the text menu.
                        tabBackgroundColorMenu.hide();
                        // Watch for clicks on each circle and set select menu to correct colour on click.

                        var circles = $(".colourpickercircle");

                        circles.click(function (e) {
                            var clicked = $(e.currentTarget);
                            circles.removeClass("selected");
                            clicked.addClass("selected");
                            tabBackgroundColorMenu.val(clicked.attr("data-colour"));
                            $("#colourselectnotify").fadeIn(200).fadeOut(1200);
                        });

                        tabBackgroundColorMenu.change(function () {
                            circles.removeClass("selected");
                            $("#colourpick_" + tabBackgroundColorMenu.val().replace("#", "") + "_tab_background").addClass("selected");
                        });

                        // If the course is being switched in to "Tiles", body will still have old format class e.g. format-topics.
                        // This comes from core.  We want body to have format-tiles class for our colour picker CSS, so we add it.
                        var body = $("body");
                        if (!body.hasClass("format-menutab")) {
                            body.addClass("format-menutab");
                        }
                    });

                    // Create clickable colour swatch for each colour in the select drop down for section zero background color.
                    var sectionZeroBackgorundHeaderMenu = $("select#id_section_zero_background_color");
                    Templates.render("format_menutab/colour_picker", {
                        colours: sectionZeroBackgorundHeaderMenu.find("option").map(
                            function (index, option) {
                                var optselector = $(option);
                                var colour = optselector.attr("value");
                                return {
                                    colour: colour,
                                    colourname: optselector.text(),
                                    selected: colour === sectionZeroBackgorundHeaderMenu.val(),
                                    id: colour.replace("#", "") + "_section_zero_background"
                                };
                            }
                        ).toArray()
                    }).done(function (html) {
                        // Add the newly created colour picker next to the standard select menu.
                        $(html).insertAfter(sectionZeroBackgorundHeaderMenu);
                        // Now that users are using the colour circles we can hide the text menu.
                        sectionZeroBackgorundHeaderMenu.hide();
                        // Watch for clicks on each circle and set select menu to correct colour on click.

                        var circles = $(".colourpickercircle");

                        circles.click(function (e) {
                            var clicked = $(e.currentTarget);
                            circles.removeClass("selected");
                            clicked.addClass("selected");
                            sectionZeroBackgorundHeaderMenu.val(clicked.attr("data-colour"));
                            $("#colourselectnotify").fadeIn(200).fadeOut(1200);
                        });

                        sectionZeroBackgorundHeaderMenu.change(function () {
                            circles.removeClass("selected");
                            $("#colourpick_" + sectionZeroBackgorundHeaderMenu.val().replace("#", "") + "_section_zero_background").addClass("selected");
                        });

                        // If the course is being switched in to "Tiles", body will still have old format class e.g. format-topics.
                        // This comes from core.  We want body to have format-tiles class for our colour picker CSS, so we add it.
                        var body = $("body");
                        if (!body.hasClass("format-menutab")) {
                            body.addClass("format-menutab");
                        }
                    });

                }); // document.ready
            }
        };
    }
);