<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Service\SoapService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DefaultController extends Controller
{
    private $session;

    public function __construct()
    {
        $this->session = new Session();
    }

    /**
     * @Route("/", name="homepage")
     * @Method({"GET"})
     */
    public function indexAction(Request $request)
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/file", name="file")
     * @Method({"POST"})
     */
    public function fileAction(Request $request)
    {
        $file = $request->files->get('file');
        $name = $this->saveFile($file);

        return $this->json([
            'filename' => $name
        ]);
    }

    /**
     * @Route("/functions", name="functions")
     * @Method({"GET"})
     */
    public function functionsAction(Request $request)
    {
        $name = $request->query->get('name');

        $Service = new SoapService($this->getStoragePath($name));
        $functions = $Service->getSoapFunctions();

        return $this->json([
            'name' => $name,
            'functions' => $functions,
        ]);
    }

    /**
     * @Route("/request", name="request")
     * @Method({"POST"})
     */
    public function requestAction(Request $request)
    {
        $Service = new SoapService($this->getStoragePath($request->request->get('name')), $request->request->all());

        try {
            $Service->call();

            $result = $Service->getLastResponse();
        } catch (\SoapFault $fault) {
            $result = $fault->getMessage();
        }

        return $this->render('default/result.html.twig', [
            'result' => $result,
        ]);
    }

    /**
     * @param File $file
     * @return mixed
     */
    private function saveFile($file)
    {
        $name = $file->getClientOriginalName();
        $file->move($this->getStoragePath(), $name);

        return $name;
    }


    private function getStoragePath($name = null)
    {
        $path = $this->get('kernel')->getRootDir() . '/../web';
        if ($name) {
            $path = $path . '/' . $name;
        }
        return $path;
    }
}
