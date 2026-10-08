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
 * Validates and parses canonical EduPlay video URLs.
 *
 * Only HTTPS URLs on the EduPlay host and the known route are accepted.
 * No request is ever made to a user-supplied URL.
 *
 * @package    local_eduplay
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class url_parser {
    /** @var string Base URL of the EduPlay service. */
    public const BASE = 'https://eduplay.rnp.br';

    /** @var string Allowed host. */
    private const HOST = 'eduplay.rnp.br';

    /**
     * Whether the URL is a supported canonical EduPlay video URL.
     *
     * @param string $url
     * @return bool
     */
    public static function supports_url(string $url): bool {
        return self::parse_reference($url) !== null;
    }

    /**
     * Parse a canonical URL (https://eduplay.rnp.br/app/video/{id}) into a reference.
     *
     * @param string $url
     * @return video_reference|null Null when the URL is not supported.
     */
    public static function parse_reference(string $url): ?video_reference {
        $url = trim($url);
        if (preg_match('/[\x00-\x20\x7f\\\\@]/', $url)) {
            return null;
        }
        $parts = parse_url($url);
        if ($parts === false
                || ($parts['scheme'] ?? '') !== 'https'
                || strtolower($parts['host'] ?? '') !== self::HOST
                || isset($parts['port'])
                || isset($parts['user'])
                || isset($parts['query'])
                || isset($parts['fragment'])) {
            return null;
        }
        if (!preg_match('~^/app/video/([1-9][0-9]{0,17})/?$~', $parts['path'] ?? '', $m)) {
            return null;
        }
        return new video_reference((int) $m[1]);
    }
}
