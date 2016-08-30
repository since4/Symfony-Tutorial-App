<?php

namespace AppBundle\Service;

/* to use the interface type MarkdownParserInterface*/
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;

/* to use the type Cache */
use Doctrine\Common\Cache\Cache;

/* MarkdownTransformer is a service. 
 * Because remember, a service is just a class that does work for us. 
 * And when you isolate a lot of your code into these service classes, 
 * you start to build what's called a "service-oriented architecture". 
 * That basically means that instead of having all of your code 
 * in big controllers, you organize them into nice little services 
 * that each do one job. */

class MarkdownTransformer {
    
    /* how can we get access to the markdown parser object 
     * inside MarkdownTransformer? 
     * The answer is: dependency injection.
     * Here's how it goes: 
     * whenever you're inside of a class 
     * and you need access to an object that you don't have - 
     * like the markdown parser - 
     * add a public function __construct() 
     * and add the object you need as an argument:
     * We're done! Well, done with this class. 
     * You see: whoever instantiates our MarkdownTransformer 
     * will now be forced to pass in a markdown parser object.
     * 
     * Type Hint:
     * php bin/console debug:container markdown
     * Service ID         markdown.parser                              
     * Class              Knp\Bundle\MarkdownBundle\Parser\Preset\Max
     * going into the class Max reveals:
     * class Max extends MarkdownParser
     * going into the class MarkdownParser reveals:
     * class MarkdownParser extends MarkdownExtra implements MarkdownParserInterface
     * MarkdownParserInterface:
     * Why is this the best option? Two small reasons. 
     * First, in theory, we could swap out the $markdownParser 
     * for a different object, as long as it implemented this interface. 
     * Second, it's really clear what methods we can call on the 
     * $markdownParser property: only those on that interface.
     * vendor/knplabs/knp-markdown-bundle/MarkdownParserInterface.php reveals:
     * it has only one method: transformMarkdown():
     * 
     * Take home message:
     * when you need an object from inside a class, 
     * use dependency injection. 
     * And when you add the __construct() argument, 
     * type-hint it with either the class you see in 
     * debug:container or an interface if you can find one. 
     * Both totally work.
     * 
     * Same story for the Cache Service:
     * php bin/console debug:container doctrine_cache.providers.my_markdown_cache
     * Service ID         doctrine_cache.providers.my_markdown_cache
     * Class              Doctrine\Common\Cache\ArrayCache 
     * This service is an instance of ArrayCache. 
     * But wait! Do not type-hint that. 
     * In our earlier course on environments, 
     * we setup a cool system that uses ArrayCache in the dev environment 
     * and FilesystemCache in prod:
     * app/config/config.yml:
     * parameters:
     *       locale: en
     *       cache_type: file_system
     * doctrine_cache:
     *       providers:
     *           my_markdown_cache:
     *               type: %cache_type%
     *               file_system:
     *                   directory: %kernel.cache_dir%/markdown_cache
     * app/config/config_dev.yml
     * parameters:
     *       # array is a "fake" cache: it won't ever store anything.
     *       cache_type: array
     * If we type-hint with ArrayCache, 
     * this will explode in prod because this service 
     * will be a different class.
     * vendor/doctrine/cache/lib/Doctrine/Common/Cache/ArrayCache.php
     *      class ArrayCache extends CacheProvider
     * vendor/doctrine/cache/lib/Doctrine/Common/Cache/CacheProvider.php
     *      abstract class CacheProvider implements Cache, FlushableCache, ClearableCache, MultiGetCache
     */
    private $markdownParser;    
    private $cache;
    public function __construct(
            MarkdownParserInterface $markdownParser, 
            Cache $cache)
    {
        $this->markdownParser = $markdownParser;
        $this->cache = $cache;
    }
    
    public function parse($str)
    {
        /* makes capital letters */
        //return strtoupper($str);

        /* this does throw a 500 ise, get() undefined*/
        //return $this->get('markdown.parser')->transform($str);
        
        /* this works because of __construct()*/
        //return $this->markdownParser->transform($str);
        //return $this->markdownParser->transformMarkdown($str);
        
        /*moved the caching here from GenusController.php:*/
        /*to use the cache for markdown:
          calculate md5 hash key and check in cache
          if it exists fetch and return it 
          else parse, cache and return it*/
        /*we do not have access here to container->get()*/
        //$cache = $this->get(
        //        'doctrine_cache.providers.my_markdown_cache'
        //);
        $cache = $this->cache;
        $key = md5($str);
        if ($cache->contains($key)) {
            return $cache->fetch($key);
        }
        sleep(1);
        $str = $this->markdownParser
            ->transformMarkdown($str);
        $cache->save($key, $str);
        return $str;
}

}
