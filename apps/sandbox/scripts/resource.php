<?php
namespace sandbox;

use BEAR\Framework\Module\StandardModule;

use Ray\Di\AbstractModule,
    Ray\Di\InjectorInterface,
    Ray\Di\Annotation,
    Ray\Di\Config,
    Ray\Di\Forge,
    Ray\Di\Container,
    Ray\Di\Injector,
    Ray\Di\Definition;
use BEAR\Resource\SignalHandler\Provides;
// Cache Adapter
use Doctrine\Common\Cache\ApcCache as Cache;
use Guzzle\Common\Cache\DoctrineCacheAdapter as CacheAdapter;

/**
 * Return application dependency injector.
 *
 * @package    sandbox
 * @subpackage script
 *
 * @return BEAR\Resource\Client
 */
$cache = new CacheAdapter(new Cache);
$resourceClientBuilder = function () use ($cache) {
    $annotations = [
        'provides' => 'BEAR\Resource\Annotation\Provides',
        'signal' => 'BEAR\Resource\Annotation\Signal',
        'argsignal' => 'BEAR\Resource\Annotation\ParamSignal',
        'get' => 'BEAR\Resource\Annotation\Get',
        'post' => 'BEAR\Resource\Annotation\Post',
        'put' => 'BEAR\Resource\Annotation\Put',
        'delete' => 'BEAR\Resource\Annotation\Delete',
        ];
    $di = new Injector(new Container(new Forge(new Config(new Annotation(new Definition, $annotations), $cache))));
    $di->setModule(new Module\AppModule(new StandardModule($di, new App)));
    $resource = $di->getInstance('BEAR\Resource\Client');
    /* @var $resource \BEAR\Resoure\Client */
    $resource->attachParamProvider('Provides', new Provides);
    $resource->setCacheAdapter($cache);
    return $resource;
};

$key = __NAMESPACE__ . __FILE__;
$resource = $cache->fetch($key);
if ($resource) {
    return unserialize($resource);
}
$resource = $resourceClientBuilder();
$cache->save($key, serialize($resource));
return $resource;