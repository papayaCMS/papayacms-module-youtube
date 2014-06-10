<?php
require_once(dirname(__FILE__).'/bootstrap.php');

class PapayaModuleYoutubeVideoTest extends PapayaTestCase {

  /**
  * @covers PapayaModuleYoutubeVideo::setOwner
  */
  public function testSetOwner() {
    $videoObject = new PapayaModuleYoutubeVideo_TestProxy();
    $owner = $this->getMock('OwnerClass');
    $videoObject->setOwner($owner);
    $this->assertAttributeSame($owner, '_owner', $videoObject);
  }

  /**
  * @covers PapayaModuleYoutubeVideo::setPageData
  */
  public function testSetPageData() {
    $videoObject = new PapayaModuleYoutubeVideo_TestProxy();
    $data = array("title" => "new video");
    $videoObject->setPageData($data);
    $this->assertAttributeSame($data, '_data', $videoObject);
  }

  /**
   * @covers PapayaModuleYoutubeVideo::setBoxData
   */
  public function testSetBoxData() {
    $videoObject = new PapayaModuleYoutubeVideo_TestProxy();
    $boxData = array("title" => "new video");
    $videoObject->setBoxData($boxData);
    $this->assertAttributeSame($boxData, '_boxData', $videoObject);
  }

  /**
  * @covers PapayaModuleYoutubeVideo::setPapayaXmlDomObject
  */
  public function testSetPapayaXmlDomObject() {
    $videoObject = new PapayaModuleYoutubeVideo_TestProxy();
    $papayaXmlDomObject = $this->getMock('PapayaXmlDocument');
    $videoObject->setPapayaXmlDomObject($papayaXmlDomObject);
    $this->assertAttributeSame($papayaXmlDomObject, '_papayaXmlDomObject', $videoObject);
  }

  /**
  * @covers PapayaModuleYoutubeVideo::getPapayaXmlDomObject
  */
  public function testGetPapayaXmlDomObject() {
    $videoObject = new PapayaModuleYoutubeVideo_TestProxy();
    $papayaXmlDomObject = $videoObject->getPapayaXmlDomObject();
    $this->assertInstanceOf('PapayaXmlDocument', $papayaXmlDomObject);
  }

  /**
   * @covers       PapayaModuleYoutubeVideo::getPageXml
   * @dataProvider getPageXmlProvider
   *
   * @param int $setNoCookie
   * @param string $videoFormat
   * @param string $url
   * @param int $height
   * @param int $html5
   */
  public function testGetPageXml($setNoCookie, $videoFormat, $url, $height, $html5) {
    $pluginLoaderMock = $this->getPluginLoaderMock(array(array('FORCE_HTML5', 1, NULL, $html5)));
    $videoObject = new PapayaModuleYoutubeVideo_TestProxy();
    $data = array(
      "title" => "new video",
      "subtitle" => "",
      "youtube_video_id" => "wPOgvzVOQig",
      "player_width" => 560,
      "video_format" => $videoFormat,
      "autoplay" => 0,
      "related" => 0,
      "show_info" => 1,
      "controls" => 1,
      "set_no_cookie" => $setNoCookie,
      "youtube_url" => "http://www.youtube.com",
      "youtube_no_cookie_url" => "http://www.youtube-nocookie.com",
      "imgalign" => "left",
      "breakstyle" => "none",
      "teaser" => "this is the teaser text",
      "image" => "25732c73d90ab89fc667e15fca30c7e9,180,202,max",
      "text" => "A video from Youtube"
    );
    $videoObject->setPageData($data);
    $owner = $this->getMock('base_object', array('getPapayaImageTag'));
    $videoObject->setOwner($owner);
    $papaya = $this->mockPapaya()->application(array('plugins' => $pluginLoaderMock));
    $videoObject->papaya($papaya);
    $this->assertXmlStringEqualsXmlString(
      '<video>
        <title>new video</title>
        <subtitle/>
        <player videoId="wPOgvzVOQig" width="560" height="'.$height.'"
          autoplay="0" rel="0" info="1" controls="1" url="'.$url.'"
          html5="'.($html5 ? 'yes' : 'no').'"/>
        <teaser>this is the teaser text</teaser>
        <image align="left" break="none"/>
        <text>A video from Youtube</text>
      </video>',
      $videoObject->getPageXml()
    );
  }

  //---------------dataProvider---------------
  public static function getPageXmlProvider() {
    return array(
      "no_cookie_16_9_html5" => array("1", "16:9", "http://www.youtube-nocookie.com", 316, 1),
      "no_cookie_16_9_flash" => array("1", "16:9", "http://www.youtube-nocookie.com", 316, 0),
      "no_cookie_4_3_html5" => array("1", "4:3", "http://www.youtube-nocookie.com", 421, 1),
      "no_cookie_4_3_flash" => array("1", "4:3", "http://www.youtube-nocookie.com", 421, 0),
      "cookie_16_9_html5" => array("0", "16:9", "http://www.youtube.com", 316, 1),
      "cookie_16_9_flash" => array("0", "16:9", "http://www.youtube.com", 316, 0),
      "cookie_4_3_html5" => array("0", "4:3", "http://www.youtube.com", 421, 1),
      "cookie_4_3_flash" => array("0", "4:3", "http://www.youtube.com", 421, 0),
    );
  }

