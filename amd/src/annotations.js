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
 * Module for the annotation functions of the margic.
 *
 * @module     mod_margic/annotations
 * @copyright  2022 coactum GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';
import {removeAllTempHighlights, anchor, describe} from './highlighting';

export const init = (cmid, canmakeannotations, myuserid, focusannotation) => {

    var edited = false;
    var annotations = Array();

    var newannotation = false;

    // Remove col-mds from moodle form.
    $('.annotation-form div.col-md-3').removeClass('col-md-3');
    $('.annotation-form div.col-md-9').removeClass('col-md-9');
    $('.annotation-form div.form-group').removeClass('form-group');
    $('.annotation-form div.row').removeClass('row');

    // Onclick listener if form is canceled.
    $(document).on('click', '#id_cancel', function(e) {
        e.preventDefault();

        removeAllTempHighlights(); // Remove other temporary highlights.

        resetForms(); // Remove old form contents.

        edited = false;
    });

    // Listen for return key pressed to submit annotation form.
    $('textarea').keypress(function(e) {
        if (e.which == 13) {
            $(this).parents(':eq(2)').submit();
            e.preventDefault();
        }
    });

    // If user selects text for new annotation
    $(document).on('mouseup', '.originaltext', function() {
        var selectedrange = window.getSelection().getRangeAt(0);

        if (selectedrange.cloneContents().textContent !== '' && canmakeannotations) {

            removeAllTempHighlights(); // Remove other temporary highlights.

            resetForms(); // Reset the annotation forms.

            // Create new annotation.
            newannotation = createAnnotation(this);

            var entry = this.id.replace(/entry-/, '');

            // RangeSelector.
            $('.annotation-form-' + entry + ' input[name="startcontainer"]').val(
                newannotation.target[0].selector[0].startContainer);
            $('.annotation-form-' + entry + ' input[name="endcontainer"]').val(
                newannotation.target[0].selector[0].endContainer);
            $('.annotation-form-' + entry + ' input[name="startoffset"]').val(
                newannotation.target[0].selector[0].startOffset);
            $('.annotation-form-' + entry + ' input[name="endoffset"]').val(
                newannotation.target[0].selector[0].endOffset);

            // TextPositionSelector.
            $('.annotation-form-' + entry + ' input[name="annotationstart"]').val(
                newannotation.target[0].selector[1].annotationstart);
            $('.annotation-form-' + entry + ' input[name="annotationend"]').val(
                newannotation.target[0].selector[1].annotationend);

            // TextQuoteSelector.
            $('.annotation-form-' + entry + ' input[name="exact"]').val(
                newannotation.target[0].selector[2].exact);
            $('.annotation-form-' + entry + ' input[name="prefix"]').val(
                newannotation.target[0].selector[2].prefix);
            $('.annotation-form-' + entry + ' input[name="suffix"]').val(
                newannotation.target[0].selector[2].suffix);

            $('.annotation-form-' + entry + ' select').val(1);

            // Prevent JavaScript injection (if annotated text in original entry is JavaScript code in script tags).
            $('#annotationpreview-temp-' + entry).html(
                newannotation.target[0].selector[2].exact.replaceAll('<', '&lt;').replaceAll('>', '&gt;'));

            $('.annotationarea-' + entry + ' .annotation-form').show();
            $('.annotation-form-' + entry + ' #id_text').focus();
        }
    });

    // Fetch and recreate annotations.
    $.ajax({
        url: './annotations.php',
        data: {'id': cmid, 'getannotations': 1},
        success: function(response) {
            annotations = JSON.parse(response);
            recreateAnnotations();

            // Highlight annotation and all annotated text if annotated text is hovered
            $('.annotated').mouseenter(function() {
                var id = this.id.replace('annotated-', '');
                $('.annotation-box-' + id).addClass('hovered');
                $('.annotated-' + id).css("background-color", 'lightblue');
            });

            $('.annotated').mouseleave(function() {
                var id = this.id.replace('annotated-', '');
                $('.annotation-box-' + id).removeClass('hovered');
                $('.annotated-' + id).css("background-color", $('.annotated-' + id).css('textDecorationColor'));
            });

            // Highlight whole temp annotation if part of temp annotation is hovered
            $(document).on('mouseover', '.annotated_temp', function() {
                $('.annotated_temp').addClass('hovered');
            });

            $(document).on('mouseleave', '.annotated_temp', function() {
                $('.annotated_temp').removeClass('hovered');
            });

            // Onclick listener for editing annotation.
            $(document).on('click', '.annotated', function() {
                var id = this.id.replace('annotated-', '');
                editAnnotation(id);
            });

            // Onclick listener for editing annotation.
            $(document).on('click', '.edit-annotation', function() {
                var id = this.id.replace('edit-annotation-', '');
                editAnnotation(id);
            });

            // Highlight annotation if hoverannotation button is hovered
            $(document).on('mouseover', '.hoverannotation', function() {
                var id = this.id.replace('hoverannotation-', '');
                $('.annotated-' + id).css("background-color", 'lightblue');
            });

            $(document).on('mouseleave', '.hoverannotation', function() {
                var id = this.id.replace('hoverannotation-', '');
                $('.annotated-' + id).css("background-color", $('.annotated-' + id).css('textDecorationColor'));
            });


            // Focus annotation if needed.
            if (focusannotation != 0) {
                $('.annotated-' + focusannotation).attr('tabindex', -1);
                $('.annotated-' + focusannotation).focus();
            }

        },
        complete: function() {
            $('#overlay').hide();
        },
        error: function() {
            // For output: alert('Error fetching annotations');
        }
    });

    /**
     * Recreate annotations.
     *
     */
    function recreateAnnotations() {

        for (let annotation of Object.values(annotations)) {

            const rangeSelectors = [[
                {type: "RangeSelector", startContainer: annotation.startcontainer, startOffset: parseInt(annotation.startoffset),
                endContainer: annotation.endcontainer, endOffset: parseInt(annotation.endoffset)},
                {type: "TextPositionSelector", annotationstart: parseInt(annotation.annotationstart),
                annotationend: parseInt(annotation.annotationend)},
                {type: "TextQuoteSelector", exact: annotation.exact, prefix: annotation.prefix, suffix: annotation.suffix}
            ]];

            const target = rangeSelectors.map(selectors => ({
                selector: selectors,
            }));

            /** @type {AnnotationData} */
            const newannotation = {
                annotation: annotation,
                target: target,
            };

            anchor(newannotation, $("#entry-" + annotation.entry)[0]);
        }
    }

    /**
     * Edit annotation.
     *
     * @param {int} annotationid
     */
    function editAnnotation(annotationid) {

        if (edited == annotationid) {
            removeAllTempHighlights(); // Remove other temporary highlights.
            resetForms(); // Remove old form contents.
            edited = false;
        } else if (canmakeannotations && myuserid == annotations[annotationid].userid) {
            removeAllTempHighlights(); // Remove other temporary highlights.
            resetForms(); // Remove old form contents.

            edited = annotationid;

            var entry = annotations[annotationid].entry;

            $('.annotation-box-' + annotationid).hide(); // Hide edited annotation-box.

            $('.annotation-form-' + entry + ' input[name="startcontainer"]').val(annotations[annotationid].startcontainer);
            $('.annotation-form-' + entry + ' input[name="endcontainer"]').val(annotations[annotationid].endcontainer);
            $('.annotation-form-' + entry + ' input[name="startoffset"]').val(annotations[annotationid].startoffset);
            $('.annotation-form-' + entry + ' input[name="endoffset"]').val(annotations[annotationid].endoffset);
            $('.annotation-form-' + entry + ' input[name="annotationstart"]').val(annotations[annotationid].annotationstart);
            $('.annotation-form-' + entry + ' input[name="annotationend"]').val(annotations[annotationid].annotationend);
            $('.annotation-form-' + entry + ' input[name="exact"]').val(annotations[annotationid].exact);
            $('.annotation-form-' + entry + ' input[name="prefix"]').val(annotations[annotationid].prefix);
            $('.annotation-form-' + entry + ' input[name="suffix"]').val(annotations[annotationid].suffix);

            $('.annotation-form-' + entry + ' input[name="annotationid"]').val(annotationid);

            $('.annotation-form-' + entry + ' textarea[name="text"]').val(annotations[annotationid].text);

            $('.annotation-form-' + entry + ' select').val(annotations[annotationid].type);

            // Prevent JavaScript injection (if annotated text in original entry is JavaScript code in script tags).
            $('#annotationpreview-temp-' + entry).html(
                annotations[annotationid].exact.replaceAll('<', '&lt;').replaceAll('>', '&gt;'));
            $('#annotationpreview-temp-' + entry).css('border-color', '#' + annotations[annotationid].color);

            $('.annotationarea-' + entry + ' .annotation-form').insertBefore('.annotation-box-' + annotationid);
            $('.annotationarea-' + entry + ' .annotation-form').show();
            $('.annotationarea-' + entry + ' #id_text').focus();
        } else {
            $('.annotation-box-' + annotationid).focus();
        }
    }

    /**
     * Reset all annotation forms
     */
    function resetForms() {
        $('.annotation-form').hide();

        $('.annotation-form input[name^="annotationid"]').val(null);

        $('.annotation-form input[name^="startcontainer"]').val(-1);
        $('.annotation-form input[name^="endcontainer"]').val(-1);
        $('.annotation-form input[name^="startoffset"]').val(-1);
        $('.annotation-form input[name^="endoffset"]').val(-1);

        $('.annotation-form textarea[name^="text"]').val('');

        $('.annotation-box').not('.annotation-form').show(); // To show again edited annotation.
    }
};

/**
 * Create a new annotation that is associated with the selected region of
 * the current document.
 *
 * @param {object} root - The root element
 * @return {object} - The new annotation
 */
function createAnnotation(root) {
    const ranges = [window.getSelection().getRangeAt(0)];

    if (ranges.collapsed) {
        return null;
    }

    const rangeSelectors = ranges.map(range => describe(root, range));

    const target = rangeSelectors.map(selectors => ({
      selector: selectors,
    }));

    /** @type {AnnotationData} */
    const annotation = {
      target,
    };

    anchor(annotation, root);

    return annotation;
}