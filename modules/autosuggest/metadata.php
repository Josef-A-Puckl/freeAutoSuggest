<?php
/**
 * This program is free software;
 * you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation;
 * either version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License along with this program; if not, see <http://www.gnu.org/licenses/>
 */
// Aktuelle Versionen:
// Autosuggest 3.1: Modulform durch eComStyle.de erstellt (alle Dateien sind nun im Modulordner) und neues Design.
// Autosuggest 3.2: Erweitert durch eComStyle.de um die Suche nach Artikelnr., einige Bugfixes und ein neues Design für Azure und das Oxid Mobiletheme.
// Autosuggest 3.3: Anpassung durch eComStyle.de fuer Shopversion 4.9.
$sMetadataVersion = '1.1';
$aModule = array(
    'id'            => 'free_autosuggest',
    'title'         => 'Free AutoSuggest with Brain',
    'description'   => 'Fehlertolerante Suche mit automatischen Suchvorschl&auml;gen.',
    'version'       => '3.3',
    'thumbnail'     => '',
    'author'        => 'OXID Community',
    'email'         => '',
    'url'           => '',
    'extend' => array(
	    'oxubase' => 'autosuggest/controllers/autosuggest'
    ),
    'blocks' => array(
	    array('template' => 'widget/header/search.tpl', 'block' => 'widget_header_search_form', 'file' => '/views/blocks/search.tpl'),
    )
);