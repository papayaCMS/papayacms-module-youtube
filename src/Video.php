<?php
/**
* Youtube video page, page module base class
* Display youtube videos
*
* @copyright 2010 by papaya Software GmbH - All rights reserved.
* @link http://www.papaya-cms.com/
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU General Public License, version 2
*
* You can redistribute and/or modify this script under the terms of the GNU General Public
* License (GPL) version 2, provided that the copyright and license notes, including these
* lines, remain unmodified. papaya is distributed in the hope that it will be useful, but
* WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
* FOR A PARTICULAR PURPOSE.
*
* @package Papaya-Modules
* @subpackage Youtube
*/

/**
 * Simple youtube page module
 * Display youtube videos
 *
 * @package Papaya-Modules
 * @subpackage Youtube
 */
class PapayaModuleYoutubeVideo extends base_connector {

  /**
   * GUID of this connector
   */
  const GUID = '73f55ecb9082c1c3b3481ac4831e70a2';

  /**
  * Page configuration data
  * @var array
  */
  private $_data = array();

  /**
  * Box configuration data
  * @var array
  */
  private $_boxData = array();

  /**
  * Owner object
  * @var PapayaModuleYoutubeVideoPage
  */
  private $_owner = NULL;

  /**
  * The PapayaXmlDocument to be used
  * @var PapayaXmlDocument
  */
  private $_papayaXmlDomObject = NULL;

  /**
   * List of module options that can be set in the backend
   *
   * @var array
   */
  public $pluginOptionFields = array(
    'FORCE_HTML5' => array(
      'Force HTML5 player',
      '',
      TRUE,
      'yesno',
    ),
  );

  /**
  * Set owner object
  *
  * @param base_plugin $owner
  */
  public function setOwner($owner) {
    $this->_owner = $owner;
  }

  /**
  * Set page configuration data
  *
  * @param array $data
  */
  public function setPageData($data) {
    $this->_data = $data;
  }

  /**
  * set box configuration data
  *
  * @param array $boxData
  */
  public function setBoxData($boxData) {
    $this->_boxData = $boxData;
  }

  /**
  * Set the PapayaXmlDocument to be used
  *
  * @param PapayaXmlDocument $papayaXmlDomObject
  */
  public function setPapayaXmlDomObject($papayaXmlDomObject) {
    $this->_papayaXmlDomObject = $papayaXmlDomObject;
  }

  /**
  * Get the PapayaXmlDocument object
  * @return PapayaXmlDocument
  */
  public function getPapayaXmlDomObject() {
    if (!is_object($this->_papayaXmlDomObject)) {
      $this->_papayaXmlDomObject = new PapayaXmlDocument();
    }
    return $this->_papayaXmlDomObject;
  }

  /**
  * Get The page's XML output
  *
  * @return string XML
  */
  public function getPageXml() {

    $papyaXmlDomObject = $this->getPapayaXmlDomObject();
    $content = $papyaXmlDomObject->appendElement("video");
    if ($this->_data['set_no_cookie'] == 1) {
      $url = $this->_data['youtube_no_cookie_url'];
    } else {
      $url = $this->_data['youtube_url'];
    }

    $playerHeight = 0;
    if ($this->_data['video_format'] == '16:9') {
      $playerHeight = $this->_data['player_width'] / 1.77;
    } else if ($this->_data['video_format'] == '4:3') {
      $playerHeight = $this->_data['player_width'] / 1.33;
    }

    $content->appendElement("title", array(), $this->_data['title']);
    $content->appendElement('subtitle', array(), $this->_data['subtitle']);
    $content->appendElement(
      "player",
      array(
        "videoId" => $this->_data['youtube_video_id'],
        "width" => $this->_data['player_width'],
        "height" => round($playerHeight),
        "autoplay" => $this->_data['autoplay'],
        "rel" => $this->_data['related'],
        "info" => $this->_data['show_info'],
        "controls" => $this->_data['controls'],
        "url" => $url,
        "html5" => $this->getModuleOption('FORCE_HTML5', 1) ? 'yes' : 'no',
      )
    );
    $teaser = $content->appendElement("teaser");
    $teaser->appendXml($this->_data['teaser']);
    $image = $content->appendElement(
      "image",
      array(
        "align" => $this->_data['imgalign'],
        "break" => $this->_data['breakstyle']
      )
    );
    $image->appendXml(
      $this->_owner->getPapayaImageTag(
        $this->_data['image']
      )
    );
    $text = $content->appendElement("text");
    $text->appendXml($this->_data['text']);

    if (isset($this->_data['modified'])) {
      $content->appendElement(
        "history",
        array(
          "modified" => $this->_data['modified'],
          "created" => $this->_data['created'],
          "update" => $this->_data['update']
        )
      );
    }
    return $papyaXmlDomObject->saveXML($content);
  }

  /**
  * Get the page's teaser XML output
  * @return string XML
  */
  public function getTeaserXml() {
    $papayaXmlDomObject = $this->getPapayaXmlDomObject();
    $teaser = $papayaXmlDomObject->appendElement('teaser');
    $teaser->appendElement('title', array(), $this->_data['title']);
    $teaser->appendElement('subtitle', array(), $this->_data['subtitle']);
    $text = $teaser->appendElement('text');
    $text->appendXml($this->_data['teaser']);
    $image = $teaser->appendElement(
      "image",
      array(
        "align" => $this->_data['imgalign'],
        "break" => $this->_data['breakstyle']
      )
    );
    $image->appendXml(
      $this->_owner->getPapayaImageTag(
        $this->_data['image']
      )
    );
    return $teaser->saveFragment();

  }

  /**
  * Get The page's XML output
  *
  * @return string XML
  */
  public function getBoxXml() {

    $papayaXmlDomObject = $this->getPapayaXmlDomObject();
    $content = $papayaXmlDomObject->appendElement("youtubebox");
    if ($this->_boxData['set_no_cookie'] == 1) {
      $url = $this->_boxData['youtube_no_cookie_url'];
    } else {
      $url = $this->_boxData['youtube_url'];
    }
    $playerHeight = 0;
    if ($this->_boxData['video_format'] == '16:9') {
      $playerHeight = $this->_boxData['player_width'] / 1.77;
    } else if ($this->_boxData['video_format'] == '4:3') {
      $playerHeight = $this->_boxData['player_width'] / 1.33;
    }

    $content->appendElement("title", array(), $this->_boxData['title']);
    $content->appendElement(
      "player",
      array(
        "videoId" => $this->_boxData['youtube_video_id'],
        "width" => $this->_boxData['player_width'],
        "height" => round($playerHeight),
        "autoplay" => $this->_boxData['autoplay'],
        "rel" => $this->_boxData['related'],
        "info" => $this->_boxData['show_info'],
        "controls" => $this->_boxData['controls'],
        "url" => $url,
        "html5" => $this->getModuleOption('FORCE_HTML5', 1) ? 'yes' : 'no',
      )
    );
    $text = $content->appendElement("text");
    $text->appendXml($this->_boxData['text']);
    return $papayaXmlDomObject->saveXML($content);
  }

  /**
   * Shortcut to read a single option for the module.
   *
   * @param $name
   * @param mixed $default
   * @param PapayaFilter $filter
   * @return mixed
   */
  public function getModuleOption($name, $default = NULL, $filter = NULL) {
    return $this
      ->papaya()
      ->plugins
      ->options[self::GUID]
      ->get($name, $default, $filter);
  }

}


