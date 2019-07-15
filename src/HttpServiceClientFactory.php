<?php


namespace GoFinTech\Allegro\Http;


use GoFinTech\Allegro\Http\Implementation\ServiceClientProxy;
use ProxyManager\Factory\RemoteObjectFactory;
use Symfony\Component\Serializer\SerializerInterface;

class HttpServiceClientFactory
{
    /** @var SerializerInterface|null */
    private $serializer;

    public function __construct(?SerializerInterface $serializer = null)
    {
        $this->serializer = $serializer;
    }

    public function newClient(string $interfaceName, string $endpoint)
    {
        $client = new ServiceClientProxy($interfaceName, $endpoint, $this->serializer);
        $factory = new RemoteObjectFactory($client);
        return $factory->createProxy($interfaceName);
    }
}
