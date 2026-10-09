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

namespace local_eduplay;

/**
 * Hook callbacks: load the adapter only on the Interactive Video add/edit form.
 *
 * @package    local_eduplay
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /** @var string Page type of the add/edit form of the mod_interactivevideo activity. */
    public const PAGETYPE = 'mod-interactivevideo-mod';

    /**
     * Whether the adapter must be loaded on a page.
     *
     * @param string $pagetype The page type.
     * @param string $path The URL path of the page.
     * @return bool
     */
    public static function applies(string $pagetype, string $path): bool {
        return $pagetype === self::PAGETYPE && substr($path, -strlen('/course/modedit.php')) === '/course/modedit.php';
    }

    /**
     * Load the AMD module that accepts the canonical EduPlay link in the video URL field.
     *
     * @param \core\hook\output\before_http_headers $hook
     */
    public static function before_http_headers(\core\hook\output\before_http_headers $hook): void {
        global $PAGE;

        if (!self::applies((string) $PAGE->pagetype, (string) $PAGE->url->get_path())) {
            return;
        }
        $PAGE->requires->js_call_amd('local_eduplay/videourl', 'init');
    }
}
