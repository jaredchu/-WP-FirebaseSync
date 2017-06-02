<?php

/**
 * Created by PhpStorm.
 * User: jaredchu
 * Date: 11/29/16
 * Time: 4:07 PM
 */

use JCFirebase\JCFirebase;
use JCFirebase\Option;
use JCFirebase\Enums\PrintType;

class JCFirebaseTest extends PHPUnit_Framework_TestCase
{
    const FIREBASE_URI = 'https://fir-php-test-c7fa2.firebaseio.com/';
    const KEY_FILE = '/resource/firebase-php-test-0a49b34e5f4a.json';

    /**
     * @var JCFirebase
     */
    protected static $firebase;

    public static function setUpBeforeClass() {
        self::$firebase = JCFirebase::fromKeyFile( self::FIREBASE_URI, getcwd() . self::KEY_FILE );
    }

    public function testGetPathURI()
    {
        $firebase = self::$firebase;

        self::assertEquals(self::FIREBASE_URI . '.json', $firebase->getPathURI());
        self::assertEquals(self::FIREBASE_URI . '.json', $firebase->getPathURI('/'));

        self::assertEquals(self::FIREBASE_URI . 'path.json', $firebase->getPathURI('path'));
        self::assertEquals(self::FIREBASE_URI . 'path.json', $firebase->getPathURI('/path/'));
        self::assertEquals(self::FIREBASE_URI . 'path.json', $firebase->getPathURI('//path//'));

        self::assertEquals(self::FIREBASE_URI . 'path/to/your.json', $firebase->getPathURI('path/to/your'));
        self::assertEquals(self::FIREBASE_URI . 'path/to/your.json', $firebase->getPathURI('/path/to/your/'));
        self::assertEquals(self::FIREBASE_URI . 'path/to/your.json', $firebase->getPathURI('//path/to/your//'));

    }

    public function testGet()
    {
        $firebase = self::$firebase;

        $response = $firebase->get();
        self::assertEquals(200, $response->status());
    }

    public function testPut()
    {
        $firebase = self::$firebase;
        $subPath = 'put_test';

        $response = $firebase->put($subPath, array(
            'data' => self::data()
        ));

        self::assertEquals(200, $response->status());
        self::assertEquals(1, $response->json()->number);
        self::assertEquals('hello', $response->json()->string);
    }

    private function data()
    {
        return array(
            'number' => 1,
            'string' => 'hello'
        );
    }

    public function testPost()
    {
        $firebase = self::$firebase;
        $subPath = 'post_test';

        $response = $firebase->post($subPath, array(
            'data' => self::data()
        ));

        self::assertEquals(200, $response->status());
        self::assertNotNull($response->json()->name);
    }

    public function testPatch()
    {
        $firebase = self::$firebase;
        $subPath = 'patch_test';

        $firebase->put($subPath, array(
            'data' => self::data()
        ));

        $response = $firebase->patch($subPath, array(
            'data' => array(
                'number' => 2,
                'string' => 'hello2'
            )
        ));

        self::assertEquals(200, $response->status());
        self::assertEquals(2, $response->json()->number);
        self::assertEquals('hello2', $response->json()->string);
    }

    public function testDelete()
    {
        $firebase = self::$firebase;
        $subPath = 'delete_test';

        $firebase->put($subPath, array(
            'data' => self::data()
        ));

        $subPath = 'delete_test/number';

        $response = $firebase->delete($subPath);

        self::assertEquals(200, $response->status());
    }

    public function testGetShallow()
    {
        $firebase = self::$firebase;
        $subPath = 'get_shallow_test';

        $firebase->put($subPath, array('data' => self::data()));

        $response = $firebase->getShallow($subPath);
        self::assertEquals(200, $response->status());
        self::assertTrue($response->json()->number);
        self::assertTrue($response->json()->string);
    }

    public function testGetPrint()
    {
        $firebase = self::$firebase;

        self::assertContains(" ", $firebase->get(null, array(
            Option::_PRINT => PrintType::PRETTY
        ))->body());

        self::assertTrue($firebase->isValid());
    }
}