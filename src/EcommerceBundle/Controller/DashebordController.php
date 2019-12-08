<?php

namespace EcommerceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use EcommerceBundle\Entity\Categorie;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;

class DashebordController extends Controller
{
    public function indexAction()
    {
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) { 
            throw $this->createNotFoundException('You are not allowed to access this page');  
        }
    
        return $this->render('EcommerceBundle:Dashebord:index.html.twig');   
    

       /* if ($this->get('security.context')->isGranted('ROLE_ADMIN')) {
            return $this->render('EcommerceBundle:Dashebord:index.html.twig');    
        }else{
            return $this->render('EcommerceBundle:Ecommerce:index.html.twig'); 
        }*/
        
    }

    public function addCategorieAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $categorie = new Categorie();
        $form = $this->createFormBuilder($categorie)
        ->add('name',TextType::class,array('attr'=>array('class'=>'form-control col-md-12','style'=>'margin-bottom:15px;')))
        ->add('description',TextareaType::class,array('attr'=>array('class'=>'form-control col-md-12','style'=>'margin-bottom:15px;')))
        ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $categorie->setName($form['name']->getData());
            $categorie->setDescription($form['description']->getData());
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($categorie);
            $em->flush();
            
        return $this->redirect($this->generateUrl('ecommerce_homepage'));
    }

    return $this->render('EcommerceBundle:Dashebord:add-categorie.html.twig',array(
        'form' => $form->createView()
    ));
}

}
