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
 * Tests for the hook callbacks.
 *
 * @package    local_eduplay
 * @category   test
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \local_eduplay\hook_callbacks
 */
final class hook_callbacks_test extends \basic_testcase {
    /**
     * The adapter loads only on the Interactive Video add/edit form.
     *
     * @dataProvider pages_provider
     * @param string $pagetype
     * @param string $path
     * @param bool $expected
     */
    public function test_applies(string $pagetype, string $path, bool $expected): void {
        $this->assertSame($expected, hook_callbacks::applies($pagetype, $path));
    }

    /**
     * Page types and paths.
     *
     * @return array
     */
    public static function pages_provider(): array {
        return [
            'add or edit form' => ['mod-interactivevideo-mod', '/course/modedit.php', true],
            'form under a subdirectory' => ['mod-interactivevideo-mod', '/moodle/course/modedit.php', true],
            'view page' => ['mod-interactivevideo-view', '/mod/interactivevideo/view.php', false],
            'other module form' => ['mod-page-mod', '/course/modedit.php', false],
            'right type, wrong path' => ['mod-interactivevideo-mod', '/mod/interactivevideo/view.php', false],
            'empty' => ['', '', false],
        ];
    }

    /**
     * The H5P editor adapter loads only on the pages that host the H5P content editor.
     *
     * @dataProvider editor_pages_provider
     * @param string $path
     * @param bool $expected
     */
    public function test_applies_to_h5p_editor(string $path, bool $expected): void {
        $this->assertSame($expected, hook_callbacks::applies_to_h5p_editor($path));
    }

    /**
     * Paths of the H5P editor pages and of other pages.
     *
     * @return array
     */
    public static function editor_pages_provider(): array {
        return [
            'content bank editor' => ['/contentbank/edit.php', true],
            'content bank editor under a subdirectory' => ['/moodle/contentbank/edit.php', true],
            'h5p edit page' => ['/h5p/edit.php', true],
            'content bank list' => ['/contentbank/index.php', false],
            'content view' => ['/contentbank/view.php', false],
            'course form' => ['/course/modedit.php', false],
            'similar name' => ['/contentbank/edit.php.bak', false],
            'empty' => ['', false],
        ];
    }
}
