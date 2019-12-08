<?php

namespace EcommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class EcommerceController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $products = $em->getRepository('EcommerceBundle:Product')->findAll();
        $categories = $em->getRepository('EcommerceBundle:Categorie')->findAll();
        return $this->render('EcommerceBundle:Ecommerce:index.html.twig',array(
          'products' => $products,
          'categories' => $categories
        ));
    } 

    public function aboutAction()
    {
        return $this->render('EcommerceBundle:Ecommerce:about.html.twig');
    }

    public function contactAction()
    {
        return $this->render('EcommerceBundle:Ecommerce:contact.html.twig');
    }

    public function productDetailsAction($id)
    {
      $em = $this->getDoctrine()->getEntityManager();
      $product = $em->getRepository('EcommerceBundle:Product')->find($id);
      $categories = $em->getRepository('EcommerceBundle:Categorie')->findAll();
      return $this->render('EcommerceBundle:Ecommerce:product.html.twig',array(
        'product' => $product,
        'categories' => $categories,
      ));
    }

    public function categorieProductsAction($id)
    {
      $em = $this->getDoctrine()->getEntityManager();
      $categorie = $em->getRepository('EcommerceBundle:Categorie')->find($id);
      $categories = $em->getRepository('EcommerceBundle:Categorie')->findAll();
      $products = $categorie->getProducts();
      return $this->render('EcommerceBundle:Ecommerce:categorie.html.twig',array(
        'products' => $products,
        'categorieAc' => $categorie,
        'categories' => $categories
      ));
    }

    public function cartAction(Request $request){

      $session = $request->getSession();
      if (!$session->has('panier')) $session->set('panier',array());
      $panier = $session->get('panier');

      $em = $this->getDoctrine()->getEntityManager();
      $products = $em->getRepository('EcommerceBundle:Product')->findBy(array('id' => array_keys($panier)));
      return $this->render('EcommerceBundle:Cart:cart.html.twig',array(
        'products' => $products,
        'panier' => $panier
      ));
    }

    public function addCartAction($id,Request $request){

        $session = $request->getSession();
        
        if (!$session->has('panier')) $session->set('panier',array());
        $panier = $session->get('panier');
        //creation ta3 panier w tvérifi lazem andek ken panier bark !ok
        if (array_key_exists($id, $panier)) {
            if ($request->query->get('qte') != null) $panier[$id] = $request->query->get('qte');
        } else {
            if ($request->query->get('qte') != null)
                $panier[$id] = $request->query->get('qte');
            else
                $panier[$id] = 1;
          }
            
        $session->set('panier',$panier);
        
        
        return $this->redirect($this->generateUrl('ecommerce_cart'));

    }


    public function deleteCartAction($id,Request $request){

        $session = $request->getSession();
        
        $panier = $session->get('panier');

        if(array_key_exists($id,$panier)){
          unset($panier[$id]);
          $session->set('panier',$panier);
        }
        return $this->redirect($this->generateUrl('ecommerce_cart'));
    }

    public function clearCartAction(Request $request){
        $session = $request->getSession();
        
        $session->clear();
        if (!$session->has('panier')) $session->set('panier',array());
        $panier = $session->get('panier');
        $session->set('panier',$panier);
        return $this->redirect($this->generateUrl('ecommerce_cart'));
    }


    public function checkoutAction(Request $request)
    {
        
      $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
      $session = $request->getSession();
      if (!$session->has('panier')) $session->set('panier',array());
      $panier = $session->get('panier');

      $em = $this->getDoctrine()->getEntityManager();
      $products = $em->getRepository('EcommerceBundle:Product')->findBy(array('id' => array_keys($panier)));
      return $this->render('EcommerceBundle:Checkout:checkout.html.twig',array(
        'products' => $products,
        'panier' => $panier,
      ));
    }

    public function SendEmailAction(){
       $message = \Swift_Message::newInstance()
       ->setSubject('Formalab')
       ->setFrom('lawinitaher@gmail.com')
       ->setTo('lawinitaher@gmail.com')
       ->setBody('message envoyé '); 

       $this->get('mailer')
           ->send($message);

       return $this->render('EcommerceBundle:Ecommerce:send.html.twig');
    }

}
