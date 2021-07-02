<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\NativeHttpClient;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;

class LandingPageController extends AbstractController
{
    /**
     * @Route("/", name="landing_page")
     * @throws \Exception
     */
    public function index(Request $request, ProductRepository $productRepository, MailerInterface $mailerInterface)
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $idProduct = $request->get('product');
            $paymentMethod = $request->get('payment');
            $product = $productRepository->findOneBy(['id' => $idProduct]);
            $order->setOrderProduct($product);
            $order->setStatus('WAITING');
            $order->setPaymentMethod($paymentMethod);
            $client = $order->getClient();
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($client);
            $entityManager->flush();
            $client->getId();
            $address = $order->getAddressBilling();
            $address->setClient($client);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($order);
            $entityManager->flush();

            $data = ['order'=>[
                        'id'=> $order->getId(),
                        'product' => $product->getName(),
                        'payment_method'=> $paymentMethod,
                        'status'=>$order->getStatus(),
                        'client'=>[
                            'firstname'=>$client->getFirstName(),
                            'lastname'=>$client->getLastName(),
                            'email'=>$client->getEmail()
                        ],
                        "addresses"=>[
                            "billing"=>[
                                "address_line1"=>$client->getAddressLine1(),
                                "address_line2"=>$client->getAddressLine2(),
                                "city"=>$client->getCity(),
                                "zipcode"=>$client->getZipcode(),
                                "country"=>$client->getCountry(),
                                "phone"=>$client->getPhone()
                            ],
                            "shipping"=>[
                                "address_line1"=>$address->getAddressLine1(),
                                "address_line2"=>$address->getAddressLine2(),
                                "city"=>$address->getCity(),
                                "zipcode"=>$address->getZipcode(),
                                "country"=>$address->getCountry(),
                                "phone"=>$client->getPhone()
            
                            ]
                        ]
                    ]
                        
                ];
             
                $data = json_encode($data);
                //$api = $this->api_request($data);
                //$order->setIdApiResponse($api['order_id']);
                $entityManager->persist($order);
                $entityManager->flush();

                //$this->stripeProcess($product->getPrice());
                if($paymentMethod === "stripe"){
    
                    return $this->redirectToRoute('stripe',[
                        'id' => $idProduct,
                        'idOrder'=> $order->getId(),
                    ]);

                }else{
                    return $this->redirectToRoute('paypal',[
                        'id' => $idProduct,
                        'idOrder'=> $order->getId(),
                    ]);
                }
          }
        //Your code here
        return $this->render('landing_page/index_new.html.twig', [
            'products' => $productRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/confirmation", name="confirmation")
     */
    public function confirmation()
    {
        return $this->render('landing_page/confirmation.html.twig', [

        ]);
    }

    /**
     * @Route("/{id}/stripe", name="stripe")
     */
    public function stripe(Request $request, Product $product)
    {
        $idOrder = $request->get('idOrder');


        return $this->render('landing_page/partials/stripe.html.twig', [
            'price' => $product->getPrice(),
            'idOrder' => $idOrder
        ]);
    }

      /**
     * @Route("/{id}/paypal", name="paypal")
     */
    public function paypal(Request $request, Product $product)
    {
        $idOrder = $request->get('idOrder');


        return $this->render('landing_page/partials/paypal.html.twig', [
            'price' => $product->getPrice(),
            'idOrder' => $idOrder
        ]);
    }


    /**
     * @Route("/payment", name="payment")
     */
    public function payment(Request $request, OrderRepository $orderRepository, MailerInterface $mailerInterface)
    {
        if($request->isMethod('POST')){
            $idOrder = $request->get('idOrder');
            $order = $orderRepository->findOneBy(['id' => $idOrder]);
            $price = $request->get('price');
            $order->setStatus("PAID");
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($order);
            $entityManager->flush();

            $this->stripeProcess($price);
            $status = json_encode(["status" => $order->getStatus()]);
            //$this->api_update_order($order->getIdApiResponse(), $status);
            $this->sendEmail($mailerInterface, $order->getClient()->getEmail(), $order->getId(), $order->getOrderProduct()->getName(),$order->getOrderProduct()->getPrice());

            return $this->render('landing_page/confirmation.html.twig', [
           
            ]);

        }
    }


    public function stripeProcess($payment){
        \Stripe\Stripe::setApiKey('sk_test_51ItX7eBHDY4ycr92YZjcJPRRUnKdJ9LCPv2nnks1TTHBCteznlcUASrx3TeTHmLOqlRFBrQATFR4jiAIJeSzcWKH00cbghmajS');
        //dd("payment",strval($payment));
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $payment*100,
            'currency' => 'eur',
        ]);
        $output = [
            'clientSecret' => $paymentIntent->client_secret,
        ];
    }

    public function api_update_order($idOrder, $status){
        $token = 'mJxTXVXMfRzLg6ZdhUhM4F6Eutcm1ZiPk4fNmvBMxyNR4ciRsc8v0hOmlzA0vTaX';

            $httpClient = HttpClient::create([], 6, 50);
            $response = $httpClient->request('POST', 'https://api-commerce.simplon-roanne.com/order/'. $idOrder . '/status', 
                ['headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-type' =>'application/json',
                    ],
                    'body' => $status
                ]);

            $statusCode = $response->getStatusCode();
            $contentType = $response->getHeaders()['content-type'][0];
            $content = $response->getContent();
            $content = $response->toArray();
            return $content;

    }



    public function api_request($data){
        // dd("api",$data);
        $token = 'mJxTXVXMfRzLg6ZdhUhM4F6Eutcm1ZiPk4fNmvBMxyNR4ciRsc8v0hOmlzA0vTaX';

            $httpClient = HttpClient::create([], 6, 50);
            $response = $httpClient->request('POST', 'https://api-commerce.simplon-roanne.com/order', 
                ['headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-type' =>'application/json',
                    ],
                    'body' => $data
                ]);

            $statusCode = $response->getStatusCode();
            $contentType = $response->getHeaders()['content-type'][0];
            $content = $response->getContent();
            $content = $response->toArray();
            return $content;

    }


        public function sendEmail(MailerInterface $mailer, $clientEmail, $orderId, $productName, $productPrice)
        {
            $email = (new Email())
                ->from('orderValidator@battle-office.com')
                ->to($clientEmail)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                ->replyTo('webmaster@battle-office.com')
                //->priority(Email::PRIORITY_HIGH)
                ->subject('Order number '.$orderId)
                ->text('You order a '.$productName.' for '. $productPrice. ' euros.')
                ->html('Thanks for your order');

            $mailer->send($email);
        }
}