  /**
  * @covers PapayaModuleYoutubeVideo::getTeaserXml
  */
  public function testGetTeaserXml() {
    $videoObject = new PapayaModuleYoutubeVideo_TestProxy();
    $data = array(
      "title" => "new video",
      "subtitle" => "",
      "teaser" => "this is the teaser text",
      "breakstyle" => "none",
      "imgalign" => "left",
      "image" => "25732c73d90ab89fc667e15fca30c7e9,180,202,max",
    );
    $videoObject->setPageData($data);
    $owner = $this->getMock('base_object', array('getPapayaImageTag'));
    $videoObject->setOwner($owner);
    $xml = '<title>new video</title><subtitle></subtitle><text>this is the teaser text</text><image align="left" break="none"/>';
    $this->assertEquals(
      $xml,
      $videoObject->getTeaserXml()
    );
  }

  /**
   * @covers       PapayaModuleYoutubeVideo::getBoxXml
   * @dataProvider getBoxXmlProvider
   *
   * @param string $setNoCookie
   * @param string $videoFormat
   * @param string $url
   * @param int $height
   * @param int $html5
   */
  public function testGetBoxXml($setNoCookie, $videoFormat, $url, $height, $html5) {
    $pluginLoaderMock = $this->getPluginLoaderMock(array(array('FORCE_HTML5', 1, NULL, $html5)));
    $videoObject = new PapayaModuleYoutubeVideo_TestProxy();
    $data = array(
      "title" => "new video",
      "youtube_video_id" => "wPOgvzVOQig",
      "player_width" => 560,
      "video_format" => $videoFormat,
      "autoplay" => 0,
      "related" => 0,
      "show_info" => 1,
      "controls" => 1,
      "set_no_cookie" => $setNoCookie,
      "youtube_url" => "http://www.youtube.com",
      "youtube_no_cookie_url" => "http://www.youtube-nocookie.com",
      "text" => "A video from Youtube"
    );
    $videoObject->setBoxData($data);
    $owner = $this->getMock('base_object', array('getPapayaImageTag'));
    $videoObject->setOwner($owner);
    $papaya = $this->mockPapaya()->application(array('plugins' => $pluginLoaderMock));
    $videoObject->papaya($papaya);
    $this->assertXmlStringEqualsXmlString(
      '<youtubebox>
        <title>new video</title>
        <player videoId="wPOgvzVOQig" width="560" height="'.$height.'"
         autoplay="0" rel="0" info="1" controls="1" url="'.$url.'"
         html5="'.($html5 ? 'yes' : 'no').'"/>
        <text>A video from Youtube</text>
      </youtubebox>',
      $videoObject->getBoxXml()
    );
  }

  //---------------dataProvider---------------
  public static function getBoxXmlProvider() {
    return array(
      "cookie_16_9_html5" => array("0", "16:9", "http://www.youtube.com", 316, 1),
      "cookie_16_9_flash" => array("0", "16:9", "http://www.youtube.com", 316, 0),
      "cookie_4_3_html5" => array("0", "4:3", "http://www.youtube.com", 421, 1),
      "cookie_4_3_flash" => array("0", "4:3", "http://www.youtube.com", 421, 0),
      "no_cookie_16_9_html5" => array("1", "16:9", "http://www.youtube-nocookie.com", 316, 1),
      "no_cookie_16_9_flash" => array("1", "16:9", "http://www.youtube-nocookie.com", 316, 0),
      "no_cookie_4_3_html5" => array("1", "4:3", "http://www.youtube-nocookie.com", 421, 1),
      "no_cookie_4_3_flash" => array("1", "4:3", "http://www.youtube-nocookie.com", 421, 0),
    );
  }

  /**
   * @covers PapayaModuleYoutubeVideo::getModuleOption
   */
  public function testGetModuleOption() {
    $pluginLoaderMock = $this->getPluginLoaderMock(array(array('test_option', 42, NULL, 4223)));

    $mockPapaya = $this->mockPapaya()->application(array('plugins' => $pluginLoaderMock));

    $config = new PapayaModuleYoutubeVideo_TestProxy();
    $config->papaya($mockPapaya);
    $this->assertEquals(4223, $config->getModuleOption('test_option', 42, NULL));
  }

  /**
   * @param array $valueMap
   * @return PapayaPluginLoader
   */
  private function getPluginLoaderMock($valueMap = array()) {
    $options = $this
      ->getMockBuilder('PapayaPluginOptions')
      ->disableOriginalConstructor()
      ->getMock();
    $options
      ->expects($this->any())
      ->method('get')
      ->will($this->returnValueMap($valueMap));
    $plugins = $this->getMockBuilder('PapayaPluginLoader')->getMock();
    $plugins
      ->expects($this->any())
      ->method('__get')
      ->with('options')
      ->will($this->returnValue(array(PapayaModuleYoutubeVideo::GUID => $options)));
    return $plugins;
  }

}

class PapayaModuleYoutubeVideo_TestProxy extends PapayaModuleYoutubeVideo {
  public function __construct() {
    // Nothing to do here
  }
}