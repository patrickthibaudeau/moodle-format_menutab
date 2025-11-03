# Menu/Tab #

This plugin displays your course in a grid format. Furthermore, it allows you to create tabs
within each section using Moodle 5's native subsection feature.

## Version 3.0.0 - Major Update for Moodle 5 ##

**Important Changes:**
- **Subsection-based tabs**: Tabs are now created using Moodle 5's native subsection feature instead of labels with `<h2>` tags.
- **Automatic migration**: When upgrading from version 2.x to 3.0, existing courses will automatically migrate labels with `<h2>` tags to subsections. The original labels will be deleted, and activities will be moved to the appropriate subsections.
- **Enhanced compatibility**: This approach aligns with Moodle 5's core architecture and provides better maintainability.

## Creating Tabs ##

To create tabs within a section:
1. Navigate to your course and turn editing on.
2. In the section where you want tabs, add a subsection (use the "Add subsection" option).
3. Give the subsection a name - this will become the tab title.
4. Add your activities/resources to the subsection.
5. Repeat for additional tabs.

Each subsection within a parent section will appear as a separate tab when viewing the course.


## Installing via uploaded ZIP file ##

1. Log in to your Moodle site as an admin and go to _Site administration >
   Plugins > Install plugins_.
2. Upload the ZIP file with the plugin code. You should only be prompted to add
   extra details if your plugin type is not automatically detected.
3. Check the plugin validation report and finish the installation.

## Installing manually ##

The plugin can be also installed by putting the contents of this directory to

    {your/moodle/dirroot}/course/format/menutab

Afterwards, log in to your Moodle site as an admin and go to _Site administration >
Notifications_ to complete the installation.

Alternatively, you can run

    $ php admin/cli/upgrade.php

to complete the installation from the command line.

## Migrating from Version 2.x ##

If you are upgrading from version 2.x:
- The upgrade will automatically convert any labels containing `<h2>` tags into subsections.
- Activities that were grouped under these labels will be moved to the new subsections.
- The original label activities will be deleted after migration.
- This process runs automatically during the plugin upgrade.
- No manual intervention is required.

**Backup Recommendation**: As with any major upgrade, it's recommended to backup your course before upgrading.

## License ##

2022 UIT Innovation  <thibaud@yorku.ca>

This program is free software: you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation, either version 3 of the License, or (at your option) any later
version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with
this program.  If not, see <https://www.gnu.org/licenses/>.
