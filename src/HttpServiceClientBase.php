<?php


namespace GoFinTech\Allegro\Http;


use GoFinTech\Serializer\SerializerFactory;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;
use Symfony\Component\Serializer\SerializerInterface;

class HttpServiceClientBase
{
    /** @var GuzzleClient */
    protected $httpClient;
    /** @var SerializerInterface */
    protected $serializer;

    public function __construct(string $endpoint, ?SerializerInterface $serializer = null)
    {
        $this->httpClient = new GuzzleClient([
            'base_uri' => $endpoint ?? 'http://internal-history-svc/'
        ]);
        $this->serializer = $serializer ?? SerializerFactory::create();
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
        try {
            $resp = $this->httpClient->request($method, $uri, $options);
        } catch (GuzzleException $e) {
            $me = get_class($this);
            throw new RuntimeException("Service call to $uri from $me failed: {$e->getMessage()}", 0, $e);
        }
        if (empty($responseClass)) {
            return null;
        }
        return $this->serializer->deserialize($resp->getBody()->getContents(), $responseClass, 'json');
    }
}
