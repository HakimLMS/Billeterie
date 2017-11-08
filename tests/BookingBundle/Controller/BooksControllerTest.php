<?php

namespace P4\BookingBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BooksControllerTest extends WebTestCase
{   
    
        public function testInitForm()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/book');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /book");
        $this->assertTrue($crawler->filter("form")->count() > 1, "Formulaires mis en place");    
        
    }
    
    
    
    
    
    
    
    
    
    
    //test fonc liens
    public function testnavbar()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /");
        
        
        $crawler = $client->click($crawler->selectLink('Accueil')->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'Accueil is off');
                   
        
        $crawler = $client->click($crawler->selectLink('Reservation')->link());
        $this->assertEquals(200, $client->getResponse()->getStatusCode(),'Reservation is off');
                  

    }
    
    //test connexion aux pages publiques sans var nécessaires
     public function testPageIsSuccessful()
    {
        $client = self::createClient();
        $url = $this->urlProvider();
        foreach($url as $singleUrl){
        $client->request('GET', $singleUrl);
        $this->assertTrue($client->getResponse()->isSuccessful());}
    }

       public function urlProvider()
    {
        return array(
            '/',
            '/book',
            // ...
        );
    }
    
}
