<?php

namespace Cobalt\Controller;

use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class TicketStatusController extends AbstractController
{
    public function indexAction()
    {
        return new ViewModel(array(
            'states' => $this->service->findAll()
        ));
    }
    
    public function addAction()
    {
        // Create a new form.
        $form = $this->getServiceLocator()->get('Cobalt\TicketStatusForm');
         
        // Check if the request is a POST.
        $request = $this->getRequest();
        if ($request->isPost())
        {
            // POST, so check if valid.
            $data = (array) $request->getPost();
          
            // Create a new hardware object.
            $status = $this->getServiceLocator()->get('Cobalt\TicketStatus');
            
            $form->bind($status);
            $form->setData($data);
            if ($form->isValid())
            {
          	// Persist ticket status.
            	$this->service->persist($status);
                
            	// Redirect to list of ticket states
		return $this->redirect()->toRoute('cobalt/default', array(
		    'controller' => 'ticketstatus',
                    'action'     => 'index'
		));
            }
        } 
        
        // If not a POST request, or invalid data, then just render the form.
        return new ViewModel(array(
            'form'   => $form
        ));
        
    }
    
    public function editAction()
    {
        
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
            $session = new Container('ticketstatus');
            $session->referer = $referer;
        }
    }
    
    private function retrieveReferer()
    {
        $session = new Container('ticketstatus');
        $referer = $session->referer;
        return $referer;
    }
    
    
}