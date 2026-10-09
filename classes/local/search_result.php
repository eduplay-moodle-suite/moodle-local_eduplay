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
 * One page of search results.
 *
 * @package    local_eduplay
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class search_result {
    /**
     * Constructor.
     *
     * @param video_info[] $videos Public videos of this page (other content types and unavailable videos are dropped).
     * @param int $page Current page, starting at 1.
     * @param int $lastpage Last page, at least 1.
     * @param int $total Total of results reported by EduPlay (includes dropped items).
     */
    public function __construct(
        /** @var video_info[] Public videos of this page. */
        public readonly array $videos,
        /** @var int Current page, starting at 1. */
        public readonly int $page,
        /** @var int Last page, at least 1. */
        public readonly int $lastpage,
        /** @var int Total of results reported by EduPlay. */
        public readonly int $total
    ) {
    }

    /**
     * An empty result.
     *
     * @return self
     */
    public static function none(): self {
        return new self([], 1, 1, 0);
    }

    /**
     * Plain array, safe to store in a cache.
     *
     * @return array
     */
    public function to_array(): array {
        return [
            'videos' => array_map(static fn(video_info $v): array => $v->to_array(), $this->videos),
            'page' => $this->page,
            'lastpage' => $this->lastpage,
            'total' => $this->total,
        ];
    }

    /**
     * Rebuild from {@see to_array()}.
     *
     * @param array $data
     * @return self
     */
    public static function from_array(array $data): self {
        return new self(
            array_map(static fn(array $v): video_info => video_info::from_array($v), $data['videos']),
            (int) $data['page'],
            (int) $data['lastpage'],
            (int) $data['total']
        );
    }
}
