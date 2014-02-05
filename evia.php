<?php
/**
 * Created by Jakub Trancik <jakub.trancik [at] relbit [dot] com>.
 * Date: 1/23/14
 *
 * TODO: testing
 */

class HttpError401Exception extends Exception
{
    public function __construct() {
        parent::__construct("Unauthorized", 401);
    }

    public function __toString() {
        return __CLASS__ . ": {$this->code} {$this->message}\n";
    }
}

class HttpError404Exception extends Exception
{
    public function __construct() {
        parent::__construct("Not Found", 404);
    }

    public function __toString() {
        return __CLASS__ . ": {$this->code} {$this->message}\n";
    }
}

class HttpError422Exception extends Exception
{
    public function __construct() {
        parent::__construct("Unprocessable Entity", 422);
    }

    public function __toString() {
        return __CLASS__ . ": {$this->code} {$this->message}\n";
    }
}

class HttpError500Exception extends Exception
{
    public function __construct() {
        parent::__construct("Internal server error", 500);
    }

    public function __toString() {
        return __CLASS__ . ": {$this->code} {$this->message}\n";
    }
}

class HttpError503Exception extends Exception
{
    public function __construct() {
        parent::__construct("Service Unavailable", 503);
    }

    public function __toString() {
        return __CLASS__ . ": {$this->code} {$this->message}\n";
    }
}

class CurlErrorException extends Exception
{
    public function __construct($message, $code) {
        parent::__construct($message, $code);
    }

    public function __toString() {
        return __CLASS__ . ": {$this->code} {$this->message}\n";
    }
}

class GenericException extends Exception
{
    public function __construct($message, $code) {
        parent::__construct($message, $code);
    }

    public function __toString() {
        return __CLASS__ . ": {$this->code} {$this->message}\n";
    }
}

class Evia {
    private $email;
    private $password;
    private $baseurl;
    private $verify;

    /**
     *  Constructor
     */

    function Evia($email, $password, $baseurl){
        $this->email = $email;
        $this->password = $password;
        $this->baseurl = $baseurl;
        $this->verify = true;
    }

    /**
     *  Verification
     */

    function disableVerification(){
        $this->verify = false;
    }

    function enableVerification(){
        $this->verify = true;
    }

    /**
    *  REST
    */

