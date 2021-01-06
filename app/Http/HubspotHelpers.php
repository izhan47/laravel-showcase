<?php
namespace App\Http;

use Request;
use Response;

class HubspotHelpers
{   
    public static function storeForm($post_json, $portalId, $FormGuid) {      
        try {           
        
            $endpoint = 'https://api.hsforms.com/submissions/v3/integration/submit/'.$portalId.'/'.$FormGuid;  

            $ch = @curl_init();
            @curl_setopt($ch, CURLOPT_POST, true);
            @curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
            @curl_setopt($ch, CURLOPT_URL, $endpoint);
            @curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = @curl_exec($ch);
            $status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_errors = curl_error($ch);            
            @curl_close($ch);   

        } catch (Exception $e) {            
            \Log::info($e->getMessage());
        }
    }

    public static function updateContact($post_json, $email, $hapikey) {      
        try {                    
            $endpoint = 'https://api.hubapi.com/contacts/v1/contact/email/'.$email.'/profile?hapikey='.$hapikey;             
            $ch = @curl_init();
            @curl_setopt($ch, CURLOPT_POST, true);
            @curl_setopt($ch, CURLOPT_POSTFIELDS, $post_json);
            @curl_setopt($ch, CURLOPT_URL, $endpoint);
            @curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            @curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = @curl_exec($ch);
            $status_code = @curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_errors = curl_error($ch);            
            @curl_close($ch);   

        } catch (Exception $e) {            
            \Log::info($e->getMessage());
        }
    }

}
