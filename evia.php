<?php
/**
 * PHP SDK encapsulating cURL communication with bitfront-API.
 *
 * bitfront-SDK consists of HTTP error exceptions, PHP functions encapsulating GET,POST,PUT and DELETE cURL requests,
 * and functions providing all the functionality provided by bitfront-API.
 * NOTES: assignSSLCertificate shouldn't be GET semantically both here and in API, same goes for flush varnish cache.
 *        Get single DNS is missing from API documentation.
 *
 * @package bitfront-SDK
 * @version 1.0
 * @author Jakub Trancik <jakub.trancik [at] relbit [dot] com>
 * @author Adam Poldauf <adam.poldauf [at] relbit [dot] com>
 * @since 1.0
 */


/**
 * Custom exception class for undefined errors.
 *
 * Class extending exception, with __toString function returning "GenericException: $code $message\n"
 */
class GenericException extends Exception
{
    /**
     * Constructor.
     *
     * Constructor calling parent::__construct($message, $code);
     */
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }

    /**
     * Function returning textual interpretation of the error.
     *
     * Function returning textual interpretation of the error - "GenericException: $code $message\n"
     */
    public function __toString() {
        return __CLASS__ . ": {$this->code} {$this->message}" . PHP_EOL;
    }
}

/**
 * Custom exception class for HTTP error 401.
 *
 * Class extending exception, with __toString function always returning "HttpError401Exception: 401 Unauthorized\n"
 */
class HttpError401Exception extends GenericException
{
    /**
     * Constructor.
     *
     * Constructor calling parent::__construct("Unauthorized", 401);
     */
    public function __construct() {
        parent::__construct("Unauthorized", 401);
    }
}

/**
 * Custom exception class for HTTP error 404.
 *
 * Class extending exception, with __toString function always returning "HttpError404Exception: 404 Not Found\n"
 */
class HttpError404Exception extends GenericException
{
    /**
     * Constructor.
     *
     * Constructor calling parent::__construct("Not Found", 404);
     */
    public function __construct() {
        parent::__construct("Not Found", 404);
    }
}

/**
 * Custom exception class for HTTP error 422.
 *
 * Class extending exception, with __toString function always returning "HttpError422Exception: 422 Unprocessable Entity\n"
 */
class HttpError422Exception extends GenericException
{
    /**
     * Constructor.
     *
     * Constructor calling parent::__construct("Unprocessable Entity", 422);
     */
    public function __construct() {
        parent::__construct("Unprocessable Entity", 422);
    }
}

/**
 * Custom exception class for HTTP error 500.
 *
 * Class extending exception, with __toString function always returning "HttpError500Exception: 500 Internal server error\n"
 */
class HttpError500Exception extends GenericException
{
    /**
     * Constructor.
     *
     * Constructor calling parent::__construct("Internal server error", 500);
     */
    public function __construct() {
        parent::__construct("Internal server error", 500);
    }
}

/**
 * Custom exception class for HTTP error 503.
 *
 * Class extending exception, with __toString function always returning "HttpError503Exception: 503 Service Unavailable\n"
 */
class HttpError503Exception extends GenericException
{
    /**
     * Constructor.
     *
     * Constructor calling parent::__construct("Service Unavailable", 503);
     */
    public function __construct() {
        parent::__construct("Service Unavailable", 503);
    }
}

/**
 * Custom exception class for cURL errors.
 *
 * Class extending exception, with __toString function returning "CurlErrorException: $code $message\n"
 */
class CurlErrorException extends GenericException
{
    /**
     * Constructor.
     *
     * Constructor calling parent::__construct($message, $code);
     */
    public function __construct($message, $code) {
        parent::__construct($message, $code);
    }
}


/**
 * Class containing all functionality of bitfront-SDK.
 *
 * Evia class consists PHP functions encapsulating GET,POST,PUT and DELETE cURL requests,
 * and functions encapsulating all the functionality provided by bitfront-API.
 */
