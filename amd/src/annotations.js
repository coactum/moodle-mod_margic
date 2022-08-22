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
import {RangeAnchor, TextPositionAnchor, TextQuoteAnchor} from './types';
import {TextRange} from './text-range';

export const init = (cmid, canmakeannotations, myuserid) => {

    var edited = false;
    var annotations = Array();

    var newannotation = false;

    // Hide all Moodle forms.
    $('.annotation-form').hide();

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

            // console.log('mouseup in originaltext');

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
            $('.annotation-form-' + entry + ' input[name="start"]').val(
                newannotation.target[0].selector[1].start);
            $('.annotation-form-' + entry + ' input[name="end"]').val(
                newannotation.target[0].selector[1].end);

            // TextQuoteSelector.
            $('.annotation-form-' + entry + ' input[name="exact"]').val(
                newannotation.target[0].selector[2].exact);
            $('.annotation-form-' + entry + ' input[name="prefix"]').val(
                newannotation.target[0].selector[2].prefix);
            $('.annotation-form-' + entry + ' input[name="suffix"]').val(
                newannotation.target[0].selector[2].suffix);

            $('.annotation-form-' + entry + ' select').val(1);

            $('#annotationpreview-temp-' + entry).html(newannotation.target[0].selector[2].exact);

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
                $('.annotated-' + id).addClass('hovered');
            });

            $('.annotated').mouseleave(function() {
                var id = this.id.replace('annotated-', '');
                $('.annotation-box-' + id).removeClass('hovered');
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

            // Highlight annotation if hoverannotation button is hovered
            $(document).on('mouseover', '.hoverannotation', function() {
                var id = this.id.replace('hoverannotation-', '');
                $('.annotated-' + id).addClass('hovered');
            });

            $(document).on('mouseleave', '.hoverannotation', function() {
                var id = this.id.replace('hoverannotation-', '');
                $('.annotated-' + id).removeClass('hovered');
            });

        },
        error: function() {
            alert ('Error fetiching annotations');
        }
    });

    /**
     * Recreate annotations.
     *
     */
    function recreateAnnotations() {

        for (let annotation of Object.values(annotations)) {

            const rangeSelectors = [[
                {type: "RangeSelector", startContainer: annotation.startcontainer, startOffset: annotation.startoffset,
                endContainer: annotation.endcontainer, endOffset: annotation.endoffset},
                {type: "TextPositionSelector", start: annotation.start, end: annotation.end},
                {type: "TextQuoteSelector", exact: annotation.exact, prefix: annotation.prefix, suffix: annotation.suffix}
            ]];

            // console.log('rangeSelectors');
            // console.log(rangeSelectors);

            const target = rangeSelectors.map(selectors => ({
                selector: selectors,
            }));

            // console.log('target');
            // console.log(target);

            /** @type {AnnotationData} */
            const newannotation = {
                annotation: annotation,
                target: target,
            };

            // console.log(newannotation);

            anchor(newannotation, $("#entry-" + annotation.entry)[0]);

            $('#annotationpreview-' + annotation.id).html(annotation.exact);
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
            $('.annotation-form-' + entry + ' input[name="start"]').val(annotations[annotationid].start);
            $('.annotation-form-' + entry + ' input[name="end"]').val(annotations[annotationid].end);
            $('.annotation-form-' + entry + ' input[name="exact"]').val(annotations[annotationid].exact);
            $('.annotation-form-' + entry + ' input[name="prefix"]').val(annotations[annotationid].prefix);
            $('.annotation-form-' + entry + ' input[name="suffix"]').val(annotations[annotationid].suffix);

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
        $('.annotation-form input[name^="startoffset"]').val(-1);
        $('.annotation-form input[name^="endoffset"]').val(-1);

        $('.annotation-form textarea[name^="text"]').val('');

        $('.annotation-box').not('.annotation-form').show(); // To show again edited annotation.
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

        // console.log('removeHighlights highlights');
        // console.log(highlights);

        for (var i = 0; i < highlights.length; i++) {
            if (highlights[i].parentNode) {
                var pn = highlights[i].parentNode;
                const children = Array.from(highlights[i].childNodes);
                replaceWith(highlights[i], children);
                pn.normalize(); // To Be removed?
            }
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
    // function nodeFromXPath(xpath, root = document.body) {
    //     try {
    //         return evaluateSimpleXPath(xpath, root);
    //     } catch (err) {
    //         return document.evaluate(
    //             '.' + xpath,
    //             root,

    //             // The `namespaceResolver` and `result` arguments are optional in the spec
    //             // but required in Edge Legacy.
    //             null /* NamespaceResolver */,
    //             XPathResult.FIRST_ORDERED_NODE_TYPE,
    //             null /* Result */
    //         ).singleNodeValue;
    //     }
    // }

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

};

/**
 * Create a new annotation that is associated with the selected region of
 * the current document.
 *
 * @param {object} root - The root element
 * @return {object} - The new annotation
 */
function createAnnotation(root) {
    // console.log('createAnnotation');

    const ranges = [window.getSelection().getRangeAt(0)];

    // console.log('ranges');
    // console.log(ranges);

    if (ranges.collapsed) {
        return null;
    }

    //const info = await this.getDocumentInfo();
    const rangeSelectors = ranges.map(range => describe(root, range));

    // console.log('rangeSelectors');
    // console.log(rangeSelectors);

    const target = rangeSelectors.map(selectors => ({
      selector: selectors,
    }));

    // console.log('target');
    // console.log(target);

    /** @type {AnnotationData} */
    const annotation = {
      target,
    };

    // console.log('TARGET INFORMATION TO SAVE IN THE DB');
    // console.log(annotation);

    temp = anchor(annotation, root);

    // console.log('TEMP');
    // console.log(temp);

    return annotation;
}

/**
 * Get anchors for new annnotation.
 *
 * @param {Element} root
 * @param {Range} range
 * @return {object} - Array with the anchors.
 */
export function describe(root, range) {
    const types = [RangeAnchor, TextPositionAnchor, TextQuoteAnchor];
    const result = [];

    // console.log('describe');

    for (let type of types) {
      try {
        const anchor = type.fromRange(root, range);

        // console.log('type');
        // console.log(type);
        // console.log('anchor');
        // console.log(anchor);

        result.push(anchor.toSelector());
      } catch (error) {
        continue;
      }
    }
    return result;
}

// Anchoring

/**
   * Anchor an annotation's selectors in the document.
   *
   * _Anchoring_ resolves a set of selectors to a concrete region of the document
   * which is then highlighted.
   *
   * Any existing anchors associated with `annotation` will be removed before
   * re-anchoring the annotation.
   *
   * @param {AnnotationData} annotation
   * @return {obj} achor object
   */
 function anchor(annotation, root) {
    // console.log('anchor');
    // console.log('annotation');
    // console.log(annotation);

    /**
     * Resolve an annotation's selectors to a concrete range.
     *
     * @param {Target} target
     * @return {obj}
     */
    const locate = target => {

        // console.log('anchor -> locate');
        // console.log('target');
        // console.log(target);

      // Only annotations with an associated quote can currently be anchored.
      // This is because the quote is used to verify anchoring with other selector
      // types.
      if (
        !target.selector ||
        !target.selector.some(s => s.type === 'TextQuoteSelector')
      ) {
        return { annotation, target };
      }

      /** @type {Anchor} */
      let anchor;
      try {
        const range = htmlAnchor(root, target.selector);
        // Convert the `Range` to a `TextRange` which can be converted back to
        // a `Range` later. The `TextRange` representation allows for highlights
        // to be inserted during anchoring other annotations without "breaking"
        // this anchor.
        // console.log('anchor -> locate -> after htmlAnchor');
        // console.log('result of htmlAnchor');
        // console.log(range);
        const textRange = TextRange.fromRange(range);
        // console.log('range for anchor');
        // console.log('textRange');
        // console.log(textRange);

        anchor = { annotation, target, range: textRange };

        // console.log('anchor found');
        // console.log(anchor);
      } catch (err) {
        // console.log('error in try to find textrange');
        // console.log(err);
        anchor = { annotation, target };
      }

    //   console.log('anchor at the end of anchor -> locate');
    //   console.log(anchor);
      return anchor;
    };

    /**
     * Highlight the text range that `anchor` refers to.
     *
     * @param {Anchor} anchor
     */
    const highlight = anchor => {
        // console.log('highlight');
        // console.log('highlight resolveAnchor');
      const range = resolveAnchor(anchor);
    //   console.log('range');
    //   console.log(range);

      if (!range) {
        // console.log('no range');
        return;
      }

    //   console.log('highlight after resolveAnchor');
    //   console.log('range');
    //   console.log(range);

    //   console.log('annotation');
    //   console.log(annotation);

      let highlights = [];

      if (annotation.annotation) {
        highlights = highlightRange(range, annotation.annotation.id, 'annotated', annotation.annotation.color);
      } else {
        highlights = highlightRange(range, false, 'annotated_temp');
      }

    //   console.log('highlights after i should have highlighted range');
    //   console.log(highlights);

      highlights.forEach(h => {
        h._annotation = anchor.annotation;
      });
      anchor.highlights = highlights;

    //   if (this._focusedAnnotations.has(anchor.annotation.$tag)) {
    //     setHighlightsFocused(highlights, true);
    //   }
    };

    // Remove existing anchors for this annotation.
    // this.detach(annotation, false /* notify */); // To be replaced by own method

    // Resolve selectors to ranges and insert highlights.
    if (!annotation.target) {
      annotation.target = [];
    }
    const anchors = annotation.target.map(locate);
    // console.log('anchors after locate');
    // console.log(anchors);

    for (let anchor of anchors) {
        // console.log('before highlighting anchor');
        // console.log('anchor');
        // console.log(anchor);
        highlight(anchor);
        // console.log('after highlighting anchor');
    }

    // Set flag indicating whether anchoring succeeded. For each target,
    // anchoring is successful either if there are no selectors (ie. this is a
    // Page Note) or we successfully resolved the selectors to a range.
    annotation.$orphan =
      anchors.length > 0 &&
      anchors.every(anchor => anchor.target.selector && !anchor.range);

    // console.log('anchor ends');
    // console.log('anchors');
    // console.log(anchors);
    return anchors;
  }

/**
 * Resolve an anchor's associated document region to a concrete `Range`.
 *
 * This may fail if anchoring failed or if the document has been mutated since
 * the anchor was created in a way that invalidates the anchor.
 *
 * @param {Anchor} anchor
 * @return {Range|null}
 */
function resolveAnchor(anchor) {
    // console.log('resolveAnchor');
    // console.log('anchor');
    // console.log(anchor);

    if (!anchor.range) {
      return null;
    }
    try {
      return anchor.range.toRange();
    } catch {
      return null;
    }
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
        // console.log('highlightRange');
        // console.log('range');
        // console.log(range);

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
        const highlights = /** @type {HighlightElement[]} */ ([]);

        textNodeSpans.forEach(nodes => {
            const highlightEl = document.createElement('margic-highlight');
            highlightEl.className = cssClass;

            if (annotationid) {
                highlightEl.className += ' ' + cssClass + '-' + annotationid;
                highlightEl.style = "text-decoration:underline; text-decoration-color: #" + color;
                highlightEl.id = cssClass + '-' + annotationid;
                highlightEl.style.backgroundColor = '#' + color;
            }

            const parent = /** @type {Node} */ (nodes[0].parentNode);
            parent.replaceChild(highlightEl, nodes[0]);
            nodes.forEach(node => highlightEl.appendChild(node));

            highlights.push(highlightEl);

        });

        return highlights;
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
 * @param {RangeAnchor|TextPositionAnchor|TextQuoteAnchor} anchor
 * @param {Object} [options]
 * @return {obj} - range
 */
 function querySelector(anchor, options = {}) {
    // console.log('querySelector');
    // console.log('anchor');
    // console.log(anchor);
    // console.log('options');
    // console.log(options);

    return anchor.toRange(options);
  }

  /**
   * Anchor a set of selectors.
   *
   * This function converts a set of selectors into a document range.
   * It encapsulates the core anchoring algorithm, using the selectors alone or
   * in combination to establish the best anchor within the document.
   *
   * @param {Element} root - The root element of the anchoring context.
   * @param {Selector[]} selectors - The selectors to try.
   * @param {Object} [options]
   * @return {object} the query selector
   */
  function htmlAnchor(root, selectors, options = {}) {
    let position = null;
    let quote = null;
    let range = null;

    // console.log('htmlAnchor()');

    // Collect all the selectors
    for (let selector of selectors) {
      switch (selector.type) {
        case 'TextPositionSelector':
          position = selector;
          options.hint = position.start; // TextQuoteAnchor hint
          break;
        case 'TextQuoteSelector':
          quote = selector;
          break;
        case 'RangeSelector':
          range = selector;
          break;
      }
    }

    /**
     * Assert the quote matches the stored quote, if applicable
     * @param {Range} range
     * @return {Range} range
     */
    const maybeAssertQuote = range => {
        // console.log('maybeAssertQuote');
        // console.log('range');
        // console.log(range);
        // console.log('quote');
        // console.log(quote);
      if (quote?.exact && range.toString() !== quote.exact) {
        throw new Error('quote mismatch');
      } else {
        // console.log('range found!');
        // console.log(range);
        return range;
      }
    };

    let queryselector = false;
    if (range) {
        // console.log('range');

      let anchor = RangeAnchor.fromSelector(root, range);

    //   console.log('anchor');
    //   console.log(anchor);

      queryselector = querySelector(anchor, options);

      if (queryselector) {

        // console.log('htmlAnchor queryselector for RangeAnchor');
        // console.log(queryselector);

        return queryselector;
      } else {
        return maybeAssertQuote;
      }
    }

    if (position) {
        // console.log('position');

        let anchor = TextPositionAnchor.fromSelector(root, position);

        queryselector = querySelector(anchor, options);
        if (queryselector) {

            // console.log('htmlAnchor queryselector for TextPositionAnchor');
            // console.log(queryselector);
            return queryselector;
          } else {
            return maybeAssertQuote;
          }
    }

    if (quote) {
        // console.log('quote');
        // console.log('htmlAnchor queryselector for TextQuoteAnchor');

        let anchor = TextQuoteAnchor.fromSelector(root, quote);

        queryselector = querySelector(anchor, options);

        // console.log(queryselector);

        return queryselector;
    }

    return false;
  }