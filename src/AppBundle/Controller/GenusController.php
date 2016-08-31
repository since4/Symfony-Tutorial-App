<?php

namespace AppBundle\Controller;

/*to parse annotations*/
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/*to extend Controller and use render*/
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/*to use Respose and JsonResponse*/
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/*to use Genus.php model and contact db*/
use AppBundle\Entity\Genus;

/*to use GenusNote.php model and contact db*/
use AppBundle\Entity\GenusNote;

/*my own services*/
use AppBundle\Service\MarkdownTransformer;

class GenusController extends Controller {
    /*NB: put your most generic-matching routes near the bottom!*/
    
    
    /*add a new genus: this method is superseded by LoadFixtures.php*/
    /**
     * @Route("/genus/new_old", name="genus_new")
     */
    public function newAction()
    {
        /*create new object from class Entity/Genus.php*/
        $genus = new Genus();
        
        /*set instance variables of Genus object*/
        $genus->setName('Octopus'.rand(1, 100));
        $genus->setSubFamily('Octopodinae');
        $genus->setSpeciesCount(rand(100, 99999));
        
        /*set instance variables of GenusNote object*/
        $note = new GenusNote();
        $note->setUsername('AquaWeaver');
        $note->setUserAvatarFilename('ryan.jpeg');
        $note->setNote('I counted 8 legs... as they wrapped around me');
        $note->setCreatedAt(new \DateTime('-1 month'));
        
        /*set relation: links GenusNote to the new Genus*/
        $note->setGenus($genus);
        
        /*create an entity manager object*/
        $em = $this->getDoctrine()->getManager();
        
        /*save object to database as a new record 
         * in table genus and table genus_note*/
        $em->persist($genus);
        $em->persist($note);
        $em->flush();
        
        /*Skip a template*/
        
        /*A controller must always return? Yep! A Response object. 
         * Enter localhost:8000/genus/new
         * New record will be created in db and
         * the response in the browser is Genus created!
         * Use some html to get the debug toolbar in the browser.
        */
        //return new Response('Genus created!');
        return new Response('<html><body>Genus created!</body></html>');
    }
    
    /*returns a list of all genuses*/
    /**
     * @Route("/genus", name="genus_list")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        /*with die: only this dump is shown in the Browser*/
        //dump($em->getRepository('AppBundle:Genus'));die;
        /*without die: the normal page is shown in the Browser
         *the dump is in the web debug toolbar either
         *via icon on right side or via Debug menu*/
        /* in prod dump generates a 500 internal server error*/
        //dump($em->getRepository('AppBundle:Genus')); 
              
        
        //$genuses = $em->getRepository('AppBundle\Entity\Genus')->findAll();
        //$genuses = $em->getRepository('AppBundle:Genus')->findAll();
        
        //$genuses = $em->getRepository('AppBundle:Genus')
        //        ->findAllPublishedOrderedBySize();
        
        $genuses = $em->getRepository('AppBundle:Genus')
        ->findAllPublishedOrderedByRecentlyActive();
               