class Evia {
    private $email;
    private $password;
    private $baseurl;
    private $verify;

    /**
     * Constructor.
     *
     * Constructor that sets email, password and base url used by API calls.
     *
     * @param string $email
     * @param string $password
     * @param string $baseurl
     */

    function Evia($email, $password, $baseurl){
        $this->email = $email;
        $this->password = $password;
        $this->baseurl = $baseurl;
        $this->verify = true;
    }

    /**
     * Function used to disable certificate verification.
     *
     * Function sets private variable verify to false (default is true), effectively disabling certificate verification in cURL requests.
     *
     */

    function disableVerification(){
        $this->verify = false;
    }

    /**
     * Function used to enable certificate verification.
     *
     * Function sets private variable verify to true (default is true), effectively enabling certificate verification in cURL requests.
     */
    function enableVerification(){
        $this->verify = true;
    }

    /**
     * Function encapsulating cURL POST request.
     *
     * Function POSTs $data to $baseurl/$url and either returns curl_exec() return value,
     * or throws corresponding exception on failure.
     *
     * @param string $url complement of $baseurl containing exact resource location
     * @param string $data data that will be POSTed to $baseurl/$url
     * @return string Returns curl_exec() return value, or throws an exception.
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    private function post($url, $data){
        $defaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL =>  $this->baseurl."/".$url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 40,
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

    /**
     * Function encapsulating cURL GET request.
     *
     * Function GETs $data from $baseurl/$url and either returns curl_exec() return value,
     * or throws corresponding exception on failure.
     *
     * @param string $url complement of $baseurl containing exact resource location
     * @return string Returns curl_exec() return value, or throws an exception.
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    private function get($url, $params = array())
    {
        $defaults = array(
            CURLOPT_URL => $this->baseurl."/".$url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($params),
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

    /**
     * Function encapsulating cURL PUT request.
     *
     * Function PUTs $data to $baseurl/$url and either returns curl_exec() return value,
     * or throws corresponding exception on failure.
     *
     * @param string $url complement of $baseurl containing exact resource location
     * @param string $data data that will be PUT to $baseurl/$url
     * @return string Returns curl_exec() return value, or throws an exception.
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    private function put($url, $data){
        $defaults = array(
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_HEADER => 0,
            CURLOPT_URL =>  $this->baseurl."/".$url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 40,
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

    /**
     * Function encapsulating cURL DELETE request.
     *
     * Function DELETEs data from $baseurl/$url and either returns curl_exec() return value,
     * or throws corresponding exception on failure.
     *
     * @param string $url complement of $baseurl containing exact resource location
     * @return string Returns curl_exec() return value, or throws an exception.
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    private function delete($url){
        $defaults = array(
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HEADER => 0,
            CURLOPT_URL =>  $this->baseurl."/".$url,
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 40,
            CURLOPT_SSL_VERIFYPEER => $this->verify,
            CURLOPT_POSTFIELDS => http_build_query(array()),
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
     * Returns JSON list of all apps.
     *
     * By default it returns all apps, results can be filtered using $limit, $offset, and $filter.
     *
     * @param int $limit maximum number of returned apps.
     * @param int $offset offset of returned apps.
     * @param array $filter array of terms(term[name]=test) which is an array of key ⇒ value pairs for filtering,
     * response contain only resources with name containing “test”
     * @return string By default it returns JSON list of all apps, results can be filtered using $limit, $offset, and $filter.
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
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

    /**
     * Returns JSON list of information about desired app.
     *
     * Returns JSON list of information about app with ID $id or throws an exception.
     *
     * @param int $id ID of the desired app
     * @return string Returns JSON list of information about app with ID $id or throws an exception.
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function getApp($id){
        $tmp = array();
        $result = $this->get("apps/".$id, $tmp);

        return $result;
    }

    /**
     * Adds a new app.
     *
     * Adds a new app with name $name or throws an exception. Creating an app may take up to 30 seconds.
     *
     * @param string $name name of the new app
     * @return string Returns cURL response, or throws an exception
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function addApp($name){
        $data = array(
            "app[name]"=>$name
        );
        $result = $this->post("apps", $data);

        return $result;
    }

    /**
     * Updates an app.
     *
     * Changes app name to $newName or throws an exception.
     *
     * @param int $id ID of the app to update
     * @param string $newName new name of the app
     * @return string Returns cURL response, or throws an exception
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function updateApp($id,$newName){
        $data = array(
            "app[name]"=>$newName/**
     * Updates an app.
     *
     * Changes app name to $newName or throws an exception.
     *
     * @param int $id ID of the app to update
     * @param string $newName new name of the app
     * @return string Returns cURL response, or throws an exception
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
        );
        $result = $this->put("apps/".$id, $data);

        return $result;
    }

    /**
     * Deletes an app.
     *
     * Deletes an app with ID $id or throws an exception.
     *
     * @param int $id ID of the app to delete
     * @return string Returns cURL response, or throws an exception
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function deleteApp($id){
        $result = $this->delete("apps/".$id);

        return $result;
    }

    /**
     * Adds a new database.
     *
     * Adds a new database with name $name, addon_id $addonID, and password $password for application with ID $id, or throws an exception.
     *
     * @param int $appID ID of the parent app
     * @param string $databaseName name of the new database
     * @param int $addonID ID of the addon
     * @param string $password password of the new database
     * @return string Returns cURL response, or throws an exception
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
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

    /**
     * Returns JSON list of all databases of an app.
     *
     * By default it returns all databases of an app with ID $appID, results can be filtered using $limit, $offset, and $filter.
     *
     * @param int $appID ID of the parent app
     * @param int $limit maximum number of returned databases.
     * @param int $offset offset of returned databases.
     * @param array $filter array of terms(term[name]=test) which is an array of key ⇒ value pairs for filtering,
     * response contain only resources with name containing “test”
     * @return string By default it returns JSON list of all databases, results can be filtered using $limit, $offset, and $filter.
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
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

    /**
     * Returns JSON list of information about desired database.
     *
     * Returns JSON list of information about database with ID $databaseID of an app with ID $appID, or throws an exception.
     *
     * @param int $appID ID of the parent app
     * @param int $databaseID ID of the desired database
     * @return string Returns JSON list of information about database with ID $id or throws an exception.
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function getDatabase($appID, $databaseID){
        $tmp = array();
        $result = $this->get("apps/" . $appID . "/databases/" . $databaseID, $tmp);

        return $result;
    }

    /**
     * Updates a database.
     *
     * Changes database password to $newPassword, or throws an exception.
     *
     * @param int $appID ID of the parent app
     * @param int $databaseID ID of the desired database
     * @param string $newPassword new password of the database
     * @return string Returns cURL response, or throws an exception
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function updateDatabase($appID, $databaseID, $newPassword){
        $data = array(
            "db[password]"=>$newPassword
        );
        $result = $this->put("apps/" . $appID . "/databases/" . $databaseID, $data);

        return $result;
    }

    /**
     * Deletes a database.
     *
     * Deletes a database with ID $databaseID of an app with ID $appID,or throws an exception.
     *
     * @param int $appID ID of the parent app
     * @param int $databaseID ID of the database to delete
     * @return string Returns cURL response, or throws an exception
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function deleteDatabase($appID, $databaseID){
        $data = array( );
        $result = $this->delete("apps/" . $appID . "/databases/" . $databaseID,$data);

        return $result;
    }

    /**
     * Adds a new SSL certificate.
     *
     * Adds a new SSL certificate with given parameters or throws an exception.
     *
     * @param string $name name of the new SSL certificate
     * @param string $key new key
     * @param string $certificate new certificate
     * @return string Returns cURL response, or throws an exception
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
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

    /**
     * Returns JSON list of all SSL certificates.
     *
     * By default it returns all SSL certificates, results can be filtered using $limit, $offset, and $filter.
     *
     * @param int $limit maximum number of returned SSL certificates.
     * @param int $offset offset of returned SSL certificates.
     * @param array $filter array of terms(term[name]=test) which is an array of key ⇒ value pairs for filtering,
     * response contain only resources with name containing “test”
     * @return string By default it returns JSON list of all SSL certificates, results can be filtered using $limit, $offset, and $filter.
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
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

    /**
     * Returns JSON list of information about desired SSL certificate.
     *
     * Returns JSON list of information about SSL certificate with ID $certificateID or throws an exception.
     *
     * @param int $certificateID ID of the desired SSL certificate
     * @return string Returns JSON list of information about SSL certificate with ID $certificateID or throws an exception.
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function getSSLCertificate($certificateID){
        $tmp = array();
        $result = $this->get("ssl_certificates/" . $certificateID, $tmp);

        return $result;
    }

    /**
     * Updates a SSL certificate.
     *
     * Changes SSL certificate name to $name, key to $key, and certificate to $certificate, or throws an exception.
     *
     * @param string $name new name of the SSL certificate
     * @param string $key new key of the SSL certificate
     * @param string $certificate new certificate of the SSL certificate
     * @return string Returns cURL response, or throws an exception
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function updateSSLCertificate($certificateID, $name, $key, $certificate){
        $data = array(
            "ssl[name]"=>$name,
            "ssl[key]"=>$key,
            "ssl[data]"=>$certificate
        );
        $result = $this->put("ssl_certificates/" . $certificateID, $data);
        return $result;
    }

    /**
     * Deletes a SSL certificate.
     *
     * Deletes a SSL certificate with ID $certificateID or throws an exception.
     *
     * @param int $certificateID ID of the SSL certificate to delete
     * @return string Returns cURL response, or throws an exception
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function deleteSSLCertificate($certificateID){
        $data = array( );
        $result = $this->delete("ssl_certificates/" . $certificateID,$data);

        return $result;
    }

    /**
     * Adds a new domain.
     *
     * Adds a new domain with name $domainName and managed set to $managed, for app with ID $appID, or throws an exception.
     *
     * @param int $appID ID of the parent app
     * @param string $domainName name of the new domain
     * @param string $managed managed parameter of the new domain
     * @return string Returns cURL response, or throws an exception
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function addDomain($appID, $domainName, $managed){
        $data = array(
            "domain[name]"=>$domainName,
            "domain[managed]"=>$managed
        );
        $result = $this->post("apps/" . $appID . "/domains", $data);

        return $result;
    }

    /**
     * Returns JSON list of all domains of an app.
     *
     * By default it returns all domains of an app with ID $appID, results can be filtered using $limit, $offset, and $filter.
     *
     * @param int $appID ID of the parent app
     * @param int $limit maximum number of returned domains.
     * @param int $offset offset of returned domains.
     * @param array $filter array of terms(term[name]=test) which is an array of key ⇒ value pairs for filtering,
     * response contain only resources with name containing “test”
     * @return string By default it returns JSON list of all domains, results can be filtered using $limit, $offset, and $filter.
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
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

    /**
     * Returns JSON list of information about desired domain.
     *
     * Returns JSON list of information about domain with ID $domainID of an app with ID $appID, or throws an exception.
     *
     * @param int $appID ID of the parent app
     * @param int $domainID ID of the desired domain
     * @return string Returns JSON list of information about domain with ID $domainID or throws an exception.
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function getDomain($appID, $domainID){
        $tmp = array();
        $result = $this->get("apps/" . $appID . "/domains/" . $domainID, $tmp);

        return $result;
    }

    /**
     * Assigns an SSL certificate to a domain.
     *
     * Assigns an SSL certificate with ID $certificateID to a domain with ID $domainID of an app with ID $appID, or throws an exception.
     *
     * @param int $appID ID of the parent app
     * @param string $domainID ID of the domain
     * @param string $certificateID ID of the certificate
     * @return string Returns cURL response, or throws an exception
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function assignSSLCertificate($appID, $domainID, $certificateID){
        $tmp = array();
        $result = $this->get("apps/" . $appID . "/domains/" . $domainID . "/ssl_certificate/" . $certificateID . "/assign", $tmp);

        return $result;
    }

    /**
     * Flushes the varnish cache of a domain.
     *
     * Flushes the varnish cache of a domain with ID $domainID of an app with ID $appID, or throws an exception.
     *
     * @param int $appID ID of the parent app
     * @param string $domainID ID of the domain
     * @param string $url url regexp
     * @return string Returns cURL response, or throws an exception
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function flushVarnishCache($appID, $domainID, $url = ""){
        $tmp = array("url" => $url);
        $result = $this->get("apps/" . $appID . "/domains/" . $domainID . "/flush", $tmp);

        return $result;
    }

    /**
     * Flushes the varnish addon ~> whole varnish cache of all apps.
     *
     * @return string Returns cURL response, or throws an exception
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function flushVarnishAddon(){
        $result = $this->get("vns/flush");

        return $result;
    }

    /**
     * Deletes a domain.
     *
     * Deletes a domain with ID $domainID of an app with ID $appID, or throws an exception.
     *
     * @param int $appID ID of the parent app
     * @param string $domainID ID of the domain
     * @return string Returns cURL response, or throws an exception
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function deleteDomain($appID, $domainID){
        $data = array( );
        $result = $this->delete("apps/" . $appID . "/domains/" . $domainID,$data);

        return $result;
    }

    /**
     * Adds a new DNS.
     *
     * Adds a new DNS (with attributes set to function arguments) to a domain with ID $domainID of an app with ID $appID, or throws an exception.
     *
     * @param int $appID ID of the parent app
     * @param int $domainID name of the new domain
     * @param array $records
     *   array(
     *           0 => array(
     *                   "name"  => "auto",
     *                   "ttl"   => 3600,
     *                   "rtype" => "A",
     *                   "data"  => "10.1.1.1"
     *           ),
     *           1 => array(
     *                   "name"  => "autobus",
     *                   "ttl"   => 3600,
     *                   "rtype" => "A",
     *                   "data"  => "10.1.1.2"
     *           )
     *   );
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function addDNS($appID, $domainID, $records){
        $data = array();

        foreach($records as $id => $r) {
                $data["records[$id][name]"]  = $r["name"];
                $data["records[$id][ttl]"]   = $r["ttl"];
                $data["records[$id][rtype]"] = $r["rtype"];
                $data["records[$id][data]"]  = $r["data"];
        }

        $result = $this->post("apps/" . $appID . "/domains/" . $domainID . "/records", $data);

        return $result;
    }

    /**
     * Returns JSON list of information about desired DNS.
     *
     * Returns JSON list of information about DNS of a domain with ID $domainID of an app with ID $appID, or throws an exception.
     *
     * @param int $domainID ID of the parent domain
     * @param int $appID ID of the parent app
     * @param int $limit maximum number of returned apps.
     * @param int $offset offset of returned apps.
     * @param array $filter array of terms(term[name]=test) which is an array of key ⇒ value pairs for filtering,
     * response contain only resources with name containing “test”
     * @return string Returns JSON list of information about desired DNS, or throws an exception.
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function getDNSs($appID, $domainID, $limit = 0, $offset = 0, $filter = array()){
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

    /**
     * Returns JSON list of information about desired DNS.
     *
     * Returns JSON list of information about DNS of a domain with ID $domainID of an app with ID $appID, or throws an exception.
     *
     * @param int $domainID ID of the parent domain
     * @param int $appID ID of the parent app
     * @param int $DNSID ID of the desired DNS
     * @return string Returns JSON list of information about desired DNS, or throws an exception.
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function getDNS($appID, $domainID, $DNSID){
        $tmp = array();
        $url = "apps/" . $appID . "/domains/" . $domainID . "/records/" . $DNSID;

        $result = $this->get($url, $tmp);

        return $result;
    }

    /**
     * Updates a DNS.
     *
     * Changes DNS name, ttl, rtype, and data to new values, or throws an exception.
     *
     * @param int $appID ID of the parent app
     * @param int $domainID name of the parent domain
     * @param int $DNSID name of the DNS
     * @param string $DNSname name for host
     * @param string $DNSttl time to live
     * @param string $DNSrtype record type [A, AAAA, MX, CNAME, NS, TXT, SRV]
     * @param string $DNSdata record data
     * @return string Returns cURL response, or throws an exception
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function updateDNS($appID, $domainID, $DNSID,  $DNSname, $DNSttl, $DNSrtype, $DNSdata){
        $data = array(
            "record[name]" =>   $DNSname,
            "record[ttl]" =>    $DNSttl,
            "record[rtype]" =>  $DNSrtype,
            "record[data]" =>   $DNSdata
        );
        $result = $this->put("apps/" . $appID . "/domains/" . $domainID . "/records/" . $DNSID, $data);

        return $result;
    }

    /**
     * Deletes a DNS.
     *
     * Deletes a DNS with ID $DNSID of a domain with ID $domainID of an app with ID $appID, or throws an exception.
     *
     * @param int $appID ID of the parent app
     * @param string $domainID ID of the domain
     * @param int $DNSID name of the DNS
     * @return string Returns cURL response, or throws an exception
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
     */
    function deleteDNS($appID, $domainID, $DNSID){
        $data = array( );
        $result = $this->delete("apps/" . $appID . "/domains/" . $domainID . "/records/" . $DNSID,$data);

        return $result;
    }

