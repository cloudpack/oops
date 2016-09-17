<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use AppBundle\Service\SoapService;

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
     * @Route("/request", name="function")
     * @Method({"POST"})
     */
    public function requestAction(Request $request)
    {
        $params = json_decode($request->getContent(), true);
        $Service = new SoapService($this->getStoragePath($params['name']), $params);

        try {
            return $this->json([
                'code' => 200,
                'result' => $Service->call(),
            ]);
        } catch (\SoapFault $fault) {
            return $this->json([
                'code' => $fault->getCode(),
                'result' => $fault->getMessage(),
            ]);
        }
    }

    /**
     * @param File $file
     * @return mixed
     */
    private function saveFile($file)
    {
        $name = md5(uniqid()) . '.wsdl';
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
