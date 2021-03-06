<?php

namespace ByJG\WebService;

/**
 * Classe Base com todas as funcionalidades para acessar o serviço ByJG
 */
abstract class BaseService
{
    protected $URL         = "http://www.byjg.com.br/site/webservice.php/ws/";
    protected $_username   = "";
    protected $_password   = "";
    protected $_service    = "";
    protected $_curlParams = [];

    /**
     *
     * @param string $username
     * @param string $password
     */
    public function __construct($username, $password, $curlParams = [CURLOPT_TIMEOUT => 5])
    {
        $this->_username   = $username;
        $this->_password   = $password;
        $this->_curlParams = $curlParams;
    }

    protected function conectarServer($httpmethod, $params)
    {
        $params["httpmethod"] = $httpmethod;
        $params["usuario"]    = $this->_username;
        $params["senha"]      = $this->_password;

        $url = $this->URL . $this->_service;

        $webRequest = new \ByJG\Util\WebRequest($url);

        foreach ($this->_curlParams as $param => $value) {
            $webRequest->setCurlOption($param, $value);
        }

        $response = $webRequest->post($params);

        $firstData = explode('|', $response);
        $result    = array(
            'status' => $firstData[0],
            'raw'    => $response
        );

        if (isset($firstData[1])) {
            $parsedData = explode(', ', $firstData[1]);

            if (!isset($parsedData[1])) {
                $parsedData[0] = null;
                $parsedData[1] = $firstData[1];
            }

            $result['data'] = array(
                "code" => $parsedData[0],
                "info" => $parsedData[1]
            );
        }

        return $result;
    }
}
