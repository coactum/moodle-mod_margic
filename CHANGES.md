## Changelog ##
- [1.2.9]:
    - Ensured compatibility with Moodle 4.2.
        - [Layout]: Minor layout fixes because of the new versions of the bootstrap and fontawesome libraries.
        - [Icon]: Added monologo version of the activity icon for current Moodle versions.
        - [Bugfix]: Minor code changes for new php version.
        - [Bugfix]: Removed deprecated legacy function from some events.
    - [Bugfix]: Cancel button on edit.php now working as intended.
    - [Bugfix]: Now hiding the edit and delete annotation buttons for annotations shown on edit.php.
    - [Bugfix]: Fixed a bug that the overview page of a margic was not displayed if it has been restored and a teacher who has graded an entry there does not exist anymore.
    - [Bugfix]: When editing an existing entry where the date can be set manually, the current date is now pre-filled. Also, the date set for the edited version must be newer than the date of the original entry.
    - [Bugfix]: Small adjustment to the print view of the overview page: Removing unnecessary margin at the top of the page. Also the background colors of the annotations are now always printed in all browsers.

    - tl;dr: The update ensures compatibility of the Margic plugin with Moodle 4.2 and contains a few minor fixes for existing bugs. It should be installed when updating your moodle to 4.2, otherwise its recommended but optional.

- [1.2.8]:
    - [Bugfix]: Renamed start and end field in table margic_annotations because those names are forbidden in postgreSQL databases.
    - [Bugfix]: Fixed wrong default value in line 43 edit.php to prevent an error in moodle installations using postgreSQL databases.
    - [Bugfix]: Changed db query in helper.php line 234 to prevent error in moodle installations with postgreSQL databases.

- [1.2.7]:
    - [Chore]: Renamed some classes for compatibility with moodle coding guidelines.
    - [Chore]: Default values for background colors in the plugin settings are not stored in language strings anymore.
    - [Bugfix]: Changed date handling while creating the download file with the entries to support cross-database usage of the plugin.

- [1.2.6]:
    - [Bugfix]: Fix for activity completion for view event.
    - [Chore]: Some minor changes to reduce moodle validation errors.

- [1.2.5]:
    - [Bugfix]: Added LICENSE.md
    - [Bugfix]: In print mode now the background of the grading form is also greyed out.
    - [Bugfix]: Symbol for deleting default errortyp templates is now hidden for teachers on error_summary as they cant ever delete the default templates.
    - [Chore]: Some changes for more compatibility with moodle coding guidelines.
    - [Icon]: Reworked activity icon. It also now has purpose COLLABORATION in Moodle 4.0 and above.
    - [Layout]: Heading and intro are not displayed on Moodle 4.0 and above to comply with the new moodle page layout.
    - [Improvement]: After editing or creating an annotation the annotation is now focused after the page reload.

- [1.2.4]: Initial moodle release.