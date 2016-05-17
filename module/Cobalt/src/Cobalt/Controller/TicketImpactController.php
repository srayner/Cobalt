<?php

namespace Cobalt\Controller;

use Zend\View\Model\ViewModel;
use Zend\Session\Container;

class TicketImpactController extends AbstractController
{
    public function indexAction()
    {
        return new ViewModel(array(
            'impacts' => $this->service->findAll()
        ));
    }
    
    public function addAction()
    {
        // Create a new form.
        $form = $this->getServiceLocator()->get('Cobalt\TicketImpactForm');
         
        // Check if the request is a POST.
        $request = $this->getRequest();
        if ($request->isPost())
        {
            // POST, so check if valid.
            $data = (array) $request->getPost();
          
            // Create a new hardware object.
            $impact = $this->getServiceLocator()->get('Cobalt\TicketImpact');
            
            $form->bind($impact);
            $form->setData($data);
            if ($form->isValid())
            {
          	// Persist ticket impact.
            	$this->service->persist($impact);
                
            	// Redirect to list of ticket states
		return $this->redirect()->toRoute('cobalt/default', array(
		    'controller' => 'ticketimpact',
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
        // Ensure we have an id, else redirect to add action.
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
             return $this->redirect()->toRoute('cobalt/default', array(
                 'controller' => 'ticketimpact',
                 'action' => 'add'
             ));
        }
        
        // Grab the impact with the specified id.
        $impact = $this->service->findById($id);
        
        $form = $this->getServiceLocator()->get('Cobalt\TicketImpactForm');
        $form->bind($impact);
        $form->get('submit')->setAttribute('value', 'Edit');
        
        $request = $this->getRequest();
        if ($request->isPost()) {
        
            $form->setData($request->getPost());
            if ($form->isValid()) {
                
                // Persist impact.
            	$this->service->persist($impact);
                
                // Redirect to original referer
                return $this->redirect()->toUrl($this->retrieveReferer());
            }     
        }
        
        $this->storeReferer('ticketimpact/edit');
        
        return new ViewModel(array(
             'id' => $id,
             'form' => $form,
        ));
    }
    
    public function detailAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        $impact = $this->service->findById($id);
        return new ViewModel(array(
            'impact' => $impact
        ));    
    }
    
    private function storeReferer($except)
    {
        $referer = $this->getRequest()->getHeader('Referer')->uri()->getPath();
        if (strpos($referer, $except) === false) {
            $session = new Container('ticketimpact');
            $session->referer = $referer;
        }
    }
    
    private function retrieveReferer()
    {
        $session = new Container('ticketimpact');
        $referer = $session->referer;
        return $referer;
    }
}