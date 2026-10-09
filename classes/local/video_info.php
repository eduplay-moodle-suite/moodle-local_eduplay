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
 * Public metadata of an EduPlay video, as returned by the EduPlay API (already validated).
 *
 * @package    local_eduplay
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class video_info {
    /**
     * Constructor.
     *
     * @param int $id Numeric EduPlay video id.
     * @param string $name Video title (plain text).
     * @param int $duration Duration in seconds.
     * @param string|null $thumbnail HTTPS thumbnail URL on the EduPlay host, or null.
     */
    public function __construct(
        /** @var int Numeric EduPlay video id. */
        public readonly int $id,
        /** @var string Video title (plain text). */
        public readonly string $name,
        /** @var int Duration in seconds. */
        public readonly int $duration,
        /** @var string|null Thumbnail URL. */
        public readonly ?string $thumbnail
    ) {
    }

    /**
     * Reference to this video, to build the canonical, embed and H5P URLs.
     *
     * @return video_reference
     */
    public function reference(): video_reference {
        return new video_reference($this->id);
    }

    /**
     * Plain array, safe to store in a cache.
     *
     * @return array
     */
    public function to_array(): array {
        return ['id' => $this->id, 'name' => $this->name, 'duration' => $this->duration, 'thumbnail' => $this->thumbnail];
    }

    /**
     * Rebuild from {@see to_array()}.
     *
     * @param array $data
     * @return self
     */
    public static function from_array(array $data): self {
        return new self(
            (int) $data['id'],
            (string) $data['name'],
            (int) $data['duration'],
            isset($data['thumbnail']) ? (string) $data['thumbnail'] : null
        );
    }
}
