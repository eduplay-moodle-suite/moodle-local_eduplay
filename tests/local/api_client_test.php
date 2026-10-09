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
 * Tests for the EduPlay API client (no real HTTP request is made).
 *
 * @package    local_eduplay
 * @category   test
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_eduplay\local\api_client
 * @covers     \local_eduplay\local\video_info
 * @covers     \local_eduplay\local\search_result
 */
final class api_client_test extends \advanced_testcase {
    /**
     * Build a search item as returned by EduPlay.
     *
     * @param array $override Fields to replace.
     * @return array
     */
    private static function item(array $override = []): array {
        return $override + [
            'contentType' => 'VIDEO',
            'id' => 353479,
            'name' => 'Documentário Eduplay 20 anos',
            'duration' => 323,
            'image' => 'https://eduplay.rnp.br/api/v1/assets/videos/images/1.jpg',
            'visibility' => 1,
            'status' => 'ACTIVE',
            'requiredAuthentication' => false,
            'embedAllowed' => false,
        ];
    }

    /**
     * Build a search response body.
     *
     * @param array $items
     * @param int $page
     * @param int $last
     * @return string
     */
    private static function body(array $items, int $page = 1, int $last = 1): string {
        return json_encode([
            'contents' => $items,
            'pageInfo' => ['quantity' => count($items), 'totalResults' => 42, 'currentPage' => $page, 'lastPage' => $last],
        ]);
    }

    /**
     * Client with a fake fetcher that records the requested URLs.
     *
     * @param array $responses Responses [code, body] returned in order (the last one is repeated).
     * @param array $urls Receives the requested URLs.
     * @param bool $usecache
     * @return api_client
     */
    private static function client(array $responses, array &$urls, bool $usecache = false): api_client {
        $i = 0;
        return new api_client(function (string $url) use ($responses, &$urls, &$i): array {
            $urls[] = $url;
            return $responses[min($i++, count($responses) - 1)];
        }, $usecache);
    }

    /**
     * A search builds a safe URL, percent-encodes UTF-8 and returns the validated videos and paging.
     */
    public function test_search_request_and_result(): void {
        $this->resetAfterTest();
        $urls = [];
        $client = self::client([[200, self::body([self::item()], 2, 5)]], $urls);

        $result = $client->search('  Documentário   Eduplay 20 anos ', 2);

        $this->assertSame(
            ['https://eduplay.rnp.br/api/v1/search?term=Document%C3%A1rio%20Eduplay%2020%20anos&page=2'],
            $urls
        );
        $this->assertCount(1, $result->videos);
        $this->assertSame(353479, $result->videos[0]->id);
        $this->assertSame('Documentário Eduplay 20 anos', $result->videos[0]->name);
        $this->assertSame(323, $result->videos[0]->duration);
        $this->assertSame('https://eduplay.rnp.br/api/v1/assets/videos/images/1.jpg', $result->videos[0]->thumbnail);
        $this->assertSame('https://eduplay.rnp.br/app/video/353479', $result->videos[0]->reference()->canonical_url());
        $this->assertSame([2, 5, 42], [$result->page, $result->lastpage, $result->total]);
    }

    /**
     * Characters that could change the request are encoded, never copied.
     */
    public function test_term_is_encoded(): void {
        $this->resetAfterTest();
        $urls = [];
        self::client([[200, self::body([])]], $urls)->search('a&page=9#x/../?y', 1);
        $this->assertSame('https://eduplay.rnp.br/api/v1/search?term=a%26page%3D9%23x%2F..%2F%3Fy&page=1', $urls[0]);
    }

    /**
     * An empty term makes no request.
     */
    public function test_empty_term_makes_no_request(): void {
        $this->resetAfterTest();
        $urls = [];
        $result = self::client([[200, self::body([])]], $urls)->search(" \t\n ");
        $this->assertSame([], $urls);
        $this->assertSame([], $result->videos);
    }

    /**
     * Long terms are truncated and the page is never below 1.
     */
    public function test_term_length_and_page_are_limited(): void {
        $this->resetAfterTest();
        $urls = [];
        self::client([[200, self::body([])]], $urls)->search(str_repeat('a', 300), -4);
        $this->assertSame('https://eduplay.rnp.br/api/v1/search?term=' . str_repeat('a', 100) . '&page=1', $urls[0]);
    }

    /**
     * Only public, active videos without authentication are kept; other content is dropped.
     *
     * @dataProvider unusable_items_provider
     * @param array $item
     */
    public function test_unusable_items_are_dropped(array $item): void {
        $this->resetAfterTest();
        $urls = [];
        $result = self::client([[200, self::body([$item])]], $urls)->search('x');
        $this->assertSame([], $result->videos);
    }

