<?php

/**
 *  
 */
class CoundNotInflateDataError extends BaseTest {

    public function testTest() {
        $httpClient = new \webignition\Http\Client\Client();        
        $request = new \HttpRequest('http://dchtls.com//js/21032013.js');        
        
        try {
            $httpClient->getResponse($request);
        } catch (\Exception $e) {
            $this->assertEquals(7, $e->getRootException()->getCode());
            $this->assertEquals('Could not inflate data: data error', $e->getRootException()->getMessage());
        }
    }
    
}