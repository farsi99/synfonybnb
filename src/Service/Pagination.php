<?php

namespace App\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class Pagination
{
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    public function __construct(ObjectManager $manager, Environment $twig, RequestStack $request, $templatePath)
    {
        $this->manager = $manager;
        $this->twig = $twig;
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->templatePath = $templatePath;
    }

    public function setTempletePath($templatePath)
    {
        $this->templatePath = $templatePath;
        return $this;
    }

    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    public function setRoute($route)
    {
        $this->route = $route;
        return $this;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function display()
    {
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'root' => $this->route

        ]);
    }

    public function getPages()
    {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité classe");
        }
        //connaitre le total des enregistrement de la table
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());
        //faire la division l'arondi
        $pages = ceil($total / $this->limit);
        return $pages;
    }

    public function getData()
    {
        if (empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité classe");
        }
        //Premiere on calcul l'ofset, l'endroit où on demarre
        //Exemple
        //1*10-10 =0
        //2*10-10=10
        $offset = $this->limit * $this->currentPage - $this->limit;
        //Demander au ripository de trouver les élémenats, nombre de page
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit, $offset);
        //Retourner les éléments
        return $data;
    }

    public function setPage($page)
    {
        $this->currentPage = $page;
        return $this;
    }

    public function getPage()
    {
        return $this->currentPage;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }
    public function getLimit()
    {
        return $this->limit;
    }
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }
}
