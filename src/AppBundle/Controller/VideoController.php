<?php
/**
 * Created by PhpStorm.
 * User: Alec
 * Date: 20/01/2017
 * Time: 1:12
 */

namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use BackendBundle\Entity\User;
use BackendBundle\Entity\Video;

class VideoController extends Controller
{
    public function newAction(Request $request){

        $helpers = $this->get("app.helpers");

        $hash = $request->get("Authorization", null);
        $authCheck = $helpers->AuthCheck($hash);

        if ($authCheck) {

            $identity = $helpers->authCheck($hash, true);

            $json = $request->get("json", null);

            if($json != null) {
                $params = json_decode($json);

                $createdAt = new \DateTime('now');
                $updatedAt = new \DateTime('now');
                $image = null;
                $video_path = null;

                $user_id = ($identity->sub != null) ? $identity->sub : null;
                $title = (isset($params->title)) ? $params->title : null;
                $description = (isset($params->description)) ? $params->description : null;
                $status = (isset($params->status)) ? $params->status : null;

                if($user_id != null && $title != null){
                    $em = $this->getDoctrine()->getManager();
                    $user = $em->getRepository("BackendBundle:User")->findOneBy(array(
                        "id"=> $user_id
                    ));

                    $video = new Video();
                    $video->setUser($user);
                    $video->setTitle($title);
                    $video->setCreatedAt($createdAt);
                    $video->setDescription($description);
                    $video->setStatus($status);
                    $video->setUpdatedAt($updatedAt);

                    $em->persist($video);
                    $em->flush();

                    $video = $em->getRepository("BackendBundle:Video")->findOneBy(array(
                        "user"=> $user,
                        "title"=> $title,
                        "status"=> $status,
                        "createdAt"=> $createdAt
                    ));

                    $data = array(
                        "status" => "success",
                        "code" => 200,
                        "data" => $video
                    );

                }else{
                    $data = array(
                        "status" => "error",
                        "code" => 400,
                        "msg" => "Video not Created"
                    );
                }

            }else{
                $data = array(
                    "status" => "error",
                    "code" => 400,
                    "msg" => "Video not Created, params failed"
                );
            }



        }else{
            $data = array(
                "status" => "error",
                "code" => 400,
                "msg" => "Authorization not Valid"
            );
        }

        return $helpers->responseToJson($data);
    }
}