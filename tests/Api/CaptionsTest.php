<?php


use ApiVideo\Client\Api\Captions;
use ApiVideo\Client\Model\Caption;
use Buzz\Message\Response;
use org\bovigo\vfs\content\LargeFileContent;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class CaptionsTest extends TestCase
{
    protected $filesystem;

    protected function setUp()
    {
        parent::setUp();
        $directory = array(
            'video' => array(),
        );

        $this->filesystem = vfsStream::setup('root', 660, $directory);

    }

    /**
     * Tears down the fixture.
     */
    protected function tearDown()
    {
        unset($this->filesystem);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function getSucceed()
    {
        $captionReturn = '
        {
            "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/captions/en",
            "src": "https://cdn.api.video/stream/942642a7-389f-4ec3-97a6-f836efb3f20e/captions/en.vtt",
            "srclang": "en",
            "default": false
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($captionReturn));


        $oAuthBrowser = $this->getMockedOAuthBrowser();
        $oAuthBrowser->method('get')->willReturn($response);

        $captions = new Captions($oAuthBrowser);
        $caption  = $captions->get('vi55mglWKqgywdX8Yu8WgDZ0', 'en');

        $this->assertInstanceOf('ApiVideo\Client\Model\Caption', $caption);
        $this->assertSame('/videos/vi55mglWKqgywdX8Yu8WgDZ0/captions/en', $caption->uri);
        $this->assertSame(
            'https://cdn.api.video/stream/942642a7-389f-4ec3-97a6-f836efb3f20e/captions/en.vtt',
            $caption->src
        );
        $this->assertSame('en', $caption->srclang);
        $this->assertFalse($caption->default);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function getAll()
    {
        $captionReturn = array(
            array(
                'uri'     => '/videos/vi55mglWKqgywdX8Yu8WgDZ0/captions/en',
                'src'     => 'https://cdn.api.video/stream/942642a7-389f-4ec3-97a6-f836efb3f20e/captions/en.vtt',
                'srclang' => 'en',
                'default' => false,
            ),
        );

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array(json_encode($captionReturn)));


        $oAuthBrowser = $this->getMockedOAuthBrowser();
        $oAuthBrowser->method('get')->willReturn($response);

        $captions    = new Captions($oAuthBrowser);
        $captionList = $captions->getAll('vi55mglWKqgywdX8Yu8WgDZ0');
        $caption     = current($captionList);
        $this->assertInstanceOf('ApiVideo\Client\Model\Caption', $caption);
        $this->assertSame('/videos/vi55mglWKqgywdX8Yu8WgDZ0/captions/en', $caption->uri);
        $this->assertSame(
            'https://cdn.api.video/stream/942642a7-389f-4ec3-97a6-f836efb3f20e/captions/en.vtt',
            $caption->src
        );
        $this->assertSame('en', $caption->srclang);
        $this->assertFalse($caption->default);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function uploadSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $captionReturn = '
        {
            "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/captions/en",
            "src": "https://cdn.api.video/stream/942642a7-389f-4ec3-97a6-f836efb3f20e/captions/en.vtt",
            "srclang": "en",
            "default": false
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($captionReturn));

        $caption = json_decode($captionReturn, true);

        $mockedBrowser->method('post')->willReturn($response);
        $mockedBrowser->method('submit')->willReturn($response);

        $captions = new Captions($mockedBrowser);

        /** @var Caption $result */
        $result = $captions->upload($this->getValideCaption()->url(), array('videoId' => 'vi55mglWKqgywdX8Yu8WgDZ0', 'language' => 'en'));
        $this->assertSame($caption['uri'], $result->uri);
        $this->assertSame($caption['src'], $result->src);
        $this->assertSame($caption['srclang'], $result->srclang);
        $this->assertSame($caption['default'], $result->default);
    }

    /**
     * @test
     * @throws ReflectionException
     */
    public function updateDefaultSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $captionReturn = '
        {
            "uri": "/videos/vi55mglWKqgywdX8Yu8WgDZ0/captions/en",
            "src": "https://cdn.api.video/stream/942642a7-389f-4ec3-97a6-f836efb3f20e/captions/en.vtt",
            "srclang": "en",
            "default": true
        }';

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 200);
        $setContent = $responseReflected->getMethod('setContent');
        $setContent->invokeArgs($response, array($captionReturn));

        $mockedBrowser->method('patch')->willReturn($response);

        $captions = new Captions($mockedBrowser);

        $patchedCaption = $captions->updateDefault('vi55mglWKqgywdX8Yu8WgDZ0', 'en', true);
        $this->assertTrue($patchedCaption->default);
    }


    /**
     * @test
     * @throws ReflectionException
     */
    public function deleteSucceed()
    {
        $mockedBrowser = $this->getMockedOAuthBrowser();

        $response = new Response();

        $responseReflected = new ReflectionClass('Buzz\Message\Response');
        $statusCode        = $responseReflected->getProperty('statusCode');
        $statusCode->setAccessible(true);
        $statusCode->setValue($response, 204);

        $mockedBrowser->method('delete')->willReturn($response);

        $captions = new Captions($mockedBrowser);

        $status = $captions->delete('vi55mglWKqgywdX8Yu8WgDZ0', 'en');
        $this->assertSame(204, $status);
    }

    private function getMockedOAuthBrowser()
    {
        return $this->getMockBuilder('ApiVideo\Client\Buzz\OAuthBrowser')
                    ->setMethods(array('get', 'submit', 'post', 'patch', 'delete'))
                    ->getMock();
    }

    private function getValideCaption()
    {

        return vfsStream::newFile('caption.vtt')
                        ->withContent(LargeFileContent::withKilobytes(2))
                        ->at($this->filesystem);
    }
}
