<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Repo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;


class ApiController extends Controller
{

    /**
     * @Route("/get-top-php-repos", name="get_top_php_repos")
     */
    public function getTopPhpReposAction(Request $request)
    {

        //init api wrapper
        $client = new \Github\Client();
        //init EntityManager
        $em = $this->getDoctrine()->getManager();
        //https://github.com/KnpLabs/php-github-api
        $repo_response = $client->api('search')->repositories('language:php', 'stars', 'desc');


        //count used to limit our foreach loop - 30 is the default limit
        $count = 0;
        $import_limit = 30;
        foreach($repo_response['items'] as $repo_row){
            $count++;
            if( $count <= $import_limit){
                //get repository
                $repository = $this->getDoctrine()->getRepository('AppBundle:Repo');
                $existing_repo = $repository->findOneBy(array('github_id'=>$repo_row['id']));
                if($existing_repo){
                    //hydrate existing repo
                    $existing_repo->hydrateFromDataRow($repo_row);
                } else{
                    //instantiate a new repo
                    $repo = new Repo();
                    //move all setters / date logic to hydrate
                    $repo->hydrateFromDataRow($repo_row);
                    $em->persist($repo);
                }


            } else{
                break;
            }
        }

        $em->flush();
        return new RedirectResponse($this->generateUrl('frontend'));

    }

}
