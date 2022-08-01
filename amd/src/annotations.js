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

 export const init = (annotations, canmakeannotations, myuserid) => {
            // Hide all Moodle forms.
            $('.annotation-form').hide();

            // Remove col-mds from moodle form.
            $('.annotation-form div.col-md-3').removeClass('col-md-3');
            $('.annotation-form div.col-md-9').removeClass('col-md-9');
            $('.annotation-form div.form-group').removeClass('form-group');
            $('.annotation-form div.row').removeClass('row');

            /**
             * Recreate annotations.
             *
             */
            function recreateAnnotations() {
                for (let annotation of Object.values(annotations)) {

                    // Recreate range from db.
                    var newrange = document.createRange();

                    try {
                        newrange.setStart(
                            nodeFromXPath(annotation.startcontainer, $("#entry-" + annotation.entry)[0]), annotation.startposition);
                        newrange.setEnd(
                            nodeFromXPath(annotation.endcontainer, $("#entry-" + annotation.entry)[0]), annotation.endposition);
                     } catch (e) {
                        // eslint-disable-line
                     }

                    var annotatedtext = highlightRange(newrange, annotation.id, 'annotated', annotation.color);

                    if (annotatedtext != '') {
                        $('#annotationpreview-' + annotation.id).html(annotatedtext);
                    }
                }
            }

            /**
             * Edit annotation.
             *
             * @param {int} annotationid
             */
            function editAnnotation(annotationid) {
                if (canmakeannotations && myuserid == annotations[annotationid].userid) {
                    removeAllTempHighlights();
                    resetForms();

                    var entry = annotations[annotationid].entry;

                    $('.annotation-box-' + annotationid).hide(); // Hide edited annotation-box.

                    $('.annotation-form-' + entry + ' input[name="startcontainer"]').val(annotations[annotationid].startcontainer);
                    $('.annotation-form-' + entry + ' input[name="endcontainer"]').val(annotations[annotationid].endcontainer);
                    $('.annotation-form-' + entry + ' input[name="startposition"]').val(annotations[annotationid].startposition);
                    $('.annotation-form-' + entry + ' input[name="endposition"]').val(annotations[annotationid].endposition);

                    $('.annotation-form-' + entry + ' input[name="annotationid"]').val(annotationid);

                    $('.annotation-form-' + entry + ' textarea[name="text"]').val(annotations[annotationid].text);

                    $('.annotation-form-' + entry + ' select').val(annotations[annotationid].type);

                    $('#annotationpreview-temp-' + entry).html($('#annotationpreview-' + annotationid).html());
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
                $('.annotation-form input[name^="startposition"]').val(-1);
                $('.annotation-form input[name^="endposition"]').val(-1);

                $('.annotation-form textarea[name^="text"]').val('');

                $('.annotation-box').not('.annotation-form').show(); // To show again edited annotation.
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
             * @param {int} annotationid - ID of annotation
             * @param {string} cssClass - A CSS class to use for the highlight
             * @param {string} color - Color of the highlighting
             * @return {HighlightElement[]} - Elements wrapping text in `normedRange` to add a highlight effect
             */
            function highlightRange(range, annotationid = false, cssClass = 'annotated', color = 'FFFF00') {

                const textNodes = wholeTextNodesInRange(range);

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

                // Wrap each text node span with a `<span>` element.
                var hihglightedtext = '';

                textNodeSpans.forEach(nodes => {
                    const highlightEl = document.createElement('span');
                    highlightEl.className = cssClass;

                    if (annotationid) {
                        highlightEl.className += ' ' + cssClass + '-' + annotationid;
                        // highlightEl.tabIndex = 1;
                        highlightEl.id = cssClass + '-' + annotationid;
                        highlightEl.style.backgroundColor = '#' + color;
                    }

                    hihglightedtext += nodes[0].textContent;

                    nodes[0].parentNode.replaceChild(highlightEl, nodes[0]);
                    nodes.forEach(node => highlightEl.appendChild(node));

                });

                return hihglightedtext;
            }

            /**
             * Returns true if any part of `node` lies within `range`.
             *
             * @param {Range} range
             * @param {Node} node
             * @return {bool} - If node is in range
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
             * Get the node name for use in generating an xpath expression.
             *
             * @param {Node} node
             * @return {string} - Name of the node
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
             * @return {int} - Position of the node
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

            /**
             * Get the path segments to the node
             *
             * @param {Node} node
             * @return {array} - Path segments
             */
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
             * @return {string} - The xpath of a node
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
             * @return {Element|null} - The child element or null
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

                        // The `namespaceResolver` and `result` arguments are optional in the spec
                        // but required in Edge Legacy.
                        null /* NamespaceResolver */,
                        XPathResult.FIRST_ORDERED_NODE_TYPE,
                        null /* Result */
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
             */
            function removeAllTempHighlights() {
                const highlights = Array.from($('body')[0].querySelectorAll('.annotated_temp'));
                if (highlights !== undefined && highlights.length != 0) {
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
                        var pn = highlights[i].parentNode;
                        const children = Array.from(highlights[i].childNodes);
                        replaceWith(highlights[i], children);
                        pn.normalize();
                    }
                }
            }

            // If user selects text for new annotation
            $(document).on('mouseup', '.originaltext', function() {
                var selectedrange = window.getSelection().getRangeAt(0);

                if (selectedrange.cloneContents().textContent !== '' && canmakeannotations) {

                    removeAllTempHighlights(); // Remove other temporary highlights.

                    resetForms(); // Remove old form contents.

                    var entry = this.id.replace(/entry-/, '');

                    $('.annotation-form-' + entry + ' input[name="startcontainer"]').val(
                        xpathFromNode(selectedrange.startContainer, this));
                    $('.annotation-form-' + entry + ' input[name="endcontainer"]').val(
                        xpathFromNode(selectedrange.endContainer, this));
                    $('.annotation-form-' + entry + ' input[name="startposition"]').val(selectedrange.startOffset);
                    $('.annotation-form-' + entry + ' input[name="endposition"]').val(selectedrange.endOffset);

                    $('.annotation-form-' + entry + ' select').val(1);

                    var annotatedtext = highlightRange(selectedrange, false, 'annotated_temp');

                    if (annotatedtext != '') {
                        $('#annotationpreview-temp-' + entry).html(annotatedtext);
                    }

                    $('.annotationarea-' + entry + ' .annotation-form').show();
                    $('.annotation-form-' + entry + ' #id_text').focus();
                }
            });

            recreateAnnotations();

            // Highlight annotation and all annotated text if annotated text is hovered
            $('.annotated').mouseenter(function() {
                var id = this.id.replace('annotated-', '');
                $('.annotationpreview-' + id).addClass('hovered');
                $('.annotated-' + id).addClass('hovered');
                $('.annotation-box-' + id + ' .errortype').addClass('hovered');

            });

            $('.annotated').mouseleave(function() {
                var id = this.id.replace('annotated-', '');
                $('.annotationpreview-' + id).removeClass('hovered');
                $('.annotated-' + id).removeClass('hovered');
                $('.annotation-box-' + id + ' .errortype').removeClass('hovered');
            });

            // Highlight annotated text if annotationpreview is hovered
            $('.annotatedtextpreview').mouseenter(function() {
                var id = this.id.replace('annotationpreview-', '');
                $('.annotated-' + id).addClass('hovered');
            });

            $('.annotatedtextpreview').mouseleave(function() {
                var id = this.id.replace('annotationpreview-', '');
                $('.annotated-' + id).removeClass('hovered');
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

            // Onclick listener for click on annotation-box.
            // $(document).on('click', '.annotation-box', function() {
            //     var id = this.id.replace('annotation-box-', '');
            //     $('#annotated-' + id).focus();
            // });

            // onclick listener if form is canceled
            $(document).on('click', '#id_cancel', function(e) {
                e.preventDefault();

                removeAllTempHighlights(); // Remove other temporary highlights.

                resetForms(); // Remove old form contents.
            });

            // Listen for return key pressed to submit annotation form.
            $('textarea').keypress(function(e) {
                if (e.which == 13) {
                    $(this).parents(':eq(2)').submit();
                    e.preventDefault();
                }
              });

};