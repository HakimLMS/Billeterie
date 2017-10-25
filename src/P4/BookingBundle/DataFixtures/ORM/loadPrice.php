<?php
 namespace P4\BookingBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use P4\Bookingbundle\Entity\Price;

class loadPrice implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $prices = array(
            array ('type' => '4', 'amount' => 0, 'name' =>'baby'),
            array ('type' => '12', 'amount' => 8, 'name' =>'young' ),
            array ('type' => '60', 'amount' => 16, 'name' =>'normal' ),
            array ('type' => '+60' , 'amount' => 12, 'name' =>'3rd age' ),
            array ('type' => 'true', 'amount' => 10, 'name' => 'discounted')
        );
        
        foreach( $prices as $price)
        {
            $Price  = new Price();
            {
                $Price->setType($price['type']);
                $Price->setAmount($price['amount']);
                $Price->setName($price['name']);
            }
            
            $manager->persist($Price);
        }
       
        $manager->flush();
    }
}
