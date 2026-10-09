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
 * Hook callbacks: load the video link adapters only on the pages where they are needed.
 *
 * - The add/edit form of the third-party Interactive Video activity (mod_interactivevideo).
 * - The H5P content editor (content bank and H5P edit page).
 *
 * @package    local_eduplay
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /** @var string Page type of the add/edit form of the mod_interactivevideo activity. */
    public const PAGETYPE = 'mod-interactivevideo-mod';

    /** @var string[] URL paths (end of the path) of the pages that host the H5P content editor. */
    private const H5P_EDITOR_PATHS = ['/contentbank/edit.php', '/h5p/edit.php'];

    /**
     * Whether the Interactive Video form adapter must be loaded on a page.
     *
     * @param string $pagetype The page type.
     * @param string $path The URL path of the page.
     * @return bool
     */
    public static function applies(string $pagetype, string $path): bool {
        return $pagetype === self::PAGETYPE && self::path_ends_with($path, '/course/modedit.php');
    }

    /**
     * Whether the H5P editor adapter must be loaded on a page.
     *
     * @param string $path The URL path of the page.
     * @return bool
     */
    public static function applies_to_h5p_editor(string $path): bool {
        foreach (self::H5P_EDITOR_PATHS as $editorpath) {
            if (self::path_ends_with($path, $editorpath)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Load the AMD modules that accept the canonical EduPlay link in video fields.
     *
     * @param \core\hook\output\before_http_headers $hook
     */
    public static function before_http_headers(\core\hook\output\before_http_headers $hook): void {
        global $PAGE;

        $path = (string) $PAGE->url->get_path();
        if (self::applies((string) $PAGE->pagetype, $path)) {
            $PAGE->requires->js_call_amd('local_eduplay/videourl', 'init');
        } else if (self::applies_to_h5p_editor($path)) {
            $PAGE->requires->js_call_amd('local_eduplay/h5peditor', 'init');
        }
    }

    /**
     * Whether a path ends with a suffix.
     *
     * @param string $path
     * @param string $suffix
     * @return bool
     */
    private static function path_ends_with(string $path, string $suffix): bool {
        return substr($path, -strlen($suffix)) === $suffix;
    }
}
