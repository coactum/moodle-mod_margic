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
        init: function(annotations, canmakeannotations) {

            // Hide all Moodle forms
            $('.annotation-form').hide();

            // remove col-mds from moodle form
            $('.annotation-form div.col-md-3').removeClass('col-md-3');
            $('.annotation-form div.col-md-9').removeClass('col-md-9');
            $('.annotation-form div.form-group').removeClass('form-group');
            $('.annotation-form div.row').removeClass('row');

            function recreateAnnotations(){
                for (let annotation of Object.values(annotations)) {

                    //console.log($( "#entry-" + annotation.entry)[0]);

                    //recreate range from db
                    var newrange = document.createRange();

                    try {
                        newrange.setStart(nodeFromXPath(annotation.startcontainer, $( "#entry-" + annotation.entry)[0]), annotation.startposition);
                        newrange.setEnd(nodeFromXPath(annotation.endcontainer, $( "#entry-" + annotation.entry)[0]), annotation.endposition);
                     }
                     catch (e) {
                        //console.log('try/catch');
                        //console.log(e);
                     }

                    //console.log('recreateAnnotations after setStart/End');

                    highlightRange(newrange, annotation.id);

                }
            }

            function editAnnotation(annotationid) {
                if (canmakeannotations) {
                    removeAllTempHighlights();
                    resetForms();

                    //console.log('annotations from js');
                    //console.log(annotations);

                    var entry = annotations[annotationid].entry;

                    //console.log(entry);

                    $('input[name="startcontainer[' + entry + ']"]').val(annotations[annotationid].startcontainer);
                    $('input[name="endcontainer[' + entry + ']"]').val(annotations[annotationid].endcontainer);
                    $('input[name="startposition[' + entry + ']"]').val(annotations[annotationid].startposition);
                    $('input[name="endposition[' + entry + ']"]').val(annotations[annotationid].endposition);

                    $('input[name="annotationid[' + entry + ']"]').val(annotationid);

                    $('textarea[name="text[' + entry + ']"]').val(annotations[annotationid].text);

                    $('.annotationarea-' + entry + ' .annotation-form').show();
                    $('#id_text_' + entry).focus();
                }
            }

            // function deleteAnnotation(annotationid) {

            //     //console.log('delete annotation');
            //     //console.log(annotationid);

            //     // removeAllTempHighlights();
            //     // resetForms();

            //     // var entry = annotations[annotationid].entry;

            //     // console.log(entry);

            //     // $('input[name="startcontainer[' + entry + ']"]').val(annotations[annotationid].startcontainer);
            //     // $('input[name="endcontainer[' + entry + ']"]').val(annotations[annotationid].endcontainer);
            //     // $('input[name="startposition[' + entry + ']"]').val(annotations[annotationid].startposition);
            //     // $('input[name="endposition[' + entry + ']"]').val(annotations[annotationid].endposition);

            //     // $('textarea[name="text[' + entry + ']"]').val(annotations[annotationid].text);

            //     // $('.annotationarea-' + entry + ' .annotation-form').show();
            //     // $('#id_text_' + entry).focus();
            // }

            function resetForms(){
                $('.annotation-form').hide();

                $('.annotation-form input[name^="annotationid"]').val(null);

                $('.annotation-form input[name^="startcontainer"]').val(-1);
                $('.annotation-form input[name^="endcontainer"]').val(-1);
                $('.annotation-form input[name^="startposition"]').val(-1);
                $('.annotation-form input[name^="endposition"]').val(-1);

                $('.annotation-form textarea[name^="text"]').val('');
            }

            /**
             * Return text nodes which are entirely inside `range`.
             *
             * If a range starts or ends part-way through a text node, the node is split
             * and the part inside the range is returned.
             *
             * @param {Range} range
             * @return {Text[]}
             */
            function wholeTextNodesInRange(range) {
                if (range.collapsed) {
                    // Exit early for an empty range to avoid an edge case that breaks the algorithm
                    // below. Splitting a text node at the start of an empty range can leave the
                    // range ending in the left part rather than the right part.
                    return [];
                }

                /** @type {Node|null} */
                let root = range.commonAncestorContainer;
                if (root.nodeType !== Node.ELEMENT_NODE) {
                    // If the common ancestor is not an element, set it to the parent element to
                    // ensure that the loop below visits any text nodes generated by splitting
                    // the common ancestor.
                    //
                    // Note that `parentElement` may be `null`.
                    root = root.parentElement;
                }
                if (!root) {
                    // If there is no root element then we won't be able to insert highlights,
                    // so exit here.
                    return [];
                }

                const textNodes = [];
                const nodeIter = /** @type {Document} */ (
                root.ownerDocument
                ).createNodeIterator(
                root,
                NodeFilter.SHOW_TEXT // Only return `Text` nodes.
                );
                let node;
                while ((node = nodeIter.nextNode())) {
                    if (!isNodeInRange(range, node)) {
                        continue;
                    }
                    let text = /** @type {Text} */ (node);

                    if (text === range.startContainer && range.startOffset > 0) {
                        // Split `text` where the range starts. The split will create a new `Text`
                        // node which will be in the range and will be visited in the next loop iteration.
                        text.splitText(range.startOffset);
                        continue;
                    }

                    if (text === range.endContainer && range.endOffset < text.data.length) {
                        // Split `text` where the range ends, leaving it as the part in the range.
                        text.splitText(range.endOffset);
                    }

                    textNodes.push(text);
                }

                return textNodes;
            }

            /**
             * Wraps the DOM Nodes within the provided range with a highlight
             * element of the specified class and returns the highlight Elements.
             *
             * @param {Range} range - Range to be highlighted
             * @param {string} cssClass - A CSS class to use for the highlight
             * @return {HighlightElement[]} - Elements wrapping text in `normedRange` to add a highlight effect
             */
            function highlightRange(range, annotationid = false, cssClass = 'annotated') {

                //console.log('highlightRange start');

                const textNodes = wholeTextNodesInRange(range);

                // Check if this range refers to a placeholder for not-yet-rendered content in
                // a PDF. These highlights should be invisible.
                const inPlaceholder = textNodes.length > 0 && isInPlaceholder(textNodes[0]);

                // Group text nodes into spans of adjacent nodes. If a group of text nodes are
                // adjacent, we only need to create one highlight element for the group.
                let textNodeSpans = [];
                let prevNode = null;
                let currentSpan = null;

                textNodes.forEach(node => {
                if (prevNode && prevNode.nextSibling === node) {
                    currentSpan.push(node);
                } else {
                    currentSpan = [node];
                    textNodeSpans.push(currentSpan);
                }
                prevNode = node;
                });

                // Filter out text node spans that consist only of white space. This avoids
                // inserting highlight elements in places that can only contain a restricted
                // subset of nodes such as table rows and lists.
                const whitespace = /^\s*$/;
                textNodeSpans = textNodeSpans.filter(span =>
                // Check for at least one text node with non-space content.
                span.some(node => !whitespace.test(node.nodeValue))
                );

                // Wrap each text node span with a `<hypothesis-highlight>` element.
                const highlights = [];
                textNodeSpans.forEach(nodes => {
                // A custom element name is used here rather than `<span>` to reduce the
                // likelihood of highlights being hidden by page styling.

                /** @type {HighlightElement} */
                const highlightEl = document.createElement('span');
                highlightEl.className = cssClass;

                if (annotationid) {
                    highlightEl.className += ' ' + cssClass + '-' + annotationid;
                    highlightEl.id = cssClass + '-' + annotationid;
                }

                nodes[0].parentNode.replaceChild(highlightEl, nodes[0]);
                nodes.forEach(node => highlightEl.appendChild(node));

                highlights.push(highlightEl);
                });

                //console.log('highlightRange end');


                return highlights;
            }

            /**
             * Returns true if any part of `node` lies within `range`.
             *
             * @param {Range} range
             * @param {Node} node
             */
            function isNodeInRange(range, node) {
                try {
                    const length = node.nodeValue?.length ?? node.childNodes.length;
                    return (
                        // Check start of node is before end of range.
                        range.comparePoint(node, 0) <= 0 &&
                        // Check end of node is after start of range.
                        range.comparePoint(node, length) >= 0
                    );
                } catch (e) {
                    // `comparePoint` may fail if the `range` and `node` do not share a common
                    // ancestor or `node` is a doctype.
                return false;
                }
            }

            /**
             * CSS selector that will match the placeholder within a page/tile container.
             */
            const placeholderSelector = '.annotator-placeholder';

            /**
             * Return true if `node` is inside a placeholder element created with `createPlaceholder`.
             *
             * This is typically used to test if a highlight element associated with an
             * anchor is inside a placeholder.
             *
             * @param {Node} node
             */
            function isInPlaceholder(node) {
                if (!node.parentElement) {
                    return false;
                }
                return node.parentElement.closest(placeholderSelector) !== null;
            }





            /**
             * Get the node name for use in generating an xpath expression.
             *
             * @param {Node} node
             */
            function getNodeName(node) {
                const nodeName = node.nodeName.toLowerCase();
                let result = nodeName;
                if (nodeName === '#text') {
                    result = 'text()';
                }
                return result;
            }

            /**
             * Get the index of the node as it appears in its parent's child list
             *
             * @param {Node} node
             */
            function getNodePosition(node) {
                let pos = 0;
                /** @type {Node|null} */
                let tmp = node;
                while (tmp) {
                    if (tmp.nodeName === node.nodeName) {
                        pos += 1;
                    }
                    tmp = tmp.previousSibling;
                    }
                return pos;
            }

            function getPathSegment(node) {
                const name = getNodeName(node);
                const pos = getNodePosition(node);
                return `${name}[${pos}]`;
            }

            /**
             * A simple XPath generator which can generate XPaths of the form
             * /tag[index]/tag[index].
             *
             * @param {Node} node - The node to generate a path to
             * @param {Node} root - Root node to which the returned path is relative
             */
            function xpathFromNode(node, root) {
                let xpath = '';

                /** @type {Node|null} */
                let elem = node;
                while (elem !== root) {
                    if (!elem) {
                        throw new Error('Node is not a descendant of root');
                    }
                    xpath = getPathSegment(elem) + '/' + xpath;
                    elem = elem.parentNode;
                }
                xpath = '/' + xpath;
                xpath = xpath.replace(/\/$/, ''); // Remove trailing slash

                return xpath;
            }

            /**
             * Return the `index`'th immediate child of `element` whose tag name is
             * `nodeName` (case insensitive).
             *
             * @param {Element} element
             * @param {string} nodeName
             * @param {number} index
             */
            function nthChildOfType(element, nodeName, index) {
                nodeName = nodeName.toUpperCase();

                let matchIndex = -1;
                for (let i = 0; i < element.children.length; i++) {
                const child = element.children[i];
                if (child.nodeName.toUpperCase() === nodeName) {
                    ++matchIndex;
                    if (matchIndex === index) {
                    return child;
                    }
                }
                }

                return null;
            }

            /**
             * Evaluate a _simple XPath_ relative to a `root` element and return the
             * matching element.
             *
             * A _simple XPath_ is a sequence of one or more `/tagName[index]` strings.
             *
             * Unlike `document.evaluate` this function:
             *
             *  - Only supports simple XPaths
             *  - Is not affected by the document's _type_ (HTML or XML/XHTML)
             *  - Ignores element namespaces when matching element names in the XPath against
             *    elements in the DOM tree
             *  - Is case insensitive for all elements, not just HTML elements
             *
             * The matching element is returned or `null` if no such element is found.
             * An error is thrown if `xpath` is not a simple XPath.
             *
             * @param {string} xpath
             * @param {Element} root
             * @return {Element|null}
             */
            function evaluateSimpleXPath(xpath, root) {
                const isSimpleXPath = xpath.match(/^(\/[A-Za-z0-9-]+(\[[0-9]+\])?)+$/) !== null;
                if (!isSimpleXPath) {
                    throw new Error('Expression is not a simple XPath');
                }

                const segments = xpath.split('/');
                let element = root;

                // Remove leading empty segment. The regex above validates that the XPath
                // has at least two segments, with the first being empty and the others non-empty.
                segments.shift();

                for (let segment of segments) {
                    let elementName;
                    let elementIndex;

                    const separatorPos = segment.indexOf('[');
                    if (separatorPos !== -1) {
                        elementName = segment.slice(0, separatorPos);

                        const indexStr = segment.slice(separatorPos + 1, segment.indexOf(']'));
                        elementIndex = parseInt(indexStr) - 1;
                        if (elementIndex < 0) {
                        return null;
                        }
                    } else {
                        elementName = segment;
                        elementIndex = 0;
                    }

                    const child = nthChildOfType(element, elementName, elementIndex);
                    if (!child) {
                        return null;
                    }

                    element = child;
                }

                return element;
            }

            /**
             * Finds an element node using an XPath relative to `root`
             *
             * Example:
             *   node = nodeFromXPath('/main/article[1]/p[3]', document.body)
             *
             * @param {string} xpath
             * @param {Element} [root]
             * @return {Node|null}
             */
            function nodeFromXPath(xpath, root = document.body) {
                try {
                    return evaluateSimpleXPath(xpath, root);
                } catch (err) {
                    return document.evaluate(
                        '.' + xpath,
                        root,

                        // nb. The `namespaceResolver` and `result` arguments are optional in the spec
                        // but required in Edge Legacy.
                        null /* namespaceResolver */,
                        XPathResult.FIRST_ORDERED_NODE_TYPE,
                        null /* result */
                    ).singleNodeValue;
                }
            }

            /**
             * Replace a child `node` with `replacements`.
             *
             * nb. This is like `ChildNode.replaceWith` but it works in older browsers.
             *
             * @param {ChildNode} node
             * @param {Node[]} replacements
             */
            function replaceWith(node, replacements) {
                const parent = /** @type {Node} */ (node.parentNode);

                replacements.forEach(r => parent.insertBefore(r, node));
                node.remove();
            }

            /**
             * Remove all temporary highlights under a given root element.
             *
             * @param {HTMLElement} root
             */
            function removeAllTempHighlights() {
                const highlights = Array.from($('body')[0].querySelectorAll('.annotated_temp'));
                if (highlights !== undefined && highlights.length != 0){
                    removeHighlights(highlights);
                }
            }

            /**
             * Remove highlights from a range previously highlighted with `highlightRange`.
             *
             * @param {HighlightElement[]} highlights - The highlight elements returned by `highlightRange`
             */
            function removeHighlights(highlights) {
                for (var i = 0; i < highlights.length; i++) {
                    if (highlights[i].parentNode) {
                        const children = Array.from(highlights[i].childNodes);
                        replaceWith(highlights[i], children);
                    }
                }
            }


            // If user selects text for new annotation
            $(document).on('mouseup', '.originaltext', function() {
                var selectedrange = window.getSelection().getRangeAt(0);

                // console.log('i should make annotation');

                // console.log('window.getSelection()');
                // console.log(window.getSelection());
                // console.log('selectedrange');
                // console.log(selectedrange);

                if (selectedrange.cloneContents().textContent !== '' && canmakeannotations) {
                    removeAllTempHighlights(); // remove other temporary highlights

                    resetForms(); // remove old form contents

                    //console.log('i should make concrete annotation');

                    // console.log(addertoolbar);
                    // console.log(addertoolbar.buildAdderToolbar());

                    var entry = this.id.replace(/entry-/, '');

                    //getSelectionValues(entry);

                    //createAnnotation(entry);
                    //console.log('selectedrange.cloneContents().textContent');
                    //console.log(selectedrange.cloneContents().textContent);

                    $('input[name="startcontainer[' + entry + ']"]').val(xpathFromNode(selectedrange.startContainer, this));
                    $('input[name="endcontainer[' + entry + ']"]').val(xpathFromNode(selectedrange.endContainer, this));
                    $('input[name="startposition[' + entry + ']"]').val(selectedrange.startOffset);
                    $('input[name="endposition[' + entry + ']"]').val(selectedrange.endOffset);

                    highlightRange(selectedrange, false, 'annotated_temp');

                    $('.annotationarea-' + entry + ' .annotation-form').show();
                    $('#id_text_' + entry).focus();
                }
            });

            recreateAnnotations();

            // Highlight annotation and all annotated text if annotated text is hovered
            $('.annotated').mouseenter (function() {
                var id = this.id.replace('annotated-', '');
                $('.annotation-'+id).addClass('hovered');
                $('.annotated-'+id).addClass('hovered');
            });

            $('.annotated').mouseleave (function() {
                var id = this.id.replace('annotated-', '');
                $('.annotation-'+id).removeClass('hovered');
                $('.annotated-'+id).removeClass('hovered');
            });

            // Highlight annotated text if annotation is hovered
            $('.annotation').mouseenter (function() {
                var id = this.id.replace('annotation-', '');
                $('.annotated-'+id).addClass('hovered');
            });

            $('.annotation').mouseleave (function() {
                var id = this.id.replace('annotation-', '');
                $('.annotated-'+id).removeClass('hovered');
            });

            // Highlight whole temp annotation if part of temp annotation is hovered
            $(document).on('mouseover', '.annotated_temp', function(){
                $('.annotated_temp').addClass('hovered');
            });

            $(document).on('mouseleave', '.annotated_temp', function(){
                $('.annotated_temp').removeClass('hovered');
            });

            // onclick listener for editing annotation
            $(document).on('click', '.annotated', function(){
                //console.log('annotated text is clicked');
                //console.log(this);
                var id = this.id.replace('annotated-', '');
                editAnnotation(id);
            });

            // onclick listener for editing annotation
            $(document).on('click', '.edit-annotation', function(){
                //console.log('edit annotation button clicked');
                //console.log(this);
                var id = this.id.replace('edit-annotation-', '');
                editAnnotation(id);
            });

            // onclick listener for deleting annotation
            // $(document).on('click', '.delete-annotation', function(){
            //     //console.log('delete annotation button clicked');
            //     //console.log(this);
            //     var id = this.id.replace('delete-annotation-', '');
            //     deleteAnnotation(id);
            // });

            // onclick listener if form is canceled
            $(document).on('click', '#id_cancel', function(e){
                e.preventDefault();

                //console.log('form is canceled');
                //console.log(e);

                removeAllTempHighlights(); // remove other temporary highlights

                resetForms(); // remove old form contents
            });

        }
    };
});