<?php namespace App\Library;

class XapiProvider{

    private $headers;
    private $response;
    const PROVIDER_URL = 'https://xap.ix-io.net/api/v1/';

    public function __construct()
    {
        $this->headers = array(
            "Accept"        => "application/json",
            "Authorization" => "ab16_Fru:8401dG7jyYJxXirqKI3DvSz094f5g96a",
        );

        $this->url = self::PROVIDER_URL;
    }

    public function addParams($params)
    {
        $this->url = $this->url . '?' . http_build_query($params);
        return $this;
    }

    public function buildUrl($name, $service = 'airberlin_lab_2016')
    {
        $this->url = $this->url . $service  . '/' . $name;
        return $this;
    }

    public function execute()
    {
        $this->response = '';
        $response = \Httpful\Request::get($this->url)
            ->addHeaders($this->headers)
            ->send();

        $this->response = $response->body;
        return $response->body;
    }

    public function setUrl(){


    }



}