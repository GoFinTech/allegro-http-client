<?php

/*
 * This file is part of the Allegro framework.
 *
 * (c) 2019 Go Financial Technologies, JSC
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GoFinTech\Allegro\Http;


use GoFinTech\Allegro\Http\Implementation\ServiceClientProxy;
use ProxyManager\Factory\RemoteObjectFactory;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * HttpServiceClientFactory creates HTTP service client automatically.
 * More specifically, it creates a dynamic interface proxy
 * that uses HttpServiceClient to call methods over HTTP.
 * Wire API matches the JsonServiceHandler on the server side.
 *
 * @package GoFinTech\Allegro\Http
 */
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
