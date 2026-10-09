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
 * Client of the public (undocumented) EduPlay API: title search and video metadata.
 *
 * Safety rules: the host is fixed, the only user-controlled values are an integer id, an integer page and a search term
 * that is percent-encoded, redirects are not followed, timeouts are short, the response is validated field by field and
 * only public, active videos that do not require authentication are returned. Responses are cached.
 *
 * @package    local_eduplay
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class api_client {
    /** @var int Maximum length of a search term. */
    private const MAX_TERM = 100;

    /** @var int Maximum response size accepted, in bytes. */
    private const MAX_BODY = 1048576;

    /** @var callable|null Fetcher used by new clients when none is given (for tests and overrides). */
    private static $defaultfetcher = null;

    /** @var callable Function (string $url): array [int $httpcode, string $body]. */
    private $fetcher;

    /** @var bool Whether responses are cached. */
    private bool $usecache;

    /**
     * Constructor.
     *
     * @param callable|null $fetcher Function (string $url): array [int $httpcode, string $body]; null for real HTTP.
     * @param bool $usecache Whether to cache responses.
     */
    public function __construct(?callable $fetcher = null, bool $usecache = true) {
        $this->fetcher = $fetcher ?? self::$defaultfetcher ?? [self::class, 'fetch'];
        $this->usecache = $usecache;
    }

    /**
     * Whether remote lookups are enabled in the site settings (enabled when the setting does not exist yet).
     *
     * @return bool
     */
    public static function is_enabled(): bool {
        $value = get_config('local_eduplay', 'enableremote');
        return $value === false || $value === null || (bool) $value;
    }

    /**
     * Replace the fetcher used by new clients. Intended for automated tests.
     *
     * @param callable|null $fetcher Function (string $url): array [int $httpcode, string $body], or null for real HTTP.
     */
    public static function set_default_fetcher(?callable $fetcher): void {
        self::$defaultfetcher = $fetcher;
    }

    /**
     * Search public videos by title.
     *
     * @param string $term Text to search; empty text returns an empty result without any request.
     * @param int $page Page, starting at 1 (EduPlay returns 10 results per page).
     * @return search_result
     * @throws \moodle_exception When EduPlay cannot be reached or answers something unexpected.
     */
    public function search(string $term, int $page = 1): search_result {
        $term = self::clean_term($term);
        if ($term === '') {
            return search_result::none();
        }
        $page = max(1, $page);
        $url = url_parser::BASE . '/api/v1/search?term=' . rawurlencode($term) . '&page=' . $page;

        $cached = $this->cache_get('s_' . sha1($page . '|' . \core_text::strtolower($term)));
        if (is_array($cached)) {
            return search_result::from_array($cached);
        }

        [$code, $body] = $this->request($url);
        if ($code !== 200) {
            throw new \moodle_exception('apierror', 'local_eduplay', '', $code);
        }
        $result = self::parse_search($body, $page);
        $this->cache_set('s_' . sha1($page . '|' . \core_text::strtolower($term)), $result->to_array());
        return $result;
    }

    /**
     * Get the public metadata of a video.
     *
     * @param int $id Numeric EduPlay video id.
     * @return video_info|null Null when the video does not exist or is not public.
     * @throws \moodle_exception When EduPlay cannot be reached or answers something unexpected.
     */
    public function get_video(int $id): ?video_info {
        if ($id <= 0) {
            return null;
        }
        $key = 'v_' . $id;
        $cached = $this->cache_get($key);
        if ($cached !== null) {
            return $cached === [] ? null : video_info::from_array($cached);
        }

        [$code, $body] = $this->request(url_parser::BASE . '/api/v1/videos/' . $id);
        if ($code === 404) {
            $this->cache_set($key, []);
            return null;
        }
        if ($code !== 200) {
            throw new \moodle_exception('apierror', 'local_eduplay', '', $code);
        }
        $data = json_decode($body, true);
        $video = is_array($data) ? self::video_from_item($data) : null;
        if ($video !== null && $video->id !== $id) {
            $video = null;
        }
        $this->cache_set($key, $video === null ? [] : $video->to_array());
        return $video;
    }

    /**
     * Normalise a search term: no control characters, single spaces, at most 100 characters.
     *
     * @param string $term
     * @return string
     */
    public static function clean_term(string $term): string {
        $term = (string) preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $term);
        $term = (string) preg_replace('/\s+/u', ' ', $term);
        return trim(\core_text::substr(trim($term), 0, self::MAX_TERM));
    }

    /**
     * Real HTTP fetcher, using Moodle's curl (honours the site proxy and blocked-hosts settings).
     *
     * @param string $url An EduPlay API URL.
     * @return array [int $httpcode, string $body]
     */
    public static function fetch(string $url): array {
        global $CFG;
        require_once($CFG->libdir . '/filelib.php');

        $curl = new \curl();
        $curl->setHeader(['Accept: application/json']);
        $body = $curl->get($url, [], [
            'CURLOPT_TIMEOUT' => 10,
            'CURLOPT_CONNECTTIMEOUT' => 5,
            'CURLOPT_FOLLOWLOCATION' => false,
        ]);
        $info = $curl->get_info();
        return [(int) ($info['http_code'] ?? 0), is_string($body) ? $body : ''];
    }

    /**
     * Parse the body of a search response.
     *
     * @param string $body JSON text.
     * @param int $requestedpage Page that was requested, used when the response does not say.
     * @return search_result
     * @throws \moodle_exception When the body is not the expected JSON.
     */
    public static function parse_search(string $body, int $requestedpage = 1): search_result {
        $data = json_decode($body, true);
        if (!is_array($data) || !isset($data['contents']) || !is_array($data['contents'])) {
            throw new \moodle_exception('apierror', 'local_eduplay', '', 'format');
        }
        $videos = [];
        foreach ($data['contents'] as $item) {
            $video = self::video_from_item($item);
            if ($video !== null) {
                $videos[] = $video;
            }
        }
        $info = isset($data['pageInfo']) && is_array($data['pageInfo']) ? $data['pageInfo'] : [];
        $page = isset($info['currentPage']) && is_int($info['currentPage']) ? max(1, $info['currentPage']) : $requestedpage;
        $lastpage = isset($info['lastPage']) && is_int($info['lastPage']) ? max($page, min(1000, $info['lastPage'])) : $page;
        $total = isset($info['totalResults']) && is_int($info['totalResults']) ? max(0, $info['totalResults']) : count($videos);
        return new search_result($videos, $page, $lastpage, $total);
    }

    /**
     * Validate one item of the API (search result or video) and keep only public, active videos.
     *
     * @param mixed $item Decoded JSON item.
     * @return video_info|null Null when the item is not a usable public video.
     */
    public static function video_from_item(mixed $item): ?video_info {
        if (!is_array($item) || strtoupper((string) ($item['contentType'] ?? '')) !== 'VIDEO') {
            return null;
        }
        $id = $item['id'] ?? null;
        if (is_string($id) && ctype_digit($id)) {
            $id = (int) $id;
        }
        if (!is_int($id) || $id <= 0) {
            return null;
        }
        if (($item['status'] ?? null) !== 'ACTIVE' || ($item['visibility'] ?? null) !== 1
                || ($item['requiredAuthentication'] ?? true) !== false) {
            return null;
        }
        $name = trim(strip_tags((string) ($item['name'] ?? '')));
        if ($name === '') {
            return null;
        }
        $duration = isset($item['duration']) && is_numeric($item['duration']) ? max(0, (int) $item['duration']) : 0;
        return new video_info($id, \core_text::substr($name, 0, 255), $duration, self::safe_thumbnail($item['image'] ?? null));
    }

    /**
     * Keep a thumbnail only when it is an HTTPS URL on the EduPlay host.
     *
     * @param mixed $image
     * @return string|null
     */
    private static function safe_thumbnail(mixed $image): ?string {
        if (!is_string($image) || preg_match('/[\x00-\x20\x7f\\\\]/', $image)) {
            return null;
        }
        $parts = parse_url($image);
        if ($parts === false || ($parts['scheme'] ?? '') !== 'https' || strtolower($parts['host'] ?? '') !== 'eduplay.rnp.br'
                || isset($parts['user']) || isset($parts['port'])) {
            return null;
        }
        return $image;
    }

    /**
     * Run the request and enforce the response size limit.
     *
     * @param string $url
     * @return array [int $httpcode, string $body]
     */
    private function request(string $url): array {
        [$code, $body] = ($this->fetcher)($url);
        if (!is_string($body) || strlen($body) > self::MAX_BODY) {
            throw new \moodle_exception('apierror', 'local_eduplay', '', 'size');
        }
        return [(int) $code, $body];
    }

    /**
     * Read from the cache.
     *
     * @param string $key
     * @return array|null Array on a hit (empty array means a cached "not available"), null on a miss.
     */
    private function cache_get(string $key): ?array {
        if (!$this->usecache) {
            return null;
        }
        $value = \cache::make('local_eduplay', 'metadata')->get($key);
        if (!is_string($value)) {
            return null;
        }
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Write to the cache.
     *
     * @param string $key
     * @param array $value
     */
    private function cache_set(string $key, array $value): void {
        if ($this->usecache) {
            \cache::make('local_eduplay', 'metadata')->set($key, json_encode($value));
        }
    }
}
