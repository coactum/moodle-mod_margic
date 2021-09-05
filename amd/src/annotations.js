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
 * Module for the annotation functions of the annotated diary.
 *
 * @module     mod_annotateddiary/annotations
 * @package    mod_annotateddiary
 * @copyright  2021 coactum GmbH
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 define(['jquery'], function($) {
    return {
        init: function() {
            function getSelectionHtml() {
                if (typeof window.getSelection != "undefined") {
                    var selection = window.getSelection().getRangeAt(0);
                } else if (typeof document.selection != "undefined") {
                    if (document.selection.type == "Text") {
                        var selection = document.selection.createRange().htmlText;
                    }
                }
                return selection.cloneContents().textContent;
            }

            function getSelectionCharOffsetWithin(element) {
                var start = 0;
                var end = 0;

                if (typeof window.getSelection != "undefined") {
                    range = window.getSelection().getRangeAt(0);
                    priorRange = range.cloneRange();
                    priorRange.selectNodeContents(element);
                    priorRange.setEnd(range.startContainer, range.startOffset);
                    start = priorRange.toString().length;
                    end = start + range.toString().length;
                } else if (typeof document.selection != "undefined") {
                    if (document.selection.type == "Text") {
                        range = document.selection.createRange();
                        priorRange = document.body.createTextRange();
                        priorRange.moveToElementText(element);
                        priorRange.setEndPoint("EndToStart", range);
                        start = priorRange.text.length;
                        end = start + range.text.length;
                    }
                }

                console.log(priorRange);

                return {
                    start: start,
                    end: end,
                };
            }

            // $(document).bind("mouseup", function() {
            //     var mytext = getSelectionHtml();
            //     console.log(mytext);
            // });

            $(document).on('mouseup', '.originaltext', function() {

                console.log(this);

                var mytext = getSelectionHtml();

                var rangy = window.rangy;

                console.log(rangy);

                console.log(mytext);

                console.log(document.getElementsByClassName('originaltext'));
                console.log(document.getElementsByClassName('originaltext')[0]);
                console.log(document.getElementsByClassName('originaltext')[0].innerHTML);
                console.log(document.getElementsByClassName('originaltext')[0].innerHTML.indexOf(mytext));
                //var position = document.getElementsByClassName('originaltext').indexOf(mytext);
                //console.log(position);

                console.log(getSelectionCharOffsetWithin(this));
            });
        }
    };
});