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

/**
 * Admin settings for local_eduplay.
 *
 * @package    local_eduplay
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_eduplay', get_string('pluginname', 'local_eduplay'));
    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configcheckbox(
        'local_eduplay/enableremote',
        get_string('enableremote', 'local_eduplay'),
        get_string('enableremote_desc', 'local_eduplay'),
        1
    ));

    $settings->add(new admin_setting_configduration(
        'local_eduplay/cachettl',
        get_string('cachettl', 'local_eduplay'),
        get_string('cachettl_desc', 'local_eduplay'),
        HOURSECS
    ));
}
