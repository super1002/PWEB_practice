<?php
/**
 * Created by PhpStorm.
 * User: Guillermo
 * Date: 10/4/16
 * Time: 12:39
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Comment;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Form\CommentType;
use AppBundle\Form\NewProductType;
use KMS\FroalaEditorBundle\Form\Type\FroalaEditorType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints as Validator;
use Symfony\Component\HttpFoundation\Request;

class ProfileController extends Controller
{

    const MAX_BALANCE = 1000;
    const PAYPAL = 1;
    const MASTER_CARD = 2;
    const VISA = 3;
    const BITCOIN = 4;


    public function rechargeAction(Request $request){

        $form = $this->createFormBuilder($this->getUser(), array(
                'validation_groups' => array('recharge')
            ))
            ->add('recharge', NumberType::class)

            ->add('recharge', NumberType::class, array(
                'label' => 'Quantity (€)',
            ))
            ->add('submit', SubmitType::class)
            ->add('payment_method', ChoiceType::class, array(
                'choices' => array(
                    'Mastercard' => ProfileController::MASTER_CARD,
                    'Paypal' => ProfileController::PAYPAL,
                    'Visa' => ProfileController::VISA,
                    'Bitcoin' => ProfileController::BITCOIN
                ),
                /*'choice_label' => function($value, $key, $index){

                    switch($value){
                        case ProfileController::PAYPAL:
                            $file ='/images/form_payment_method_1.png';
                            break;
                        case ProfileController::MASTER_CARD:
                            $file ='/images/form_payment_method_0.png';
                            break;
                        case ProfileController::VISA:
                            $file ='/images/form_payment_method_2.png';
                            break;
                        case ProfileController::BITCOIN:
                            $file ='/images/icon_bitcoin.ong';
                            break;
                    }

                    return new File($file);
                },*/
                "mapped" => false,
                'multiple' => false,
                'expanded' => true,
                'label' => ' ',


            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getUser()->setBalance($this->getUser()->getBalance()+$this->getUser()->getRecharge());
            $this->getDoctrine()->getManager()->flush();
        }


        return $this->render('default/recharge.html.twig',
            array(
                'form' => $form->createView()
            ));
    }

    public function addProductAction(Request $request){


        $fileSystem = new Filesystem();
        if(!$fileSystem->exists('uploads/Products/'.$this->getUser()->getUsername())){
            $fileSystem->mkdir('uploads/Products/'.$this->getUser()->getUsername());
        }

        $tempPictureRoute = 'uploads/Products/product-placeholder.png';
        $product = new Product();

        $em = $this->getDoctrine()->getManager();

        $em->persist($product);

        $form = $this->createForm(NewProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $product->setOwner($this->getUser());

            $repo = $this->getDoctrine()->getRepository('AppBundle:Product');
            $resultset = $repo->findBy(array('name' => $product->getName()));
            $partial = join('-', preg_split('/\s/', strtolower($product->getName())));
            $redirections = $this->getDoctrine()->getRepository('AppBundle:Redirection')->getNumberRedirections($partial);
            if((is_null($resultset) or empty($resultset)) and $redirections == 0){
                $product->setNormalizedName($partial);
            }else{
                $product->setNormalizedName($partial . '-' . (count($resultset) + $redirections));
            }
            $product->setCreationDate(new \DateTime());

            $file = $product->getFile();
            if (null !== $file) {

                $extension =  $file->guessExtension();
                if (!$extension) {
                    // extension cannot be guessed
                    $extension = 'bin';
                }

                $resize400 = imagecreatetruecolor(400, 300);
                $resize100 = imagecreatetruecolor(100, 100);

                imagesavealpha($resize400, true);
                imagesavealpha($resize100, true);

                $makeWhite400 = imagecolorallocatealpha($resize400, 0, 0, 0, 127);
                $makeWhite100 = imagecolorallocatealpha($resize100, 0, 0, 0, 127);

                imagefill($resize400, 0, 0, $makeWhite400);
                imagefill($resize100, 0, 0, $makeWhite100);

                switch ($extension){
                    case "gif":
                        $source = imagecreatefromgif($file->getRealPath());
                        break;
                    case "png":
                        $source = imagecreatefrompng($file->getRealPath());
                        break;
                    default:
                        $source = imagecreatefromjpeg($file->getRealPath());
                        break;
                }

                list($w, $h) = getimagesize($file->getRealPath());

                $ratio = $w/$h;

                dump($ratio);

                if($ratio > 1){
                    if($ratio > 4/3){
                        //Amplada restrictiva a les dues mides
                        $wr = 400;
                        $hr = $wr/$ratio;
                        imagecopyresampled($resize400, $source, 0, ((300 - $hr)/2), 0, 0, $wr, $hr, $w, $h);

                        $wr = 100;
                        $hr = $wr/$ratio;
                        imagecopyresampled($resize100, $source, 0, ((100 - $hr)/2), 0, 0, $wr, $hr, $w, $h);
                    }
                    else{
                        //Altura restrictiva a 400x300
                        $hr = 400;
                        $wr = $hr*$ratio;
                        imagecopyresampled($resize400, $source, ((400 - $wr)/2), 0, 0, 0, $wr, $hr, $w, $h);

                        //Amplada restrictiva a 100x100
                        $wr = 100;
                        $hr = $wr/$ratio;
                        imagecopyresampled($resize100, $source, 0, ((100 - $hr)/2), 0, 0, $wr, $hr, $w, $h);
                    }
                }
                else{
                    //Altura restrictiva a les dues mides
                    $hr = 300;
                    $wr = $hr*$ratio;
                    imagecopyresampled($resize400, $source, ((400 - $wr)/2), 0, 0, 0, $wr, $hr, $w, $h);

                    $hr = 100;
                    $wr = $hr*$ratio;
                    imagecopyresampled($resize100, $source, ((100 - $wr)/2), 0, 0, 0, $wr, $hr, $w, $h);
                }

                $productPicturesDir = $this->getParameter('kernel.root_dir').'/../web/uploads/Products/'.$product->getOwner()->getUsername().'/';

                //Getting the file saved
                $fileName = $product->getNormalizedName().'.400.png';
                imagepng($resize400, $productPicturesDir.$fileName);

                $fileName = $product->getNormalizedName().'.100.png';
                imagepng($resize100, $productPicturesDir.$fileName);

                $product->setPicture('uploads/Products/'.$product->getOwner()->getUsername().'/'. $product->getNormalizedName());
            }
            else{

                if($fileSystem->exists($this->getParameter('kernel.root_dir').'/../web/uploads/Products/TEMP/'.$this->getUser()->getUsername().'.400.png')){

                    var_dump("fileExists");

                    $file = new File($this->getParameter('kernel.root_dir').'/../web/uploads/Products/TEMP/'.$this->getUser()->getUsername().'.400.png');

                    $productPicturesDir = $this->getParameter('kernel.root_dir').'/../web/uploads/Products/'.$product->getOwner()->getUsername().'/';

                    //Getting the file saved

                    $fileName = $product->getNormalizedName().'.400.png';
                    $file->move($productPicturesDir, $fileName);

                    $file = new File($this->getParameter('kernel.root_dir').'/../web/uploads/Products/TEMP/'.$this->getUser()->getUsername().'.100.png');
                    $fileName = $product->getNormalizedName().'.100.png';
                    $file->move($productPicturesDir, $fileName);

                    $product->setPicture('uploads/Products/'.$product->getOwner()->getUsername().'/'. $product->getNormalizedName());
                }
                else{
                    //El formulari es valid pero no ha posat imatge i no hi ha cap imatge temporal

                    return $this->render('default/new_product.html.twig',
                        array(
                            'form' => $form->createView(),
                            'image' => $tempPictureRoute
                        ));
                }
            }

            //Tot esta be i tenim imatge (Ja sigui precarregada la que ha entrat ara)
            if($this->getUser()->getBalance() - $product->getStock() > 0){

                var_dump($this->getUser()->getBalance() - $product->getStock());

                $this->getUser()->setBalance($this->getUser()->getBalance() - $product->getStock());

                $em->flush();

                $this->addFlash('modal','Your product was added');
                return $this->redirectToRoute('product_show', array(
                    'category' => $product->getCategory(),
                    'uuid' => $product->getNormalizedName()
                ));
            }else{
                //No te diners!!

                $this->addFlash('modal','You don\'t have enough money');

                return $this->render('default/new_product.html.twig',
                    array(
                        'form' => $form->createView(),
                        'image' => $product->getPicture100()
                    ));
            }
            //HUE
        }
        else{
            $file = $product->getFile();

            if (null !== $file) {

                if($file->isValid()) {

                    $extension =  $file->guessExtension();
                    if (!$extension) {
                        // extension cannot be guessed
                        $extension = 'bin';
                    }

                    $resize400 = imagecreatetruecolor(400, 300);
                    $resize100 = imagecreatetruecolor(100, 100);

                    imagesavealpha($resize400, true);
                    imagesavealpha($resize100, true);

                    $makeWhite400 = imagecolorallocatealpha($resize400, 0, 0, 0, 127);
                    $makeWhite100 = imagecolorallocatealpha($resize100, 0, 0, 0, 127);

                    imagefill($resize400, 0, 0, $makeWhite400);
                    imagefill($resize100, 0, 0, $makeWhite100);

                    switch ($extension){
                        case "gif":
                            $source = imagecreatefromgif($file->getRealPath());
                            break;
                        case "png":
                            $source = imagecreatefrompng($file->getRealPath());
                            break;
                        default:
                            $source = imagecreatefromjpeg($file->getRealPath());
                            break;
                    }

                    list($w, $h) = getimagesize($file->getRealPath());

                    $ratio = $w/$h;

                    if($ratio > 1){
                        if($ratio > 4/3){
                            //Amplada restrictiva a les dues mides
                            $wr = 400;
                            $hr = $wr/$ratio;
                            imagecopyresampled($resize400, $source, 0, ((300 - $hr)/2), 0, 0, $wr, $hr, $w, $h);

                            $wr = 100;
                            $hr = $wr/$ratio;
                            imagecopyresampled($resize100, $source, 0, ((100 - $hr)/2), 0, 0, $wr, $hr, $w, $h);
                        }
                        else{
                            //Altura restrictiva a 400x300
                            $hr = 400;
                            $wr = $hr*$ratio;
                            imagecopyresampled($resize400, $source, ((400 - $wr)/2), 0, 0, 0, $wr, $hr, $w, $h);

                            //Amplada restrictiva a 100x100
                            $wr = 100;
                            $hr = $wr/$ratio;
                            imagecopyresampled($resize100, $source, 0, ((100 - $hr)/2), 0, 0, $wr, $hr, $w, $h);
                        }
                    }
                    else{
                        //Altura restrictiva a les dues mides
                        $hr = 300;
                        $wr = $hr*$ratio;
                        imagecopyresampled($resize400, $source, ((400 - $wr)/2), 0, 0, 0, $wr, $hr, $w, $h);

                        $hr = 100;
                        $wr = $hr*$ratio;
                        imagecopyresampled($resize100, $source, ((100 - $wr)/2), 0, 0, 0, $wr, $hr, $w, $h);
                    }

                    $productPicturesTemp = $this->getParameter('kernel.root_dir') . '/../web/uploads/Products/TEMP/';

                    //Getting the file saved
                    $fileName = $this->getUser()->getUsername().'.400.png';
                    imagepng($resize400, $productPicturesTemp.$fileName);

                    $fileName = $this->getUser()->getUsername().'.100.png';
                    imagepng($resize100, $productPicturesTemp.$fileName);

                    $tempPictureRoute = 'uploads/Products/TEMP/'.$this->getUser()->getUsername().'.100.png';
                }
            }
        }

        return $this->render('default/new_product.html.twig',
            array(
                'form' => $form->createView(),
                'image' => $tempPictureRoute
            ));
    }


    public function showProductsAction(Request $request, $page){


        $allProducts = $this->getUser()->getProducts()->toArray();
        $limit = 12;
        $products = null;
        $totalProducts = count($allProducts);
        dump($totalProducts);
        dump($allProducts);
        $maxPages = ceil($totalProducts / $limit);

        if ($page < 1){

            $this->redirectToRoute('profile_products', array('page' => 1));
        }elseif ($page > $maxPages){

            $this->redirectToRoute('profile_products', array('page' => $maxPages));
        }


        if ($allProducts){

            $pageProducts = array_chunk($allProducts, 12);
            dump($pageProducts);
            $products = array_chunk($pageProducts[$page - 1], 3);
        }



        return $this->render('default/user_products.html.twig',
            array(
                'products' => $products,
                'maxPages' => $maxPages,
                'page' => $page
            ));

    }

    public function showAction($username){

        $canComment1 = true;
        $canComment2 = false;

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('username' => $username));

        if(is_null($user) or empty($user) ){
            throw $this->createNotFoundException();
        }

        $newComment = new Comment();
        $userComment = new Comment();

        foreach ($user->getComments() as $ele) {
            if ($ele->getAuthor()->getUsername() == $this->getUser()->getUsername()) {
                $userComment = $ele;
                $canComment1 = false;
                break;
            }
        }

        $canComment2 = false;
        foreach($this->getUser()->getPurchases() as $purchase){
            if($purchase->getProduct()->getOwner() == $user){
                $canComment2 = true;
                break;
            }
        }

        $canComment = $canComment1 && $canComment2;

        $formCreate = $this->createForm(CommentType::class, $newComment, array('username' => $username));

        dump($userComment);
        $formEdit = $this->createForm(CommentType::class, $userComment, array('username' => $username,
            'route' => 'edit_comment'));


        return $this->render('default/view_profile.html.twig',
            array(
                'formCreate' => $formCreate->createView(),
                'formEdit' => $formEdit->createView(),
                'user' => $user,
                'canComment' => $canComment
            ));
    }

    public function postCommentAction(Request $request, $username){

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('username' => $username));

        $comment = new Comment();
        $this->getDoctrine()->getManager()->persist($comment);
        $comment->setAuthor($this->getUser());
        $comment->setTarget($user);

        $formCreate = $this->createForm(CommentType::class, $comment, array('username' => $username));

        $formCreate->handleRequest($request);

        if($formCreate->isSubmitted() && $formCreate->isValid()){
            $canPost = true;
            foreach ($user->getComments() as $ele) {
                if ($ele->getAuthor()->getUsername() == $this->getUser()->getUsername()) {
                    $canPost = false;
                    break;
                }
            }
            if($canPost){
                $user->addComment($comment);
                $this->getDoctrine()->getManager()->flush();
            }else{
                $this->addFlash('modal','You can only comment once per user');
            }
        }

        return $this->redirectToRoute('profile', array('username' => $username));
    }

    public function editCommentAction(Request $request, $username){

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('username' => $username));

        $userComment = new Comment();
        $userComment->setAuthor($this->getUser());
        $userComment->setTarget($user);

        foreach ($user->getComments() as $ele) {
            if ($ele->getAuthor()->getUsername() == $this->getUser()->getUsername()) {
                $userComment = $ele;
                break;
            }
        }
        $this->getDoctrine()->getManager()->persist($userComment);

        $formEdit = $this->createForm(CommentType::class, $userComment, array('username' => $username,
            'route' => 'edit_comment'));
        $formEdit->handleRequest($request);

        if($formEdit->isSubmitted() && $formEdit->isValid()){
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('profile', array('username' => $username));
    }

    public function deleteCommentAction($username){

        $user = $this->getDoctrine()->getRepository('AppBundle:User')->findOneBy(array('username' => $username));
        $userComment = null;

        foreach ($user->getComments() as $ele) {
            if ($ele->getAuthor()->getUsername() == $this->getUser()->getUsername()) {
                $userComment = $ele;
                break;
            }
        }

        if(!is_null($userComment)){
            $this->getDoctrine()->getManager()->remove($userComment);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->redirectToRoute('profile', array('username' => $username));
    }

}