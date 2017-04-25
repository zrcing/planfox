<?php
/**
 * @author Liao Gengling <liaogling@gmail.com>
 */
namespace Planfox\Foundation\Request;

interface RepositoryInterface
{
    public function getParam($name,$defaultValue=null);

    public function getQuery($name,$defaultValue=null);

    public function getPost($name,$defaultValue=null);

    public function getPut($name,$defaultValue=null);

    public function getPatch($name,$defaultValue=null);

    public function getDelete($name,$defaultValue=null);

    public function getRestParams();

    public function getContentType();

    public function getPort();

    public function getSecurePort();

    public function getIsDeleteViaPostRequest();

    public function getIsDeleteRequest();

    public function getIsPutRequest();

    public function getIsPutViaPostRequest();

    public function getIsPatchViaPostRequest();

    public function getIsPatchRequest();

    public function getIsSecureConnection();

    public function getHostInfo($schema='');

    public function getBaseUrl($absolute=false);

    public function getScriptUrl();

    public function getRawBody();

    public function getUrl();

    public function getRequestUri();
}