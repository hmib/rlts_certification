<?php 

class RLTS_Certification_Model_Observer 
{
    public function updateAutoStatusChangeDate($observer) 
	{
		$helper = Mage::helper('Certification');
		$oldStatus = $helper->getStatusStringValueFromIntValue($observer->getCustomer()->getOrigData()[$helper::CERTI_STATUS]);
		$newStatus = $helper->getStatusStringValueFromIntValue($observer->getCustomer()->getCertiStatus());
		
		$currentNextAutoStatusChangeDate = new DateTime($observer->getCustomer()->getNextAutoStatusChangeDate());
		$newNextAutoStatusChangeDate = $currentNextAutoStatusChangeDate;
		
		if($oldStatus != $newStatus)
		{
			if($oldStatus == $helper::INACTIVE)
			{
				if($newStatus == $helper::INACTIVE)
				{
					//Nothing needs to be done
				}
				else if($newStatus == $helper::CANDIDATE)
				{
					$newNextAutoStatusChangeDate = new DateTime('now');
					$newNextAutoStatusChangeDate->add(new DateInterval('P' . $helper->getDurationMonthsForCandidate() . 'M'));
				}
				else if($newStatus == $helper::CERTIFIED)
				{
					$newNextAutoStatusChangeDate = new DateTime('now');
					$newNextAutoStatusChangeDate->add(new DateInterval('P' . $helper->getDurationMonthsForCertified() . 'M'));
				}
			}

			else if($oldStatus == $helper::CANDIDATE)
			{
				if($newStatus == $helper::INACTIVE)
				{
					$newNextAutoStatusChangeDate = "";
				}
				else if($newStatus == $helper::CANDIDATE)
				{
					//Nothing needs to be done
				}
				else if($newStatus == $helper::CERTIFIED)
				{
					$newNextAutoStatusChangeDate = new DateTime('now');
					$newNextAutoStatusChangeDate->add(new DateInterval('P' . $helper->getDurationMonthsForCertified() . 'M'));
				}
			}
			
			else if($oldStatus == $helper::CERTIFIED)
			{
				if($newStatus == $helper::INACTIVE)
				{
					$newNextAutoStatusChangeDate = "";
				}
				else if($newStatus == $helper::CANDIDATE)
				{
					//Nothing needs to be done
				}
				else if($newStatus == $helper::CERTIFIED)
				{
					//Nothing needs to be done
				}
			}				
		}
		
		if($newNextAutoStatusChangeDate != $currentNextAutoStatusChangeDate)
		{
			$dateString = date_format($newNextAutoStatusChangeDate, 'M/d/Y');
			$observer->getCustomer()->setNextAutoStatusChangeDate($dateString);
		}
    }
}