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
 * English strings for local_eduplay.
 *
 * @package    local_eduplay
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['apierror'] = 'The EduPlay service could not be queried ({$a}). Try again later.';
$string['cachedef_metadata'] = 'EduPlay video metadata';
$string['cachettl'] = 'Metadata cache lifetime';
$string['cachettl_desc'] = 'How long EduPlay video metadata is cached.';
$string['eduplay:manage'] = 'Manage EduPlay settings';
$string['enableremote'] = 'Query the EduPlay service';
$string['enableremote_desc'] = 'Allow the server to query the public EduPlay API to search videos by title and to read their title and thumbnail. When disabled, no request is made to EduPlay by this plugin and only pasted video links work.';
$string['pluginname'] = 'EduPlay';
$string['privacy:metadata:eduplay'] = 'To search videos, the server sends the search text to the EduPlay service (eduplay.rnp.br). No user identifier is sent.';
$string['privacy:metadata:eduplay:searchterm'] = 'The text typed by the user in the video search.';
