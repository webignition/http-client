<?php

/**
 * Check that we can see if the redirect handler is enabled
 *  
 */
class RedirectHandlerIsEnabledTest extends BaseTest {

    public function testNotEnabledWhenGloballyDisabled() {                
        $this->httpClient->redirectHandler()->disable();
        
        foreach ($this->httpClient->redirectHandler()->getRedirectableResponseCodes() as $redirectableResponseCode) {
            $this->assertFalse($this->httpClient->redirectHandler()->isEnabled($redirectableResponseCode));
        }
    }
    
    
    public function testIsEnabledWhenGloballyDisabled() {                
        $this->httpClient->redirectHandler()->enable();
        
        foreach ($this->httpClient->redirectHandler()->getRedirectableResponseCodes() as $redirectableResponseCode) {
            $this->assertTrue($this->httpClient->redirectHandler()->isEnabled($redirectableResponseCode));
        }
    }    
    
    
    public function testIsEnabledForSpecificResponseCodesOnly() {
        $currentResponseCodeSet = array();
        
        foreach ($this->httpClient->redirectHandler()->getRedirectableResponseCodes() as $redirectableResponseCode) {
            $currentResponseCodeSet[] = $redirectableResponseCode;
            
            $this->httpClient->redirectHandler()->disable();
            
            foreach ($currentResponseCodeSet as $currentRedirectableResponseCode) {
                $this->httpClient->redirectHandler()->enable($currentRedirectableResponseCode);
                $this->assertTrue($this->httpClient->redirectHandler()->isEnabled($currentRedirectableResponseCode));
            }
        }
    }      
    
}