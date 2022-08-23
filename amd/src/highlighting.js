/**
 * Functions for the highlighting and anchoring of annotations.
 *
 * This code originaly is from the Hypothesis project (https://github.com/hypothesis/client)
 * which is released under the 2-Clause BSD License (https://opensource.org/licenses/BSD-2-Clause),
 * sometimes referred to as the "Simplified BSD License".
 */

import $ from 'jquery';
import {RangeAnchor, TextPositionAnchor, TextQuoteAnchor} from './types';
import {TextRange} from './text-range';

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
 * @param {obj} root
 * @return {obj} achor object
 */
 export function anchor(annotation, root) {
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
        return {annotation, target};
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
 * Modified for handling annotations.
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

/**
 * Remove all temporary highlights under a given root element.
 */
 export function removeAllTempHighlights() {
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