    private function post($url, $data){
        $defaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL =>  $this->baseurl."/".$url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_SSL_VERIFYPEER => $this->verify,
            CURLOPT_USERPWD => $this->email.":".$this->password,
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($defaults));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        switch ($httpcode) {
            case 200:
                return $result;
                break;
            case 201:
                return $result;
                break;
            case 202:
                return $result;
                break;
            case 401:
                throw new HttpError401Exception();
                break;
            case 404:
                throw new HttpError404Exception();
                break;
            case 422:
                throw new HttpError422Exception();
                break;
            case 500:
                throw new HttpError500Exception();
                break;
            case 503:
                throw new HttpError503Exception();
                break;
            default:
                if (($result == 0)
                    && ($httpcode == 0) ){
                    throw new CurlErrorException($error, $errno);
                } else {
                    throw new GenericException($result, $httpcode);
                }
                break;
        }

    }


    private function get($url, array $get = NULL)
    {
        $defaults = array(
            CURLOPT_URL => $this->baseurl."/".$url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($get),
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_SSL_VERIFYPEER => $this->verify,
            CURLOPT_USERPWD => $this->email.":".$this->password
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($defaults));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        switch ($httpcode) {
            case 200:
                return $result;
                break;
            case 201:
                return $result;
                break;
            case 202:
                return $result;
                break;
            case 401:
                throw new HttpError401Exception();
                break;
            case 404:
                throw new HttpError404Exception();
                break;
            case 422:
                throw new HttpError422Exception();
                break;
            case 500:
                throw new HttpError500Exception();
                break;
            case 503:
                throw new HttpError503Exception();
                break;
            default:
                if (($result == 0)
                   && ($httpcode == 0) ){
                    throw new CurlErrorException($error, $errno);
                } else {
                    throw new GenericException($result, $httpcode);
                }
                break;
        }
    }

    private function put($url, $data){
        $defaults = array(
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_HEADER => 0,
            CURLOPT_URL =>  $this->baseurl."/".$url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_SSL_VERIFYPEER => $this->verify,
            CURLOPT_USERPWD => $this->email.":".$this->password
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($defaults));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        switch ($httpcode) {
            case 200:
                return $result;
                break;
            case 201:
                return $result;
                break;
            case 202:
                return $result;
                break;
            case 401:
                throw new HttpError401Exception();
                break;
            case 404:
                throw new HttpError404Exception();
                break;
            case 422:
                throw new HttpError422Exception();
                break;
            case 500:
                throw new HttpError500Exception();
                break;
            case 503:
                throw new HttpError503Exception();
                break;
            default:
                if (($result == 0)
                    && ($httpcode == 0) ){
                    throw new CurlErrorException($error, $errno);
                } else {
                    throw new GenericException($result, $httpcode);
                }
                break;
        }

    }

    private function delete($url,$data){
        $defaults = array(
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HEADER => 0,
            CURLOPT_URL =>  $this->baseurl."/".$url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 4,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_SSL_VERIFYPEER => $this->verify,
            CURLOPT_HEADER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_USERPWD => $this->email.":".$this->password
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($defaults));
        $result = curl_exec($ch);
        $error = curl_error($ch);
        $errno = curl_errno($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        switch ($httpcode) {
            case 200:
                return $result;
                break;
            case 201:
                return $result;
                break;
            case 202:
                return $result;
                break;
            case 401:
                throw new HttpError401Exception();
                break;
            case 404:
                throw new HttpError404Exception();
                break;
            case 422:
                throw new HttpError422Exception();
                break;
            case 500:
                throw new HttpError500Exception();
                break;
            case 503:
                throw new HttpError503Exception();
                break;
            default:
                if (($result == 0)
                    && ($httpcode == 0) ){
                    throw new CurlErrorException($error, $errno);
                } else {
                    throw new GenericException($result, $httpcode);
                }
                break;
        }

    }

    /**
    *  APPLICATIONS
    */

    function getApps($limit = 0, $offset = 0, $filter = array()){
        $tmp = array();
        $url = "apps";
        if ($limit > 0) {
            $url = $url . "?limit=" . $limit;
        } else {
            $url = $url . "?";
        }

        $url = $url . "&offset=" . $offset;

        if (!empty($filter)) {
            foreach ($filter as $key => $value) {
                $url = $url . "&term[" . $key . "]=" . $value;
            }
        }

        $result = $this->get($url, $tmp);

        return $result;
    }

    function getApp($id){
        $tmp = array();
        $result = $this->get("apps/".$id, $tmp);

        return $result;
    }

    function addApp($name){
        $data = array(
            "app[name]"=>$name
        );
        $result = $this->post("apps", $data);

        return $result;
    }

    function updateApp($id,$newName){
        $data = array(
            "app[name]"=>$newName
        );
        $result = $this->put("apps/".$id, $data);

        return $result;
    }

    function deleteApp($id, $name){
        $data = array(
            "app[name]"=>$name
        );
        $result = $this->delete("apps/".$id,$data);

        return $result;
    }

    /**
     *  DATABASES
     */

    function addDatabase($appID, $databaseName, $addonID, $password){
        $data = array(
            "db[name]"=>$databaseName,
            "db[addon_id]"=>$addonID,
            "db[password]"=>$password
        );
        $result = $this->post("apps/" . $appID . "/databases", $data);

        return $result;
    }

    function getDatabases($appID, $limit = 0, $offset = 0, $filter = array()){
        $tmp = array();
        $url = "apps/" . $appID . "/databases";
        if ($limit > 0) {
            $url = $url . "?limit=" . $limit;
        } else {
            $url = $url . "?";
        }

        $url = $url . "&offset=" . $offset;

        if (!empty($filter)) {
            foreach ($filter as $key => $value) {
                $url = $url . "&term[" . $key . "]=" . $value;
            }
        }
        $result = $this->get($url, $tmp);

        return $result;
    }

    function getDatabase($appID, $databaseID){
        $tmp = array();
        $result = $this->get("apps/" . $appID . "/databases/" . $databaseID, $tmp);

        return $result;
    }

    function updateDatabase($appID, $databaseID, $newPassword){
        $data = array(
            "db[password]"=>$newPassword
        );
        $result = $this->put("apps/" . $appID . "/databases/" . $databaseID, $data);

        return $result;
    }

    function deleteDatabase($appID, $databaseID){
        $data = array( );
        $result = $this->delete("apps/" . $appID . "/databases/" . $databaseID,$data);

        return $result;
    }

    /**
     *  SSL CERTIFICATES
     */

    function addSSLCertificate($name, $key, $certificate){
        $data = array(
            "ssl[name]"=>$name,
            "ssl[key]"=>$key,
            "ssl[data]"=>$certificate
        );
        $result = $this->post("ssl_certificates", $data);

        return $result;
    }

    function getSSLCertificates($limit = 0, $offset = 0, $filter = array()){
        $tmp = array();
        $url = "ssl_certificates";

        if ($limit > 0) {
            $url = $url . "?limit=" . $limit;
        } else {
            $url = $url . "?";
        }

        $url = $url . "&offset=" . $offset;

        if (!empty($filter)) {
            foreach ($filter as $key => $value) {
                $url = $url . "&term[" . $key . "]=" . $value;
            }
        }
        $result = $this->get($url, $tmp);

        return $result;
    }

    function getSSLCertificate($certificateID){
        $tmp = array();
        $result = $this->get("ssl_certificates/" . $certificateID, $tmp);

        return $result;
    }

    function updateSSLCertificate($name, $key, $certificate){
        $data = array(
            "ssl[name]"=>$name,
            "ssl[key]"=>$key,
            "ssl[data]"=>$certificate
        );
        $result = $this->put("ssl_certificates", $data);
        return $result;
    }

    function deleteSSLCertificate($certificateID){
        $data = array( );
        $result = $this->delete("ssl_certificates/" . $certificateID,$data);

        return $result;
    }

    /**
     *  DOMAINS
     */

    function addDomain($appID, $domainName, $managed){
        $data = array(
            "domain[name]"=>$domainName,
            "domain[managed] "=>$managed
        );
        $result = $this->post("apps/" . $appID . "/domains", $data);

        return $result;
    }

    function getDomains($appID, $limit = 0, $offset = 0, $filter = array()){
        $tmp = array();
        $url = "apps/" . $appID . "/domains";

        if ($limit > 0) {
            $url = $url . "?limit=" . $limit;
        } else {
            $url = $url . "?";
        }

        $url = $url . "&offset=" . $offset;

        if (!empty($filter)) {
            foreach ($filter as $key => $value) {
                $url = $url . "&term[" . $key . "]=" . $value;
            }
        }

        $result = $this->get($url, $tmp);

        return $result;
    }

    function getDomain($appID, $domainID){
        $tmp = array();
        $result = $this->get("apps/" . $appID . "/domains/" . $domainID, $tmp);

        return $result;
    }

    function assignSSLCertificate($appID, $domainID, $certificateID){
        $tmp = array();
        $result = $this->get("apps/" . $appID . "/domains/" . $domainID . "/ssl_certificate/" . $certificateID, $tmp);

        return $result;
    }

    function flushVarnishCache($appID, $domainID){
        $tmp = array();
        $result = $this->get("apps/" . $appID . "/domains/" . $domainID . "/flush", $tmp);

        return $result;
    }

    function deleteDomain($appID, $domainID){
        $data = array( );
        $result = $this->delete("apps/" . $appID . "/domains/" . $domainID,$data);

        return $result;
    }

    /**
     *  DNS
     */

    function addDNS($appID, $domainID, $DNSname, $DNSttl, $DNSrtype, $DNSdata){
        $data = array(
            "records[0][name]" => 	$DNSname,
            "records[0][ttl]" => 	$DNSttl,
            "records[0][rtype]" =>	$DNSrtype,
            "records[0][data]" =>	$DNSdata
        );

        $result = $this->post("apps/" . $appID . "/domains/" . $domainID . "/records", $data);

        return $result;
    }

    function getDNS( $domainID, $appID, $limit = 0, $offset = 0, $filter = array()){
        $tmp = array();
        $url = "apps/" . $appID . "/domains/" . $domainID . "/records";

        if ($limit > 0) {
            $url = $url . "?limit=" . $limit;
        } else {
            $url = $url . "?";
        }

        $url = $url . "&offset=" . $offset;

        if (!empty($filter)) {
            foreach ($filter as $key => $value) {
                $url = $url . "&term[" . $key . "]=" . $value;
            }
        }

        $result = $this->get($url, $tmp);

        return $result;
    }

    function updateDNS($domainID, $appID, $DNSID,  $DNSname, $DNSttl, $DNSrtype, $DNSdata){
        $data = array(
            "records[0][name]" => 	$DNSname,
            "records[0][ttl]" => 	$DNSttl,
            "records[0][rtype]" =>	$DNSrtype,
            "records[0][data]" =>	$DNSdata
        );
        $result = $this->put("apps/" . $appID . "/domains/" . $domainID . "/records/" . $DNSID, $data);

        return $result;
    }

    function deleteDNS($appID, $domainID, $DNSID){
        $data = array( );
        $result = $this->delete("apps/" . $appID . "/domains/" . $domainID . "/records/" . $DNSID,$data);

        return $result;
    }


    /**
     *  ADDONS
     */

    function getAddons($limit = 0, $offset = 0, $filter = array()){
        $tmp = array();
        $url = "addons";

        if ($limit > 0) {
            $url = $url . "?limit=" . $limit;
        } else {
            $url = $url . "?";
        }

        $url = $url . "&offset=" . $offset;

        if (!empty($filter)) {
            foreach ($filter as $key => $value) {
                $url = $url . "&term[" . $key . "]=" . $value;
            }
        }

        $result = $this->get($url, $tmp);

        return $result;
    }
}