        //dump($genuses);die;
        return $this->render('genus/list.html.twig', [
            'genuses' => $genuses]);
    }
    
    /*returns the genus: called via link from list.html.twig*/
    /**
     * @Route("/genus/{genusName}", name="genus_show")
     */
    public function showAction($genusName) {
        /* $templating = $this->container->get('templating');
          $html = $templating->render('genus/show.html.twig', array(
          'name' => $genusName
          ));
          return new Response($html); 
        */


        /* $notes = [
          'Octopus asked me a riddle, outsmarted me',
          'I counted 8 legs... as they wrapped around me',
          'Inked!'
          ];
          return $this->render('genus/show.html.twig', array(
          'name' => $genusName,
          'notes' => $notes)); 
        */
        
        /*
        $funFact = 'Octopuses can change the color of their 
			body in just *three-tenths* of a second!!';
        */
        
        /* $funFact = $this->container->get('markdown.parser')
          ->transform($funFact); */
        /* $funFact = $this->get('markdown.parser')
          ->transform($funFact); */       
        
        $em = $this->getDoctrine()->getManager();
        
        $genus = $em->getRepository('AppBundle:Genus')
            ->findOneBy(['name' => $genusName]);
        
        if (!$genus) {
            throw $this->createNotFoundException('genus not found');
        }
        
        // todo - add the caching back later 
        // now implemented in MarkdownTransformer.php     
//        /*to use the cache for markdown:
//           calculate md5 hash key and check in cache
//           if it exists fetch it 
//           else parse and cache it*/
//        $cache = $this->get('doctrine_cache.providers.my_markdown_cache');
//        $key = md5($funFact);
//        if ($cache->contains($key)) {
//            $funFact = $cache->fetch($key);
//        } else {
//            sleep(1); // fake how slow this could be
//            $funFact = $this->get('markdown.parser')
//                    ->transform($funFact);
//            $cache->save($key, $funFact);
//        }
        
        /*MarkdownTransformer does not live in the container.
         * We need to instantiate it manually: 
         * we can't just say something like 
         * $this->get('app.markdown_transformer') 
         * and expect the container to create it for us.
         * Time to change that... Open up app/config/services.yml
         */
        //$markdownParser = new MarkdownTransformer(
        //        $this->get('markdown.parser')
        //);
        
        /*After configuration in services.yml our own service
            is available from the container with get()
            all necessary objects for the constructor are
            passed automatically according to instructions in
            services.yml*/
        $markdownParser = $this->get('app.markdown_transformer');
        $funFact = $markdownParser->parse($genus->getFunFact());
        
        /*
        return $this->render('genus/show.html.twig', array(
                    'name' => $genusName,
                    'funFact' => $funFact
        ));
        */
        
        /*
        return $this->render('genus/show.html.twig', array(
            'genus' => $genus
        ));
        */
        
        /*getNotes() returns an ArrayCollection object 
         * and it has some tricks on it - like a method for filtering! 
         * Chain a call to the filter() method 
         * and pass this an anonymous function with a GenusNote argument. 
         * The ArrayCollection will call this function for each item. 
         * If we return true, it stays. If we return false, it disappears.
         * Don't Abuse ArrayCollection 
         * Do you see any downsides to this? There's one big one: 
         * this queries for all of the notes, even though we don't need them all.
         * If you may have many notes: don't do this - 
         * you will feel the performance impact of loading up hundreds 
         * of extra objects.
         * Instead make a custom query that only returns 
         * the GenusNote objects we need
         */
        //$recentNotes = $genus->getNotes()
        //    ->filter(function(GenusNote $note) {
        //        return $note->getCreatedAt() > new \DateTime('-3 months');
        //    });
        
        
        /*Now, instead of fetching all the notes just to count some of them, 
         * we're only querying for the ones we need. 
         * And, Doctrine loves returning objects, 
         * but you could make this even faster by returning only the count 
         * from the query, instead of the objects.
         * We cover that in our Going Pro with Doctrine Queries.
         */
        $recentNotes = $em->getRepository('AppBundle:GenusNote')
             ->findAllRecentNotesForGenus($genus);
            
        return $this->render('genus/show.html.twig', array(
            'genus' => $genus,
            'funFact' => $funFact,
            'recentNoteCount' => count($recentNotes)
        ));
        
    }

    /*returns the notes as json API object: called via link from show.html.twig: 
     * Rule: The name of a routing wildcard must match function argument OR must
     * match a property of a function argument object.
     * old version:
     * old Route("/genus/{genusName}/notes", name="genus_show_notes")
     * old public function getNotesAction($genusName)
     * new version:
     * parameter changes implemented below ($name is property of Genus)
     * parameter changes implemented in show.html.twig which calles this link.
     */
    /**
     * @Route("/genus/{name}/notes", name="genus_show_notes")
     * @Method("GET")
     */
    public function getNotesAction(Genus $genus) {
        
        /*We don't see the dump because it's actually an AJAX call - 
         * one that happens automatically each second.
         * Go to /_profiler to see a list of the most recent requests, 
         * including AJAX requests. Select one of these: 
         * this is the profiler for that AJAX call, and in the Debug panel... 
         * there's the dump.
         */
        /* in prod dump generates a 500 internal server error*/
        //dump($genus);
        
        //foreach ($genus->getNotes() as $note) {
        //    dump($note);
        //}
        
        
        /* old version
        $notes = [
            ['id' => 1, 'username' => 'AquaPelham',
                'avatarUri' => '/images/leanna.jpeg',
                'note' => 'Octopus asked me a riddle, 
			outsmarted me', 'date' => 'Dec. 10, 2015'],
            ['id' => 2, 'username' => 'AquaWeaver',
                'avatarUri' => '/images/ryan.jpeg',
                'note' => 'I counted 8 legs... as they wrapped around me',
                'date' => 'Dec. 1, 2015'],
            ['id' => 3, 'username' => 'AquaPelham',
                'avatarUri' => '/images/leanna.jpeg',
                'note' => 'Inked!', 'date' => 'Aug. 20, 2015'],
        ];*/
        
        
        /*PHP Associative Arrays (dictionaries, hash tables)
         * Associative arrays are arrays that use named keys 
         * that you assign to them. (key=>value pairs)*/
        $notes = [];       
        foreach ($genus->getNotes() as $note) {
            $notes[] = [
                'id' => $note->getId(),
                'username' => $note->getUsername(),
                'avatarUri' => '/images/'.$note->getUserAvatarFilename(),
                'note' => $note->getNote(),
                'date' => $note->getCreatedAt()->format('M d, Y')
            ];
        }
              
        $data = [
            'notes' => $notes
        ];
        
        /* in prod dump generates a 500 internal server error*/
        //dump($data);
        
        /* json endpoint API
          the data-structure: $data is written to
          the url of this router,
          where it is fetched from:ReactDOM.render in show.html.twig
          and used in notes.react.js to load it dynamically:
         */
        /* return new Response(json_encode($data)); */
        return new JsonResponse($data);
    }

}
