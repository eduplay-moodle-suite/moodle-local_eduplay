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
 * Accept the canonical EduPlay link in the video fields of the H5P content editor.
 *
 * The H5P editor accepts any URL as a video file without checking it, so the canonical page link of an EduPlay video
 * would be saved as a video that never plays ("Video format not supported"). The link is replaced by the stable
 * h5p-url endpoint (which redirects to the MP4) before the editor reads it.
 *
 * The editor lives in an iframe of the same origin, so the listeners are attached to its document in the capture
 * phase. They only act on the URL field of a video field (not on image fields).
 *
 * @module      local_eduplay/h5peditor
 * @copyright   2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {toMediaUrl} from 'local_eduplay/videourl';

const URL_FIELD = 'input.h5p-file-url';
const VIDEO_FIELD = '.field.video';

/**
 * Replace the canonical EduPlay link typed in the URL field of a video field.
 *
 * @param {Element} field The URL input.
 */
const convert = (field) => {
    if (!field.matches(URL_FIELD) || !field.closest(VIDEO_FIELD)) {
        return;
    }
    const mediaUrl = toMediaUrl(field.value);
    if (mediaUrl !== null) {
        field.value = mediaUrl;
    }
};

/**
 * Convert the URL field of the dialog that contains the given element (used on the Insert button).
 *
 * @param {Element} element An element inside the "add video" dialog.
 */
const convertInDialog = (element) => {
    const dialog = element.closest('.h5p-add-dialog');
    const field = dialog ? dialog.querySelector(URL_FIELD) : null;
    if (field) {
        convert(field);
    }
};

/**
 * Attach the listeners to the document of an editor iframe.
 *
 * @param {Document} doc
 */
const hook = (doc) => {
    const onInput = (event) => convert(event.target);
    doc.addEventListener('input', onInput, true);
    doc.addEventListener('change', onInput, true);
    doc.addEventListener('paste', () => setTimeout(() => {
        const field = doc.activeElement;
        if (field) {
            convert(field);
        }
    }, 0), true);
    doc.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            convert(event.target);
        }
    }, true);
    doc.addEventListener('click', (event) => {
        if (event.target.closest && event.target.closest('.h5p-insert')) {
            convertInDialog(event.target);
        }
    }, true);
};

/**
 * Initialise the adapter.
 */
export const init = () => {
    const hooked = new WeakSet();
    const attach = () => {
        document.querySelectorAll('iframe').forEach((iframe) => {
            try {
                const doc = iframe.contentDocument;
                if (doc && doc.body && !hooked.has(doc)) {
                    hooked.add(doc);
                    hook(doc);
                }
            } catch (e) {
                // Iframes from other origins cannot be inspected and are not editors.
            }
        });
    };
    attach();
    // The editor creates its iframe and replaces its document after the page has loaded.
    setInterval(attach, 1000);
};
