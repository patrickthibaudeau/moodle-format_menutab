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
                // Must set a timeout of 1 second in order for nav index to load.
                setTimeout(function () {
                        // targets from the index nav drawer
                        const targets = document.getElementsByClassName('dropready');
                        // move Items from the card update menu
                        const moveItems = document.getElementsByClassName('move');
                        // Capture the Hide show section button
                        const showHide = document.getElementsByClassName(' editing_showhide');
                        // Capture delete section
                        const deleteItem = document.getElementsByClassName(' editing_delete');

                        // Drag N Drop nav index
                        for (let i = 0; i < targets.length; i++) {
                            targets[i].addEventListener('drop', (event) => {
                                // Reload page if section has been dropped
                                location.reload();
                            });
                        }
                        ;

                        // Move button from section edit menu
                        for (let x = 0; x < moveItems.length; x++) {
                            moveItems[x].addEventListener('click', (event) => {
                                // Wait for modal to open before getting list items
                                setTimeout(function () {
                                    let listItems = document.getElementsByClassName('collapse-list-link');
                                    // Reload page if lsit item clicked
                                    for (let y = 0; y < listItems.length; y++) {
                                        listItems[y].addEventListener('click', (event) => {
                                            location.reload();
                                        });
                                    }
                                    ;
                                }, 1000);
                            });
                        }
                        ;

                        //  SHow/Hide button from edit menu
                        for (let j = 0; j < showHide.length; j++) {
                            showHide[j].addEventListener('click', (event) => {
                                // Reload page if section has been dropped
                                setTimeout(function () {
                                    location.reload();
                                }, 100);
                            });
                        }
                        ;

                        // Delete button from edit menu
                        for (let k = 0; k < deleteItem.length; k++) {
                            deleteItem[k].addEventListener('click', (event) => {
                                // Reload page if section has been dropped
                                // location.reload();
                            });
                        }
                        ;

                    }, 1000
                );

            }
        };
    }
);