    /**
     * Items that must not be offered.
     *
     * @return array
     */
    public static function unusable_items_provider(): array {
        return [
            'channel' => [self::item(['contentType' => 'CHANNEL'])],
            'inactive' => [self::item(['status' => 'INACTIVE'])],
            'not public' => [self::item(['visibility' => 0])],
            'requires authentication' => [self::item(['requiredAuthentication' => true])],
            'authentication unknown' => [array_diff_key(self::item(), ['requiredAuthentication' => 1])],
            'no title' => [self::item(['name' => '  '])],
            'zero id' => [self::item(['id' => 0])],
            'non numeric id' => [self::item(['id' => 'abc'])],
            'not an object' => [['x']],
        ];
    }

    /**
     * Thumbnails outside the EduPlay host are not exposed; titles are plain text.
     */
    public function test_thumbnail_and_title_are_sanitised(): void {
        $this->resetAfterTest();
        $urls = [];
        $items = [
            self::item(['id' => 1, 'image' => 'https://iptv.usp.br/a.jpg', 'name' => '<b>Aula</b> <script>x</script>']),
            self::item(['id' => 2, 'image' => 'http://eduplay.rnp.br/a.jpg']),
            self::item(['id' => 3, 'image' => 'https://eduplay.rnp.br@evil.example/a.jpg']),
            self::item(['id' => '4']),
        ];
        $result = self::client([[200, self::body($items)]], $urls)->search('x');
        $this->assertCount(4, $result->videos);
        $this->assertNull($result->videos[0]->thumbnail);
        $this->assertSame('Aula x', $result->videos[0]->name);
        $this->assertNull($result->videos[1]->thumbnail);
        $this->assertNull($result->videos[2]->thumbnail);
        $this->assertSame(4, $result->videos[3]->id);
    }

    /**
     * Errors from EduPlay become exceptions with a clear message.
     *
     * @dataProvider failing_responses_provider
     * @param array $response
     */
    public function test_failures_throw(array $response): void {
        $this->resetAfterTest();
        $urls = [];
        $this->expectException(\moodle_exception::class);
        self::client([$response], $urls)->search('x');
    }

    /**
     * Responses that are not a valid search result.
     *
     * @return array
     */
    public static function failing_responses_provider(): array {
        return [
            'server error' => [[500, '{}']],
            'bad request' => [[400, '{"status":400}']],
            'redirect' => [[302, '']],
            'network failure' => [[0, '']],
            'not json' => [[200, '<html>']],
            'missing contents' => [[200, '{"pageInfo":{}}']],
            'contents not a list' => [[200, '{"contents":"x"}']],
            'oversized body' => [[200, str_repeat('a', 1048577)]],
        ];
    }

    /**
     * Video metadata: valid video, unknown video and a response for another id.
     */
    public function test_get_video(): void {
        $this->resetAfterTest();
        $urls = [];
        $video = self::client([[200, json_encode(self::item(['contentType' => 'video']))]], $urls)->get_video(353479);
        $this->assertSame(['https://eduplay.rnp.br/api/v1/videos/353479'], $urls);
        $this->assertSame('Documentário Eduplay 20 anos', $video->name);

        $this->assertNull(self::client([[404, '{}']], $urls)->get_video(999999999));
        $this->assertNull(self::client([[200, json_encode(self::item(['id' => 7]))]], $urls)->get_video(8));
        $this->assertNull(self::client([[200, json_encode(self::item(['visibility' => 0]))]], $urls)->get_video(353479));
        $this->assertNull(self::client([[200, '{}']], $urls)->get_video(0));
    }

    /**
     * Results are cached, including "not available".
     */
    public function test_cache(): void {
        $this->resetAfterTest();
        \cache::make('local_eduplay', 'metadata')->purge();
        $urls = [];
        $client = self::client([[200, self::body([self::item()])]], $urls, true);
        $client->search('cache test');
        $client->search('Cache  Test');
        $this->assertCount(1, $urls);

        $urls = [];
        $missing = self::client([[404, '{}']], $urls, true);
        $this->assertNull($missing->get_video(123456));
        $this->assertNull($missing->get_video(123456));
        $this->assertCount(1, $urls);
        \cache::make('local_eduplay', 'metadata')->purge();
    }

    /**
     * Remote lookups can be switched off in the site settings.
     */
    public function test_is_enabled_setting(): void {
        $this->resetAfterTest();
        $this->assertTrue(api_client::is_enabled());
        set_config('enableremote', 0, 'local_eduplay');
        $this->assertFalse(api_client::is_enabled());
        set_config('enableremote', 1, 'local_eduplay');
        $this->assertTrue(api_client::is_enabled());
    }
}
