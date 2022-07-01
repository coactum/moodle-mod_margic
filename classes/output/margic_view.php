<?php
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

/**
 * Class containing data for margic main page
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace mod_margic\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

/**
 * Class containing data for margic_view
 *
 * @package     mod_margic
 * @copyright   2022 coactum GmbH
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class margic_view implements renderable, templatable {

    /** @var object */
    protected $cm;
    /** @var int */
    protected $cmid;
    /** @var object */
    protected $entries;
    /** @var string */
    protected $sortmode;
    /** @var string */
    protected $entrybgc;
    /** @var string */
    protected $entrytextbgc;
    /** @var bool */
    protected $caneditentries;
    /** @var int */
    protected $edittimeends;
    /** @var bool */
    protected $canmanageentries;
    /** @var string */
    protected $sesskey;
    /** @var string */
    protected $currentuserrating;
    /** @var int */
    protected $ratingaggregationmode;
    /** @var int */
    protected $courseid;
    /** @var int */
    protected $singleuser;
    /** @var int */
    protected $pagecountoptions;
    /** @var array */
    protected $pagebar;
    /** @var int */
    protected $entriescount;
    /** @var bool */
    protected $annotationmode;
    /** @var bool */
    protected $canmakeannotations;
    /** @var object */
    protected $annotationtypes;
    /**
     * Construct this renderable.
     * @param int $cmid The course module id
     * @param array $entries The accessible entries for the margic instance
     * @param string $sortmode Sort mode for the margic instance
     * @param string $entrybgc Background color of the entries
     * @param string $entrytextbgc Background color of the texts in the entries
     * @param bool $caneditentries If own entries can be edited
     * @param int $edittimeends Time when entries cant be edited anymore
     * @param bool $edittimehasended If edit time has ended
     * @param bool $canmanageentries If entries can be managed
     * @param string $sesskey The session key
     * @param string $currentuserrating The rating of the current user viewing the page
     * @param string $ratingaggregationmode The mode of the aggregated grades
     * @param int $courseid The course id for getting the user pictures
     * @param int $singleuser If only entries of one user are displayed
     * @param array $pagecountoptions Options for the pagecount select
     * @param array $pagebar Array with the bpages for the pagebar
     * @param int $entriescount The amount of all entries
     * @param bool $annotationmode If annotation mode is set
     * @param bool $canmakeannotations If user can make annotations
     * @param array $annotationtypes Array with annotation types for form
     */
    public function __construct($cm, $entries, $sortmode, $entrybgc, $entrytextbgc, $caneditentries, $edittimeends, $edittimehasended, $canmanageentries,
        $sesskey, $currentuserrating, $ratingaggregationmode, $courseid, $singleuser, $pagecountoptions, $pagebar, $entriescount, $annotationmode, $canmakeannotations, $annotationtypes) {

        $this->cm = $cm;
        $this->cmid = $this->cm->id;
        $this->entries = $entries;
        $this->sortmode = $sortmode;
        $this->entrybgc = $entrybgc;
        $this->entrytextbgc = $entrytextbgc;
        $this->caneditentries = $caneditentries;
        $this->edittimeends = $edittimeends;
        $this->edittimehasended = $edittimehasended;
        $this->canmanageentries = $canmanageentries;
        $this->sesskey = $sesskey;
        $this->currentuserrating = $currentuserrating;
        $this->ratingaggregationmode = $ratingaggregationmode;
        $this->courseid = $courseid;
        $this->singleuser = $singleuser;
        $this->pagecountoptions = $pagecountoptions;
        $this->pagebar = $pagebar;
        $this->entriescount = $entriescount;
        $this->annotationmode = $annotationmode;
        $this->canmakeannotations = $canmakeannotations;
        $this->annotationtypes = $annotationtypes;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output Renderer base.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $data = new stdClass();
        $data->cmid = $this->cmid;

        global $OUTPUT, $DB, $USER, $CFG;

        require_once($CFG->dirroot . '/mod/margic/annotation_form.php');

        if ($this->entries) {

            foreach ($this->entries as $key => $entry) {
                if ($this->canmanageentries) {
                    $this->entries[$key]->user->userpicture = $OUTPUT->user_picture($entry->user, array('courseid' => $this->courseid, 'link' => true, 'includefullname' => true));
                }

                if ($entry->teacher) {
                    $teacher = $DB->get_record('user', array('id' => $entry->teacher));
                    $teacherimage = $OUTPUT->user_picture($teacher, array('courseid' => $this->courseid, 'link' => true, 'includefullname' => true));

                    if ($this->canmanageentries) {
                        $replace = str_replace('<span class="teacherpicture m-l-1">', '<br><span class="teacherpicture m-l-1">' .  $teacherimage . ' - ', $entry->gradingform);
                    } else {
                        $replace = str_replace('<span class="teacherpicture"></span>', '<span class="teacherpicture">' .  $teacherimage, $entry->gradingform);
                    }

                    $this->entries[$key]->gradingform = $replace;
                }

                if ($this->annotationmode) {

                    $mform = new \annotation_form(new \moodle_url('/mod/margic/annotations.php', array('id' => $this->cmid)), array('types' => $this->annotationtypes));

                    // Set default data.
                    $mform->set_data(array('id' => $this->cmid, 'entry' => $entry->id));

                    $this->entries[$key]->annotationform = $mform->render();

                    foreach ($this->entries[$key]->annotations as $anr => $annotation) {
                        $annotater = $DB->get_record('user', array('id' => $annotation->userid));
                        $annotaterimage = $OUTPUT->user_picture($annotater, array('courseid' => $this->courseid, 'link' => true, 'includefullname' => true, 'size' => 20));

                        $this->entries[$key]->annotations[$anr]->userpicturestr = $annotaterimage;
                    }

                } else {
                    $this->entries[$key]->annotationform = false;
                }
            }
        }

        $data->entries = $this->entries;
        $data->sortmode = $this->sortmode;
        $data->entrybgc = $this->entrybgc;
        $data->entrytextbgc = $this->entrytextbgc;
        $data->caneditentries = $this->caneditentries;
        $data->edittimeends = $this->edittimeends;
        $data->edittimehasended = $this->edittimehasended;
        $data->canmanageentries = $this->canmanageentries;
        $data->sesskey = $this->sesskey;
        $data->currentuserrating = $this->currentuserrating;
        $data->ratingaggregationmode = $this->ratingaggregationmode;
        $data->singleuser = $this->singleuser;
        $data->pagecountoptions = $this->pagecountoptions;
        $data->pagebar = $this->pagebar;
        $data->entriescount = $this->entriescount;
        $data->annotationmode = $this->annotationmode;
        $data->canmakeannotations = $this->canmakeannotations;
        return $data;
    }
}
