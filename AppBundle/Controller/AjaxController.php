<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Repo;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;


class AjaxController extends Controller
{

    /**
     * @Route("/ajax/repos", name="get_repos", condition="request.isXmlHttpRequest()")
     */
    public function getReposAction(Request $request)
    {

        //get repository
        $repository = $this->getDoctrine()->getRepository('AppBundle:Repo');
        //get serializer
        $serializer = $this->get('serializer');

        //get all the repos
        $repos = $repository->findAll();

        $json = $serializer->serialize($repos, 'json');

        //create response
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($json);
        return $response;

    }
    /**
     * @Route("/ajax/repo/{repo_id}", name="get_repo",condition="request.isXmlHttpRequest()")
     */
    public function getRepoAction($repo_id)
    {
        //get repository
        $repository = $this->getDoctrine()->getRepository('AppBundle:Repo');
        //get serializer
        $serializer = $this->get('serializer');

        //get this repo by id
        $repo = $repository->find($repo_id);

        $json = $serializer->serialize($repo, 'json');

        //create response
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent($json);
        return $response;

    }
}
