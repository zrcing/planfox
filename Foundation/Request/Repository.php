<?php
/**
 * @author Liao Gengling <liaogling@gmail.com>
 */
namespace Planfox\Foundation\Request;

use Planfox\Foundation\Request\Exception\Exception;

class Repository implements RepositoryInterface
{
    protected $port;
    protected $securePort;
    protected $hostInfo;
    protected $baseUrl;
    protected $scriptUrl;
    protected $requestUri;
    protected $restParams;

    public function __construct()
    {

    }

    public function getParam($name,$defaultValue=null)
    {
        return isset($_GET[$name]) ? $_GET[$name] : (isset($_POST[$name]) ? $_POST[$name] : $defaultValue);
    }

    public function getQuery($name,$defaultValue=null)
    {
        return isset($_GET[$name]) ? $_GET[$name] : $defaultValue;
    }

    public function getPost($name,$defaultValue=null)
    {
        return isset($_POST[$name]) ? $_POST[$name] : $defaultValue;
    }

    public function getPut($name,$defaultValue=null)
    {
        if($this->getIsPutViaPostRequest())
            return $this->getPost($name, $defaultValue);

        if($this->getIsPutRequest())
        {
            $restParams=$this->getRestParams();
            return isset($restParams[$name]) ? $restParams[$name] : $defaultValue;
        }
        else
            return $defaultValue;
    }

    public function getPatch($name,$defaultValue=null)
    {
        if($this->getIsPatchViaPostRequest())
            return $this->getPost($name, $defaultValue);

        if($this->getIsPatchRequest())
        {
            $restParams=$this->getRestParams();
            return isset($restParams[$name]) ? $restParams[$name] : $defaultValue;
        }
        else
            return $defaultValue;
    }

    public function getDelete($name,$defaultValue=null)
    {
        if($this->getIsDeleteViaPostRequest())
            return $this->getPost($name, $defaultValue);

        if($this->getIsDeleteRequest())
        {
            $restParams=$this->getRestParams();
            return isset($restParams[$name]) ? $restParams[$name] : $defaultValue;
        }
        else
            return $defaultValue;
    }

    public function getRestParams()
    {
        if($this->restParams===null)
        {
            $result=array();
            if (strncmp($this->getContentType(), 'application/json', 16) === 0)
                $result = json_decode($this->getRawBody(),true);
            elseif(function_exists('mb_parse_str'))
                mb_parse_str($this->getRawBody(), $result);
            else
                parse_str($this->getRawBody(), $result);
            $this->restParams=$result;
        }

        return $this->restParams;
    }

    public function getContentType()
    {
        if (isset($_SERVER["CONTENT_TYPE"])) {
            return $_SERVER["CONTENT_TYPE"];
        } elseif (isset($_SERVER["HTTP_CONTENT_TYPE"])) {
            //fix bug https://bugs.php.net/bug.php?id=66606
            return $_SERVER["HTTP_CONTENT_TYPE"];
        }
        return null;
    }

    public function getPort()
    {
        if($this->port===null)
            $this->port=!$this->getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 80;
        return $this->port;
    }

    public function getSecurePort()
    {
        if($this->securePort===null)
            $this->securePort=$this->getIsSecureConnection() && isset($_SERVER['SERVER_PORT']) ? (int)$_SERVER['SERVER_PORT'] : 443;
        return $this->securePort;
    }

    public function getIsDeleteViaPostRequest()
    {
        return isset($_POST['_method']) && !strcasecmp($_POST['_method'],'DELETE');
    }

