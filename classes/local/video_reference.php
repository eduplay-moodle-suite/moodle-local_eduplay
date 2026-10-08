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
 * Small, portable reference to an EduPlay video.
 *
 * @package    local_eduplay
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class video_reference {
    /**
     * Constructor.
     *
     * @param int $videoid Numeric EduPlay video id.
     */
    public function __construct(
        /** @var int Numeric EduPlay video id. */
        public readonly int $videoid
    ) {
    }

    /**
     * Canonical URL, the only value to be persisted.
     *
     * @return string
     */
    public function canonical_url(): string {
        return url_parser::BASE . '/app/video/' . $this->videoid;
    }

    /**
     * Official player URL, for iframe embedding.
     *
     * @return string
     */
    public function embed_url(): string {
        return url_parser::BASE . '/app/video/embed/' . $this->videoid;
    }

    /**
     * Stable H5P endpoint (may redirect to a temporary CDN URL, which must not be stored).
     *
     * @return string
     */
    public function h5p_url(): string {
        return url_parser::BASE . '/api/v1/videos/' . $this->videoid . '/h5p-url';
    }
}
