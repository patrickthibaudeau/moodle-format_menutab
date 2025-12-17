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
 * Plugin upgrade steps are defined here.
 *
 * @package     format_menutab
 * @category    upgrade
 * @copyright   2022 UIT Innovation  <thibaud@yorku.ca>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/upgradelib.php');

/**
 * Execute format_menutab upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_format_menutab_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // For further information please read {@link https://docs.moodle.org/dev/Upgrade_API}.
    //
    // You will also have to create the db/install.xml file by using the XMLDB Editor.
    // Documentation for the XMLDB Editor can be found at {@link https://docs.moodle.org/dev/XMLDB_editor}.

    if ($oldversion < 2025110300) {
        // Migrate h2 labels to subsections.
        format_menutab_migrate_labels_to_subsections();

        upgrade_plugin_savepoint(true, 2025110300, 'format', 'menutab');
    }

    if ($oldversion < 2025111001) {
        // Fix numsections count to exclude subsections, preventing orphaned sections.
        format_menutab_fix_numsections_count();

        upgrade_plugin_savepoint(true, 2025111001, 'format', 'menutab');
    }

    if ($oldversion < 2025111002) {
        // Fix numsections count again with improved type handling and verification.
        format_menutab_fix_numsections_count();

        upgrade_plugin_savepoint(true, 2025111002, 'format', 'menutab');
    }

    if ($oldversion < 2025111003) {
        // Fix numsections count with corrected lib.php that respects stored values.
        format_menutab_fix_numsections_count();

        upgrade_plugin_savepoint(true, 2025111003, 'format', 'menutab');
    }

    if ($oldversion < 2025111004) {
        // Fix numsections to include ALL sections (regular sections AND subsections).
        format_menutab_fix_numsections_count();

        upgrade_plugin_savepoint(true, 2025111004, 'format', 'menutab');
    }

    if ($oldversion < 2025111005) {
        // Set numsections to 0 for all courses to prevent orphaned sections.
        format_menutab_fix_numsections_count();

        upgrade_plugin_savepoint(true, 2025111005, 'format', 'menutab');
    }

    return true;
}