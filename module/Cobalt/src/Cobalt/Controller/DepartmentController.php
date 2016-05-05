<?php

namespace Cobalt\Controller;

use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class DepartmentController extends AbstractController
{
    public function indexAction()
    {
        $offices = $this->service->findAll();
        
        return new ViewModel(array(
            'offices' => $offices
        ));
    }
    
    public function addAction()
    {
        // Check we have a company id, if not redirect back to list of companies.
        $id = (int)$this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('cobalt/default', array('controller' => 'company'));
	}
        
        // Create a new form.
        $form = $this->getServiceLocator()->get('Cobalt\DepartmentForm');
         
        // Check if the request is a POST.
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $department = $this->getServiceLocator()->get('Cobalt\Department');
            $form->bind($department);
            $form->setData($request->getPost());
            if ($form->isValid())
            {
                // Persist department
          	$em = $this->service->getEntityManager();
                $department->setCompany($em->getReference('Cobalt\Entity\Company', $id));
                $this->service->persist($department);
                
            	// Redirect.
                return $this->redirect()->toRoute('cobalt/default',
                    array('controller' => 'company',
                          'action' => 'detail',
                          'id' => $id
		    ),
                    array('fragment' => 'departments')
                );
            }
        } 
        
        // If not a POST request, or invalid data, then just render the form.
        return new ViewModel(array(
            'form'   => $form,
            'companyId' => $id
        ));
        
    }
    
    public function editAction()
    {
        // Get a current copy of the entity.
        $id = (int)$this->params()->fromRoute('id');
        if (!$id) {
            return $this->redirect()->toRoute('cobalt/default', array('controller' => 'department', 'action'=>'add'));
	}
        $department = $this->service->findById($id);
        
        // Create a new form instance and bind the entity to it.
        $form = $this->getServiceLocator()->get('Cobalt\DepartmentForm');
        $form->bind($department);
        
        // Check if this request is a POST.
        $request = $this->getRequest();
        if ($request->isPost())
        {
            // Validate the data.
            $form->setData($request->getPost());
            if ($form->isValid())
            {
                // Save changes.
                $this->service->persist($department);

                // Redirect back to original referer.
                return $this->redirect()->toUrl($this->retrieveReferer());
            }
        }
        
        $this->storeReferer('office/edit');
        
        return new ViewModel(array(
            'id' => $id,
            'form' => $form
        ));
    }
    
    public function deleteAction()
    {
        
    }
    
    public function detailAction()
    {
        
    }
    
    private function storeReferer($except)
    {
        $referer = $this->getRequest()->getHeader('Referer')->uri()->getPath();
        if (strpos($referer, $except) === false) {
            $session = new Container('department');
            $session->referer = $referer;
        }
    }
    
    private function retrieveReferer()
    {
        $session = new Container('department');
        $referer = $session->referer;
        if (strpos($referer, 'company/detail') !== false) {
            $referer .= '#departments';
        }
        return $referer;
    }
}

