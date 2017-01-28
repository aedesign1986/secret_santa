<?php
/**
 * Created by PhpStorm.
 * User: Alec
 * Date: 18/01/2017
 * Time: 1:49
 */

namespace AppBundle\Services;

use Firebase\JWT\JWT;

class JwtAuth
{
    public $manager;
    public $key;

    public function __construct($manager)
    {
        $this->manager = $manager;
        $this->key = "clave-secreta";
    }

    public function signUp($email, $password, $getHash = NULL){
        $key = $this->key;
        $user = $this->manager->getRepository('BackendBundle:User')->findOneBy(
            array(
                "email" => $email,
                "password" => $password
            )
        );
        $signup = false;
        if(is_object($user)){
            $signup = true;
        }
        if($signup){
            $token = array(
                "sub"=> $user->getId(),
                "email"=> $user->getEmail(),
                "name"=> $user->getName(),
                "surname" => $user->getSurname(),
                "password" => $user->getPassword(),
                "image"=> $user->getImage(),
                "iat"=> time(),
                "exp"=> time()+(7 * 24 * 60 * 60)
            );
            $jwt = JWT::encode($token, $key, 'HS256');
            $decoded = JWT::decode($jwt, $key, array('HS256'));

            if($getHash){
                return $jwt;
            }else{
                return $decoded;
            }

        }else{
            return array("status" => "error", "data" => "Login Failed");
        }
    }

    public function checkToken($jwt, $getIdentity = false){
        $key = $this->key;
        $auth = false;
        $decoded = false;

        try{
            $decoded = JWT::decode($jwt, $key, array('HS256'));
        }catch(\UnexpectedValueException $e){
            $auth = false;
        }catch(\DomainException $e){
            $auth = false;
        }

        if(isset($decoded->sub)){
            $auth = true;
        }else{
            $auth = false;
        }
        return $getIdentity ? $decoded : $auth;
    }

}