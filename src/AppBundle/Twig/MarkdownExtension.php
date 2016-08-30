<?php

namespace AppBundle\Twig;

/*to use the type MarkdownTransformer*/
use AppBundle\Service\MarkdownTransformer;

/*markdownify
 * Let's create our own Twig filter.
 * To do that, you need a Twig "extension" - 
 * that's basically Twig's plugin system.
 * Create a class and make it extend 
 * \Twig_Extension and then fill in some methods.
 * The new service has to be configured in:
 * services.yml
 */
class MarkdownExtension extends \Twig_Extension
{
    /* Dependency injection
     * We're inside a service and need access to some other service: 
     * MarkdownTransformer. */
    private $markdownTransformer;
    public function __construct(MarkdownTransformer $markdownTransformer)
    {
        $this->markdownTransformer = $markdownTransformer;
    }
    
    /*markdownify
     * you'll return an array of new filters: 
     * each is described by a new \Twig_SimpleFilter object. 
     * The first argument will be the filter name - 
     * how about markdownify - 
     * it will be used in show.html.twig. 
     * Then, point to a function in this class 
     * that should be called when that filter is used: 
     * parseMarkdown
     * 
     * The HTML is still being escaped  
     * We could add the |raw filter… but let’s do something cooler. 
     * Add a third argument to Twig_SimpleFilter: 
     * an options array. 
     * Add is_safe set to an array containing html
     * This means it’s always safe to output contents 
     * of this filter in HTML
     */
    public function getFilters()
    {
        return [ new \Twig_SimpleFilter(
                    'markdownify', 
                    array($this, 'parseMarkdown'), 
                    ['is_safe' => ['html']]
                )
        ];
    }
    
    public function parseMarkdown($str)
    {
        //return strtoupper($str);
        return $this->markdownTransformer->parse($str);
    }
    
    public function getName()
    {
        return 'app_markdown';
    }

    
}

