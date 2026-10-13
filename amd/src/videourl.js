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
 * Accept the canonical EduPlay link in the video URL field of the Interactive Video form.
 *
 * mod_interactivevideo only accepts media URLs, so the canonical page link is replaced by the stable
 * h5p-url endpoint (which redirects to the MP4) before the plugin validates the field. The pattern mirrors
 * the strict parser of this plugin (classes/local/url_parser.php).
 *
 * @module      local_eduplay/videourl
 * @copyright   2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const BASE = 'https://eduplay.rnp.br';
const PATTERN = /^https:\/\/eduplay\.rnp\.br\/app\/video\/(?:embed\/)?([1-9][0-9]{0,17})\/?$/;

/**
 * Get the media URL (h5p-url) for a canonical or embed EduPlay video link.
 *
 * @param {string} value The text typed or pasted by the user.
 * @returns {string|null} The h5p-url, or null when the value is not a supported EduPlay link.
 */
export const toMediaUrl = (value) => {
    const match = PATTERN.exec(String(value).trim());
    return match ? `${BASE}/api/v1/videos/${match[1]}/h5p-url` : null;
};

/**
 * Whether an input event comes from the user typing (or deleting) character by character.
 *
 * While typing, a partial link such as ".../app/video/3" already matches the pattern; converting it then would
 * leave the remaining digits after the converted address. Pasting, dropping and autofill are not typing.
 *
 * @param {InputEvent|Event} event
 * @returns {boolean}
 */
export const isTyping = (event) => /^(insertText|insertCompositionText|delete)/.test(event.inputType || '');

/**
 * Initialise the adapter.
 */
export const init = () => {
    const input = document.querySelector('input[name="videourl"]');
    if (!input) {
        return;
    }
    const convert = (event) => {
        if (event.type === 'input' && isTyping(event)) {
            return;
        }
        const mediaUrl = toMediaUrl(input.value);
        if (mediaUrl !== null) {
            input.value = mediaUrl;
            if (event.type !== 'input') {
                // Typed links are converted when the field is left: let the plugin react as if the value had been pasted.
                input.dispatchEvent(new Event('input', {bubbles: true}));
            }
        }
    };
    // The capture phase on the target runs before the bubbling (jQuery) handlers of mod_interactivevideo,
    // so the plugin reads the already converted value.
    input.addEventListener('input', convert, true);
    input.addEventListener('change', convert, true);
};
