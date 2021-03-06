<?php

namespace AppBundle\Service;

class SoapService
{
    /* @var \SoapClient $client */
    private $client;
    private $param;

    public function __construct($uri, array $param = [])
    {
        $option = [
            'trace' => 1,
        ];
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
            $arg = $this->buildArgs();

            return $this->client->__soapCall($this->param['function'], $arg);
        } catch (\SoapFault $fault) {
            throw $fault;
        }
    }

    private function buildArgs()
    {
        if ($this->param['arg'] == 'object') {
            $obj = [];
            foreach ($this->param['object']['key'] as $index => $key) {
                $obj[$key] = $this->param['object']['value'][$index];
            }
            return $obj;
        } else {
            return $this->param['array'];
        }
    }

    public function getLastResponse()
    {
        return [
            'header' => $this->client->__getLastRequestHeaders(),
            'body' => $this->client->__getLastResponse(),
        ];
    }

    private function addHeaders($headers)
    {
        $SoapHeaders = [];
        foreach ($headers['namespace'] as $index => $namespace) {
            $SoapHeaders[] = new \SoapHeader(
                $namespace,
                $headers['key'][$index],
                $headers['value'][$index]
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
        sort($functions);
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