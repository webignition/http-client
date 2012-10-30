<?php

/**
 * Check that we can get the response codes for which redirect handling is enabled
 *  
 */
class RedirectHandlerGetIsEnabledForTest extends BaseTest {
    
    public function testWhenGloballyEnabled() {
        $this->httpClient->redirectHandler()->enable();
        $responseCodesEnabledFor = $this->httpClient->redirectHandler()->getIsEnabledFor();
        $redirectableResponseCodes = $this->httpClient->redirectHandler()->getRedirectableResponseCodes();
        
        foreach ($redirectableResponseCodes as $redirectableResponseCode) {
            $this->assertTrue(isset($responseCodesEnabledFor[$redirectableResponseCode]));
        }
    }
    
    public function testWhenGloballyDisabled() {
        $this->httpClient->redirectHandler()->disable();
        $responseCodesEnabledFor = $this->httpClient->redirectHandler()->getIsEnabledFor();
        
        foreach ($responseCodesEnabledFor as $responseCode => $isEnabled) {
            $this->assertFalse($isEnabled);
        }        
    }    
    
    public function testWhenEnabledForSpecificResponseCodesOnly() {
        $currentResponseCodeSet = array();
        
        foreach ($this->httpClient->redirectHandler()->getRedirectableResponseCodes() as $redirectableResponseCode) {
            $currentResponseCodeSet[] = $redirectableResponseCode;
            
            $this->httpClient->redirectHandler()->disable();
            
            foreach ($currentResponseCodeSet as $currentRedirectableResponseCode) {
                $this->httpClient->redirectHandler()->enable($currentRedirectableResponseCode);
            }
            
            $currentlyEnabledFor = $this->httpClient->redirectHandler()->getIsEnabledFor();
            
            foreach ($currentlyEnabledFor as $responseCode => $isEnabled) {                
                $this->assertEquals($isEnabled, in_array($responseCode, $currentResponseCodeSet));
            }      
        }        
    }     
    
}