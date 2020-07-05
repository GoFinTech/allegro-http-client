<?php

/*
 * This file is part of the Allegro framework.
 *
 * (c) 2019, 2020 Go Financial Technologies, JSC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GoFinTech\Allegro\Http;


use GoFinTech\Serializer\SerializerFactory;
use GuzzleHttp\Client as GuzzleClient;
use Symfony\Component\Serializer\SerializerInterface;

class HttpServiceClientBase
{
    /** @var GuzzleClient */
    protected $httpClient;
    /** @var SerializerInterface */
    protected $serializer;
    /** @var array */
    protected $options;

    public function __construct(string $endpoint, ?SerializerInterface $serializer = null, array $options = [])
    {
        $this->httpClient = new GuzzleClient([
            'base_uri' => $endpoint
        ] + $options);
        $this->serializer = $serializer ?? SerializerFactory::create();
        $this->options = $options;
    }

    /**
     * @param string $uri
     * @param mixed $request
     * @param string $responseClass
     * @return mixed
     */
    protected function callService(string $uri, $request, $responseClass = null)
    {
        $options = [];
        $headers = [];
        if (is_null($request)) {
            $method = 'GET';
        }
        else {
            $method = 'POST';
            $headers['Content-Type'] = 'application/json';
            $options['body'] = $this->serializer->serialize($request, 'json');
        }
        if (!empty($responseClass)) {
            $headers['Accept'] = 'application/json';
        }
        $options['headers'] = $headers;
        // Silencing exception warning since we do not want to influence caller definitions
        /** @noinspection PhpUnhandledExceptionInspection */
        $resp = $this->httpClient->request($method, $uri, $options);
        if (empty($responseClass) || $responseClass == 'void') {
            return null;
        }
        return $this->serializer->deserialize($resp->getBody()->getContents(), $responseClass, 'json');
    }
}
