<?php

class RLTS_Certification_Model_StatusHelper
{
	public function updateCertiStatus()
	{
		$helper = Mage::helper('Certification');		

		$list = Mage::getModel('customer/customer')
					->getCollection()
					->addAttributeToSelect(array($helper::CERTI_STATUS, $helper::NEXT_AUTO_STATUS_CHANGE_DATE))
					->load();

		$now = new DateTime('now');		
		$attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')->setCodeFilter($helper::CERTI_STATUS)->getFirstItem();
		$attributeId = $attributeInfo->getId();

		foreach ($list as $customer)
		{
			$cDate = new DateTime($customer->getNextAutoStatusChangeDate());
			$statusString = $helper->getStatusStringValueFromIntValue($customer->getCertiStatus());
			
			if($now > $cDate && $statusString != $helper::INACTIVE)
			{
				$statusToChangeTo = $helper->getStatusIntValueFromStringValue($helper::INACTIVE);

				$dateToChangeTo = "";
				$customer->setCertiStatus($statusToChangeTo);
				$customer->setNextAutoStatusChangeDate($dateToChangeTo);
				$customer->save();
			}
		}
	}
	
	public function certiRenewalReminder()
	{
		$helper = Mage::helper('Certification');		
		
		$list = Mage::getModel('customer/customer')
					->getCollection()
					->addAttributeToSelect(array($helper::CERTI_STATUS, $helper::NEXT_AUTO_STATUS_CHANGE_DATE))
					->load();
		
		$now = new DateTime('now');
		$expiryLimit = new DateTime('now');
		$expiryLimit = $expiryLimit->add(new DateInterval('P' . $helper->getDurationMonthsForRenewal() . 'M'));

		foreach ($list as $customer)
		{
			$cDate = new DateTime($customer->getNextAutoStatusChangeDate());
			$statusString = $helper->getStatusStringValueFromIntValue($customer->getCertiStatus());
			
			if($now < $cDate && $cDate < $expiryLimit && $statusString == $helper::CERTIFIED)
			{
				$email = $customer->getEmail();		
				//TODO: Send reminder for this customer
				try {
					$user = Mage::getModel('customer/customer')->load($customer->getEntityId());	
					$fName = $user->getFirstname();
					$lName = $user->getLastname();
					
					//load the custom template to the email  
					$emailTemplate = Mage::getModel('core/email_template')
							->loadDefault('certification_renewal_notification');
				   
					$dat = $customer->getNextAutoStatusChangeDate();							 
					$expiry = date("j F\, Y", strtotime($dat));
					
					// it depends on the template variables
					$emailTemplateVariables = array();
					$emailTemplateVariables['name'] = $fName ;
					$emailTemplateVariables['expiry'] = $expiry;
						
					$emailTemplate->setSenderName('ICCOTP');
					$emailTemplate->setSenderEmail('iccotp');
					$emailTemplate->setType('html');
					$emailTemplate->setTemplateSubject('Renewal Notification of Certificate');
					$emailTemplate->send($email, $fName, $emailTemplateVariables);
				} catch (Exception $e) {
					$errorMessage = $e->getMessage();
					Mage::log($errorMessage);
				}
			}
		}
	}
}