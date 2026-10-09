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
 * Portuguese (Brazil) strings for local_eduplay.
 *
 * @package    local_eduplay
 * @copyright  2026 Kelson da Costa Medeiros <kelsoncm@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['apierror'] = 'Não foi possível consultar o serviço EduPlay ({$a}). Tente novamente mais tarde.';
$string['cachedef_metadata'] = 'Metadados de vídeos EduPlay';
$string['cachettl'] = 'Tempo de vida do cache de metadados';
$string['cachettl_desc'] = 'Por quanto tempo os metadados dos vídeos EduPlay ficam em cache.';
$string['eduplay:manage'] = 'Gerenciar configurações do EduPlay';
$string['enableremote'] = 'Consultar o serviço EduPlay';
$string['enableremote_desc'] = 'Permite que o servidor consulte a API pública do EduPlay para buscar vídeos por título e ler título e miniatura. Desabilitado, este plugin não faz nenhuma requisição ao EduPlay e só funcionam links de vídeo colados.';
$string['pluginname'] = 'EduPlay';
$string['privacy:metadata:eduplay'] = 'Para buscar vídeos, o servidor envia o texto da busca ao serviço EduPlay (eduplay.rnp.br). Nenhum identificador do usuário é enviado.';
$string['privacy:metadata:eduplay:searchterm'] = 'O texto digitado pelo usuário na busca de vídeos.';
