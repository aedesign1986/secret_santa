<?php

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    public function loginAction(Request $request){
        $helpers = $this->get("app.helpers");
        $jwt_auth = $this->get("app.jwt_auth");
        $json = $request->getContent();

        if($json != null){
            $params = json_decode($json);

            $email = (isset($params->email)) ? $params->email : null;
            $password = (isset($params->password)) ? $params->password : null;
            $getHash = (isset($params->getHash)) ? $params->getHash : null;
            $emailConstraint = new Assert\Email();
            $emailConstraint->message = "This email is not valid !!!";
            $validate_email = $this->get("validator")->validate($email, $emailConstraint);

            //Password cifrate
            $pwd = hash('sha256', $password);
            if(count($validate_email) == 0 && $password != null){
                $result = $jwt_auth->signUp($email, $pwd, $getHash);
                return new JsonResponse($result);


            }else{
                echo "Data no valid";
                die();
            }

        }else{
            echo "Haz la peticion por post y con los parametros correctos";
            die();
        }

    }

    public function pruebasAction(Request $request)
    {
        $helpers = $this->get("app.helpers");
        $hash = $request->get("Authorization", null);
        $check = $helpers->authCheck($hash);
        var_dump($check);
        die();

//        $em = $this->getDoctrine()->getManager();
//        $users = $em->getRepository('BackendBundle:User')->findAll();
//        return $helpers->responseToJson($users);
    }




}
