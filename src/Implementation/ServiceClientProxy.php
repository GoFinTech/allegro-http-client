<?php


namespace GoFinTech\Allegro\Http\Implementation;


use GoFinTech\Allegro\Http\HttpServiceClientBase;
use LogicException;
use ProxyManager\Factory\RemoteObject\AdapterInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\Serializer\SerializerInterface;

class ServiceClientProxy extends HttpServiceClientBase implements AdapterInterface
{
    /** @var string[] indexed by method name */
    private $returnTypes;

    public function __construct(string $interfaceName, string $endpoint, ?SerializerInterface $serializer)
    {
        parent::__construct($endpoint, $serializer);

        try {
            $interfaceInfo = new ReflectionClass($interfaceName);
            $this->returnTypes = [];
            foreach ($interfaceInfo->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                $this->returnTypes[$method->name] = (string)$method->getReturnType();
            }
        } catch (ReflectionException $ex) {
            throw new LogicException("ServiceClientProxy: can't initialize $interfaceName, {$ex->getMessage()}", 0, $ex);
        }
    }

    public function call(string $wrappedClass, string $method, array $params = [])
    {
        return $this->callService($method, $params[0] ?? null, $this->returnTypes[$method]);
    }
}
