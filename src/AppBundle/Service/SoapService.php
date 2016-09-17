<?php

namespace AppBundle\Service;

class SoapService
{
    /* @var \SoapClient $client */
    private $client;
    private $param;

    public function __construct($uri, array $param = [])
    {
        $option = [];
        if (!empty($param['version'])) {
            if ($param['version'] == '1.2') {
                $option['soap_version'] = SOAP_1_2;
            } else {
                $option['soap_version'] = SOAP_1_1;
            }
        }
        $this->client = new \SoapClient($uri, $option);

        if (!empty($param['header'])) {
            $this->addHeaders($param['header']);
        }
        $this->param = $param;
    }

    public function call()
    {
        try {
            $arg = $this->param['argType'] == 'object' ? $this->param['object'] : $this->param['array'];

            return $this->client->__soapCall($this->param['function'], $arg);
        } catch (\SoapFault $fault) {
            throw $fault;
        }
    }

    private function addHeaders($headers)
    {
        $SoapHeaders = [];
        foreach ($headers as $header) {
            $SoapHeaders[] = new \SoapHeader(
                $header['namespace'],
                $header['key'],
                $header['value']
            );
        }

        $this->client->__setSoapHeaders($SoapHeaders);
    }

    public function getSoapFunctions()
    {
        $functions = [];
        foreach ($this->client->__getFunctions() as $function) {
            $functionName = $this->parseFunction($function);
            if ($functionName) {
                $functions[] = $this->parseFunction($function);
            }
        }

        return $functions;
    }

    private function parseFunction($function)
    {
        // GetWeatherResponse GetWeather(GetWeather $parameters)
        $pattern = '/(\w+) ([a-zA-Z0-9]+)\((.*)\)/u';
        if (preg_match($pattern, $function, $match)) {
            list($full, $responseType, $functionName, $requestType) = $match;
            return $functionName;
        }

        return null;
    }
}