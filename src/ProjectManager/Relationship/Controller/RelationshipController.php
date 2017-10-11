<?php

namespace ProjectManager\Relationship\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use ProjectManager\Customers\Model\Customer;
use ProjectManager\Projects\Model\Product;
use ProjectManager\Projects\Model\Project;
use SIOFramework\Acl\Controller\SecuredController;
use SIOFramework\Common\Factory\StandardFactory;


class RelationshipController extends SecuredController
{
    protected function userHasAccess()
    {
        return $this->loggedUserHasRole('CUSTOMER');
    }

    /**
     * Retrives the Customer
     * @return Customer
     */
    protected function getLoggedCustomer()
    {
        $userId = $this->getLoggedUserId();

        $dbFactory = new StandardFactory($this->app);
        $user = $dbFactory->get('SIOFramework\Acl\Model\SystemUser',$userId);

        $customer = $dbFactory->selectOne('ProjectManager\Customers\Model\Customer',array('user'=>$user));

        if(! $customer instanceof Customer)
            throw new \ErrorException('Invalid customer user');

        return $customer;
    }

    /**
     * Gets the project by ID
     *
     * @param $projectId
     * @return Project
     * @throws \Exception
     */
    protected function selectProject($projectId)
    {
        $dbFactory = new StandardFactory($this->app);

        $project = $dbFactory->get('ProjectManager\Projects\Model\Project',$projectId);

        if(! $project instanceof Project)
            throw new \Exception('Invalid Project');

        return $project;
    }


    public function dashboard()
    {
        $customer = $this->getLoggedCustomer();
        $projects = $customer->getProjects();
        $products = array();

        // Merging Products of all Projects
        foreach ($projects as $project) {
            $arrayProducts = $project->getProducts();

            if ($arrayProducts->count() == 0) {
                continue;
            }

            $products = array_merge(
                $products,
                $arrayProducts->toArray()
            );
        }

        $products = new ArrayCollection($products);

        $total_projects = $projects->count();
        $total_products = $products->count();
        $pending_total  = 0;
        $paid_total     = 0;

        foreach ($products as $product) {
            if ($product->getPaid()) {
                $paid_total += $product->getValue();
            } else {
                $pending_total += $product->getValue();
            }
        }

        $status = compact(
            'total_products',
            'total_projects',
            'pending_total',
            'paid_total'
        );

        $this->data = compact('products', 'projects', 'status');

        $this->render(
            '@Relationship/dashboard.twig',
            $this->data
        );
    }

    public function listProjects()
    {
        $this->data['list'] = $this->getLoggedCustomer()->getProjects();

        $this->render('@Relationship/project_list.twig',$this->data);
    }

    public function listProducts($projectId)
    {
        $project = $this->selectProject($projectId);

        if(!$this->getLoggedCustomer()->getProjects()->contains($project))
            throw new \ErrorException('Invalid Project');

        $list = $project->getProducts();

        $this->data['list'] = $list;
        $this->data['project'] = $project;

        $this->render('@Relationship/product_list.twig',$this->data);
    }
}