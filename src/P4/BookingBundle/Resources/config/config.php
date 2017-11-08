<?php>

Namespace P4\BookingBundle\config;
require_once('vendor/autoload.php');

$stripe = array(
  "secret_key"      => "sk_test_W6WXT55oRpahbFejPnk2NWMl",
  "publishable_key" => "pk_test_jB5pcMqX4X8dHbt901nkONog"
);

\Stripe\Stripe::setApiKey($stripe['secret_key']);