    public function getIsDeleteRequest()
    {
        return (isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'],'DELETE')) || $this->getIsDeleteViaPostRequest();
    }

    public function getIsPutRequest()
    {
        return (isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'],'PUT')) || $this->getIsPutViaPostRequest();
    }

    public function getIsPutViaPostRequest()
    {
        return isset($_POST['_method']) && !strcasecmp($_POST['_method'],'PUT');
    }

    public function getIsPatchViaPostRequest()
    {
        return isset($_POST['_method']) && !strcasecmp($_POST['_method'],'PATCH');
    }

    public function getIsPatchRequest()
    {
        return (isset($_SERVER['REQUEST_METHOD']) && !strcasecmp($_SERVER['REQUEST_METHOD'],'PATCH')) || $this->getIsPatchViaPostRequest();
    }

    /**
     * Return if the request is sent via secure channel (https).
     * @return boolean if the request is sent via secure channel (https)
     */
    public function getIsSecureConnection()
    {
        return isset($_SERVER['HTTPS']) && (strcasecmp($_SERVER['HTTPS'],'on')===0 || $_SERVER['HTTPS']==1)
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strcasecmp($_SERVER['HTTP_X_FORWARDED_PROTO'],'https')===0;
    }

    public function getHostInfo($schema='')
    {
        if($this->hostInfo===null)
        {
            if($secure=$this->getIsSecureConnection())
                $http='https';
            else
                $http='http';
            if(isset($_SERVER['HTTP_HOST']))
                $this->hostInfo=$http.'://'.$_SERVER['HTTP_HOST'];
            else
            {
                $this->hostInfo=$http.'://'.$_SERVER['SERVER_NAME'];
                $port=$secure ? $this->getSecurePort() : $this->getPort();
                if(($port!==80 && !$secure) || ($port!==443 && $secure))
                    $this->hostInfo.=':'.$port;
            }
        }
        if($schema!=='')
        {
            $secure=$this->getIsSecureConnection();
            if($secure && $schema==='https' || !$secure && $schema==='http')
                return $this->hostInfo;

            $port=$schema==='https' ? $this->getSecurePort() : $this->getPort();
            if($port!==80 && $schema==='http' || $port!==443 && $schema==='https')
                $port=':'.$port;
            else
                $port='';

            $pos=strpos($this->hostInfo,':');
            return $schema.substr($this->hostInfo,$pos,strcspn($this->hostInfo,':',$pos+1)+1).$port;
        }
        else
            return $this->hostInfo;
    }

    public function getBaseUrl($absolute=false)
    {
        if($this->baseUrl===null)
            $this->baseUrl=rtrim(dirname($this->getScriptUrl()),'\\/');
        return $absolute ? $this->getHostInfo() . $this->baseUrl : $this->baseUrl;
    }

    public function getScriptUrl()
    {
        if($this->scriptUrl===null)
        {
            $scriptName=basename($_SERVER['SCRIPT_FILENAME']);
            if(basename($_SERVER['SCRIPT_NAME'])===$scriptName)
                $this->scriptUrl=$_SERVER['SCRIPT_NAME'];
            elseif(basename($_SERVER['PHP_SELF'])===$scriptName)
                $this->scriptUrl=$_SERVER['PHP_SELF'];
            elseif(isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME'])===$scriptName)
                $this->scriptUrl=$_SERVER['ORIG_SCRIPT_NAME'];
            elseif(($pos=strpos($_SERVER['PHP_SELF'],'/'.$scriptName))!==false)
                $this->scriptUrl=substr($_SERVER['SCRIPT_NAME'],0,$pos).'/'.$scriptName;
            elseif(isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'],$_SERVER['DOCUMENT_ROOT'])===0)
                $this->scriptUrl=str_replace('\\','/',str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['SCRIPT_FILENAME']));
            else
                throw new Exception('Request is unable to determine the entry script URL.');
        }
        return $this->scriptUrl;
    }

    public function getRawBody()
    {
        static $rawBody;
        if($rawBody===null)
            $rawBody=file_get_contents('php://input');
        return $rawBody;
    }

    public function getUrl()
    {
        return $this->getRequestUri();
    }

    public function getRequestUri()
    {
        if($this->requestUri===null)
        {
            if(isset($_SERVER['HTTP_X_REWRITE_URL'])) // IIS
                $this->requestUri=$_SERVER['HTTP_X_REWRITE_URL'];
            elseif(isset($_SERVER['REQUEST_URI']))
            {
                $this->requestUri=$_SERVER['REQUEST_URI'];
                if(!empty($_SERVER['HTTP_HOST']))
                {
                    if(strpos($this->requestUri,$_SERVER['HTTP_HOST'])!==false)
                        $this->requestUri=preg_replace('/^\w+:\/\/[^\/]+/','',$this->requestUri);
                }
                else
                    $this->requestUri=preg_replace('/^(http|https):\/\/[^\/]+/i','',$this->requestUri);
            }
            elseif(isset($_SERVER['ORIG_PATH_INFO']))  // IIS 5.0 CGI
            {
                $this->requestUri=$_SERVER['ORIG_PATH_INFO'];
                if(!empty($_SERVER['QUERY_STRING']))
                    $this->requestUri.='?'.$_SERVER['QUERY_STRING'];
            }
            else
                throw new Exception('Request is unable to determine the request URI.');
        }

        return $this->requestUri;
    }
}