    /**
     * Returns JSON list of all addons.
     *
     * By default it returns all addons, results can be filtered using $limit, $offset, and $filter.
     *
     * @param int $limit maximum number of returned addons.
     * @param int $offset offset of returned addons.
     * @param array $filter array of terms(term[name]=test) which is an array of key ⇒ value pairs for filtering,
     * response contain only resources with name containing “test”
     * @return string By default it returns JSON list of all addons, results can be filtered using $limit, $offset, and $filter.
     * @throws HttpError401Exception
     * @throws HttpError404Exception
     * @throws HttpError422Exception
     * @throws HttpError500Exception
     * @throws HttpError503Exception
     * @throws CurlErrorException
     * @throws GenericException
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

/**
 * Custom exception class for Missing argument error, used in CLI mode.
 *
 */
class MissingArgumentExpetion extends GenericException
{

    public function __construct($method) {
        parent::__construct("Missing argument for method " . $method);
    }
}

/**
 * Custom exception class for Missing argument error, used in CLI mode.
 *
 */
class MissingCredentialsExpetion extends GenericException
{

    public function __construct() {
        parent::__construct("Missing Api Credentials.");
    }
}


/*
 * Function do determine CLI mode usage
 */
function isCLI(){
    return (php_sapi_name() === 'cli' OR defined('STDIN'));
}


/*
 * Function to parse method arguments in CLI mode. Decodes array arguments passed as JSON strings.
 */
function parseParams($api, $funcName, $argv){
    $reflector = new ReflectionClass($api);
    $method = $reflector->getMethod($funcName);
    $parameters = $method->getParameters();

    $i = 0;
    $requiredArgumentCount = 0;
    foreach($parameters as $param){
        if ($param->isOptional()){
            if(is_array($param->getDefaultValue()) && (count($argv) > $i)){
                $argv[$i] = json_decode($argv[$i], true);
            }
        }
        else
        {
            $requiredArgumentCount++;
        }    
        $i++;
    }

    if(count($argv) < $requiredArgumentCount){
        throw new MissingArgumentExpetion($funcName);    
    }

    return $argv;
}


/*
 * Parse argumenets of script in CLI mode.
 * 
 * -h or --help for options with no value
 * -e=value or --email=value for options with value
 */
function parseArguments($argv)
{
    // position [0] is the script's file name
    array_shift($argv);
    $out = array();
    foreach($argv as $arg)
    {
        if(substr($arg, 0, 2) == '--')
        {
            $eqPos = strpos($arg, '=');
            if($eqPos === false)
            {
                $key = substr($arg, 2);
                $out[$key] = isset($out[$key]) ? $out[$key] : true;
            }
            else
            {
                $key = substr($arg, 2, $eqPos - 2);
                $out[$key] = substr($arg, $eqPos + 1);
            }
            $out["params"] = array();
        }
        else if(substr($arg, 0, 1) == '-')
        {
            if(substr($arg, 2, 1) == '=')
            {
                $key = substr($arg, 1, 1);
                $out[$key] = substr($arg, 3);
            }
            else
            {
                $chars = str_split(substr($arg, 1));
                foreach($chars as $char)
                {
                    $key = $char;
                    $out[$key] = isset($out[$key]) ? $out[$key] : true;
                }
            }
            $out["params"] = array();
        }
        else
        {
            $out["params"][] = $arg;
        }
    }
    return $out;
}

/*
 * Method prints one formatted line of scipt options.
 */
function printOptionLine($short, $long, $desc){
    fwrite(STDOUT, "\t". $short .", ". $long ."\t\t". $desc . PHP_EOL);
}

/*
 * Method prints scripts help. -h or --help
 */
function printHelp(){
    fwrite(STDOUT, "bitfront-API CLI". PHP_EOL);
    
    fwrite(STDOUT, "SYNTAX:". PHP_EOL);
    fwrite(STDOUT, "\tevia.php OPTIONS METHOD [PARAMS]" . PHP_EOL);
    
    fwrite(STDOUT, "USAGE EXAMPLE:". PHP_EOL);
    fwrite(STDOUT, "\tphp evia.php -e=account@prg0.relbitapp.com --password=topsecret -u=https://api.prg0.relbitapp.com getApps". PHP_EOL);
    fwrite(STDOUT, PHP_EOL);
    fwrite(STDOUT, "\tphp evia.php -e=account@prg0.relbitapp.com --password=topsecret -u=https://api.prg0.relbitapp.com addApp test_app". PHP_EOL);

    fwrite(STDOUT, "OPTIONS:". PHP_EOL);    
    printOptionLine("-h", "--help", "Prints this help");
    printOptionLine("-e", "--email", "API login email");
    printOptionLine("-p", "--password", "API password");
    printOptionLine("-u", "--url", "API base url");
}

/*
 * Returns value from $arguments array by $key1 or $key2.
 */
function getFromArguments($arguments, $key1, $key2){
    if(isset($arguments[$key1]))
    {
        return $arguments[$key1];
    }
    else if(isset($arguments[$key2]))
    {
        return $arguments[$key2];
    }
    else
    {
        return false;
    }
}


if(isCLI()){  
    try
    {
        $arguments = parseArguments($argv);
        
        if(getFromArguments($arguments, "h", "help")){
            printHelp();
        }
        else
        {
            $email = getFromArguments($arguments, "e", "email");
            $password = getFromArguments($arguments, "p", "password");
            $baseurl = getFromArguments($arguments, "u", "url");
            if(!($email && $password && $baseurl)){
                throw new MissingCredentialsExpetion(); 
            }
            $api = new Evia($email, $password, $baseurl);
            $funcName = array_shift($arguments["params"]);
            $params = parseParams($api, $funcName, $arguments["params"]);
            
            fwrite(STDOUT, call_user_func_array(array($api, $funcName), $params));
        }
    }
    catch (Exception $e) {
        fwrite(STDERR, 'Exception: ' . $e->getMessage() . ". Use -h for help." . PHP_EOL);
    }
}
