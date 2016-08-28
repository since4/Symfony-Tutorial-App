<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
// ...
use AppBundle\Entity\Product;
use Symfony\Component\HttpFoundation\Response;


class ProductController extends Controller
{
    
    /**
     * @Route("/product/create", name="product_create")
     */
    public function createAction()
    {
        $product = new Product();
        $product->setName('Keyboard');
        $product->setPrice(19.99);
        $product->setDescription('Ergonomic and stylish!');

        $em = $this->getDoctrine()->getManager();

        // tells Doctrine you want to (eventually) save the Product (no queries yet)
        $em->persist($product);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        return new Response('Saved new product with id '.$product->getId());
    }
    
    
    // dynamic method names to find a single product based on a column value
    /**
     * @Route("/products/show/{columnValue}", name="products_show")
     */
    public function showAction($columnValue)
    {
        
        $repository = $this->getDoctrine()->getRepository('AppBundle:Product');
               
        // query for a single product by its primary key (usually "id")
        $productId=$columnValue;
        $product = $repository->find($productId);
        if ($product) {
            dump($product);
        }
        
        // dynamic method names to find a single product based on a column value
        $productId=$columnValue;
        $product = $repository->findOneById($productId);
        if ($product) {
            dump($product);
        }
        $Name=$columnValue;
        $product = $repository->findOneByName($Name);
        if ($product) {
            dump($product);
        }

        // dynamic method names to find a group of products based on a column value
        $Price=$columnValue;
        $products = $repository->findByPrice($Price);
        if ($products) {
            dump($products);
        }
       
        // ... do something, like pass the $product object into a template
        return new Response('<html><body>Products dumped!</body></html>');       
    }
    
    /**
     * @Route("/products/list", name="products_list")
     */
    public function listAction()
    {
        
        $repository = $this->getDoctrine()->getRepository('AppBundle:Product');

        // find *all* products
        $products = $repository->findAll();
        if ($products) {
            dump($products);
        }
        else{
            throw $this->createNotFoundException(
                'No products found'
            );
        }
       
        // ... do something, like pass the $product object into a template
        return new Response('<html><body>Products listed/dumped!</body></html>');
        
    }
    
    /**
     * @Route("/product/update/{productId}", name="products_update")
     */
    public function updateAction($productId)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle:Product')->find($productId);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$productId
            );
        }

        $product->setName('New product name!');
        $em->flush();
      
        // ... do something, like pass the $product object into a template
        //return $this->redirectToRoute('homepage');
        return new Response('<html><body>Product updated!</body></html>');
        
    }
    
    /**
     * @Route("/product/delete/{productId}", name="product_delete")
     */
    public function deleteAction($productId)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle:Product')->find($productId);

        if (!$product) {
            throw $this->createNotFoundException(
                'No product found for id '.$productId
            );
        }
        $em->remove($product);
        $em->flush();
        
        // ... do something, like pass the $product object into a template
        return new Response('<html><body>Product deleted!</body></html>');
    }
    
    
    /**
     * @Route("/products/query1", name="products_query1")
     */
    public function query1Action()
    {
        /*Take note of the setParameter() method. 
         * When working with Doctrine, 
         * it's always a good idea to set any 
         * external values as "placeholders" 
         * (:price in the example above) 
         * as it prevents SQL injection attacks.
         */
        
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery(
            'SELECT p
            FROM AppBundle:Product p
            WHERE p.price = :price
            ORDER BY p.price ASC'
            )->setParameter('price', 19.99);

        $products = $query->getResult();
        
        // to get just one result:
        // $products = $query->setMaxResults(1)->getOneOrNullResult();
        
        if ($products) {
            dump($products);
        }
        else{
            throw $this->createNotFoundException(
                'No products found'
            );
        }
       
        // ... do something, like pass the $product object into a template
        return new Response('<html><body>Product queried/dumped!</body></html>');
    }
    
    
  
}
