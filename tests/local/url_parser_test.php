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
 * Tests for the URL parser.
 *
 * @package    local_eduplay
 * @category   test
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_eduplay\local\url_parser
 * @covers     \local_eduplay\local\video_reference
 */
final class url_parser_test extends \basic_testcase {
    /**
     * Valid URLs produce the expected derived URLs.
     */
    public function test_valid_url(): void {
        $ref = url_parser::parse_reference('https://eduplay.rnp.br/app/video/353479');
        $this->assertNotNull($ref);
        $this->assertSame(353479, $ref->videoid);
        $this->assertSame('https://eduplay.rnp.br/app/video/353479', $ref->canonical_url());
        $this->assertSame('https://eduplay.rnp.br/app/video/embed/353479', $ref->embed_url());
        $this->assertSame('https://eduplay.rnp.br/api/v1/videos/353479/h5p-url', $ref->h5p_url());
    }

    /**
     * Invalid, foreign or hostile URLs are rejected.
     *
     * @dataProvider invalid_provider
     * @param string $url
     */
    public function test_invalid_url(string $url): void {
        $this->assertFalse(url_parser::supports_url($url));
    }

    /**
     * Invalid URLs, including SSRF attempts.
     *
     * @return array
     */
    public static function invalid_provider(): array {
        return [
            'http' => ['http://eduplay.rnp.br/app/video/1'],
            'other host' => ['https://evil.example/app/video/1'],
            'userinfo trick' => ['https://eduplay.rnp.br@evil.example/app/video/1'],
            'suffix host' => ['https://eduplay.rnp.br.evil.example/app/video/1'],
            'private ip' => ['https://127.0.0.1/app/video/1'],
            'other route' => ['https://eduplay.rnp.br/app/channel/1'],
            'embed route' => ['https://eduplay.rnp.br/app/video/embed/1'],
            'non numeric id' => ['https://eduplay.rnp.br/app/video/abc'],
            'zero id' => ['https://eduplay.rnp.br/app/video/0'],
            'with port' => ['https://eduplay.rnp.br:8443/app/video/1'],
            'with query' => ['https://eduplay.rnp.br/app/video/1?x=1'],
            'empty' => [''],
            'space' => ['https://eduplay.rnp.br/app/video/1 x'],
        ];
    }
}
