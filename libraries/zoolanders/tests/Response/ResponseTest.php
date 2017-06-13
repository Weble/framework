<?php

namespace ZFTests\Response;

use ZFTests\TestCases\ZFTestCase;
use Zoolanders\Framework\Response\Response;

/**
 * Class ResponseTest
 * Http response wrapper
 *
 * @package ZFTests\Response
 */
class ResponseTest extends ZFTestCase
{
    /**
     * Response create test
     *
     * @covers          Response::create()
     * @dataProvider    statusCodesProvider
     */
    public function testResponseCreating($pass, $expect){
        if($pass === null){
            $response = new Response();
        } else {
            $response = new Response([], $pass);
        }

        $this->assertEquals($expect, $response->code);
    }

    /**
     * Test get set ops
     *
     * @covers          Response::get()
     * @covers          Response::set()
     *
     * @dataProvider    bindingDataSet
     */
    public function testSetGet($key, $value){
        $response = new Response();

        $response->set($key, $value);
        $this->assertEquals($value, $response->{$key});
    }

    /**
     * Test set get header ops
     *
     * @covers          Response::setHeader()
     * @depends         testSetGet
     *
     * @dataProvider    headersDataSet
     */
    public function testSetHeader($key, $value){
        $response = new Response();

        $response->setHeader($key, $value);
        $this->assertArrayHasKey($key, $response->headers);
        $this->assertEquals($response->headers[$key], $value);
    }

    /**
     * Test add func
     *
     * @covers          Response::add()
     * @dataProvider    bindingDataSet
     */
    public function testAdd($key, $value){

        $this->markTestSkipped("Move to JsonResponse test");

        /*
        $response = new Response([]);

        $response->add($key, $value);
        $this->assertArraySubset([ $value ], $response->{$key});*/
    }

    /**
     * Status codes data provider
     */
    public function statusCodesProvider(){
        return  [
            [ null, 200 ],
            [ 200, 200 ],
            [ 401, 401 ],
            [ 403, 403 ],
            [ 404, 404 ],
            [ 500, 500 ]
        ];
    }

    /**
     * Binding data set
     */
    public function bindingDataSet(){
        return [
            ['alpha', 'A'],
            ['bravo', 'b'],
            ['charlie', 1],
            ['delta', false],
            ['echo', array()]
        ];
    }

    /**
     * Headers data provider
     */
    public function headersDataSet(){
        return [
            [ 'Content-Type', 'application/pdf' ],
            [ 'Location', 'http://example.com' ],
            [ 'X-Data-Attribute', 'alpha' ],
        ];
    }
}
