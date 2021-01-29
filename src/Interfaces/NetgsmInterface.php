<?php

namespace TCGunel\Netgsm\Interfaces;

interface NetgsmInterface{

    /**
     * Uses service type config option to send mesage.
     *
     * @throws \Illuminate\Http\Client\RequestException|\Exception
     */
    public function send(): string;

    /**
     * Make request with HTTP GET method.
     * Can be used directly to ignore service type config.
     *
     * @throws \Illuminate\Http\Client\RequestException|\Exception
     */
    public function sendWithHttp(): string;

    /**
     * Make request with SOAP.
     * Can be used directly to ignore service type config.
     *
     * @throws \Exception
     */
    public function sendWithSoap(): string;

    /**
     * Make request as TEXT/XML with HTTP POST request.
     * Can be used directly to ignore service type config.
     *
     * @throws \Illuminate\Http\Client\RequestException|\Exception
     */
    public function sendWithXml(): string;

    /**
     * @return string
     */
    public function getXml(): string;

    /**
     * Generates an array from $values_to_send similar to XML structure.
     *
     * @return array[]
     */
    public function prepareXmlData(): array;

    /**
     * Returns an XML string created by XMLWriter.
     *
     * @param $xml_array
     * @return string
     */
    public function outputXml($xml_array): string;

    public function calculateMessageLength(string $service, $messages, $encoding = null);
}
