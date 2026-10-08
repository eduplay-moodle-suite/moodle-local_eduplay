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

namespace local_eduplay\local;

/**
 * Contract to resolve EduPlay videos for other plugins of the suite.
 *
 * @package    local_eduplay
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface resolver_interface {
    /**
     * Whether the URL is supported.
     *
     * @param string $url
     * @return bool
     */
    public function supports_url(string $url): bool;

    /**
     * Parse a canonical URL.
     *
     * @param string $url
     * @return video_reference
     * @throws \moodle_exception When the URL is not supported.
     */
    public function parse_reference(string $url): video_reference;
}
