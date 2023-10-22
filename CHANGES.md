## Changelog ##

- [1.3.1]:
    - Ensured compatibility with Moodle 4.3.
        - Changed code to comply with new moodle coding standards.
    - [Bugfix]: Changed default error type template colors for new installations to improve contrast.

- [1.3.0]:
    - [Bugfix]: Fixed a bug that prevented the order of Margic error types (that were subsequently added to an instance from an error type template) from being changed under certain conditions.
    - [Bugfix]: Deleting error types now triggers a confirm prompt.
    - [Bugfix]: Removed doubled triggering of the download_margic_entries event.
    - [Feature]: Added a color picker for creating error types and templates.
    - [Improvement]: The default value for the feedback notification for the entries can now be set. Administrators can set the default value in the admin settings. This is then taken as the default value when a new margic is created, but can be changed there by teachers for the entire margic. Of course, teachers can deviate from the default value for each grading in the actual grading form. If the admin does not change the default value, it remains true as it was until now.
    - [Improvement]: You can now prevent the displaying of timestamps for entries, annotations and feedback. There are now three new capabilities: "viewotherusersentrytimes" determines whether a user sees when an entry made by other users was created. "viewotherusersannotationtimes" determines whether a user can see when annotations were created by other users. "viewotherusersfeedbacktimes" determines whether a user can see when other teachers have given feedback on an entry. All three capabilities are activated for all users by default, but you can now withdraw these permission for individual roles (e.g. if you do not want the participants to see the times at which the teachers create their annotations or when they give feedback).
    - [Improvement]: You can now define for each Margic if teachers can overwrite and delete the annotations made by other teachers.
    - [Improvement]: If you save feedback or grading the page now jumps to the changed feedback after if is saved.
    - [Improvement]: Annotation button now in a different color when annotation mode is activated.
	- [Change]: Removed the link to index.php in the course navigation (use the course block instead).

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