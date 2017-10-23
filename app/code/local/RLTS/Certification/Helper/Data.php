<?php

class RLTS_Certification_Helper_Data extends Mage_Core_Helper_Abstract
{
	const INACTIVE = 'Inactive';
	const CANDIDATE = 'Candidate';
	const CERTIFIED = 'Certified';
	const CERTI_STATUS = 'certi_status';
	const NEXT_AUTO_STATUS_CHANGE_DATE = 'next_auto_status_change_date';	

	
	
	public function getDurationMonthsForCandidate()
	{
		return Mage::getStoreConfig('rlts/rlts_group/rlts_input_1',Mage::app()->getStore());	
	}
	
	
	
	public function getDurationMonthsForCertified()
	{
		return Mage::getStoreConfig('rlts/rlts_group/rlts_input_2',Mage::app()->getStore());	
	}
	
	
	
	public function getDurationMonthsForRenewal()
	{
		return Mage::getStoreConfig('rlts/rlts_group/rlts_input_3',Mage::app()->getStore());	
	}
	
	
	
	public function getStatusIntValueFromStringValue($statusStringValue)
	{
		return $this->getStatusXValueFromYValue('value', 'label', $statusStringValue);
	}
	
	

	public function getStatusStringValueFromIntValue($statusIntValue)
	{
		return $this->getStatusXValueFromYValue('label', 'value', $statusIntValue);
	}
	
	
	
	private function getStatusXValueFromYValue($x, $y, $value)
	{
		$statusXValue = "";

		$attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')->setCodeFilter(self::CERTI_STATUS)->getFirstItem();
		$attributeOptions = $attributeInfo->getSource()->getAllOptions(false);

		for($iii=0; $iii<count($attributeOptions); ++$iii)
		{
			if($attributeOptions[$iii][$y] == $value)
			{
				$statusXValue = $attributeOptions[$iii][$x];
				break;
			}
		}
		
		return $statusXValue;
	}


	
	public function getCountOfInactiveCustomers()
	{
		return $this->getCountOfCustomersByStatus(self::INACTIVE);
	}

	
	
	public function getCountOfCandidateCustomers()
	{
		return $this->getCountOfCustomersByStatus(self::CANDIDATE);
	}


	
	public function getCountOfCertifiedCustomers()
	{
		return $this->getCountOfCustomersByStatus(self::CERTIFIED);
	}
	
	
	
	private function getCountOfCustomersByStatus($status)
	{
		$result = 0;
		$statusValue = $this->getStatusIntValueFromStringValue($status);

		if($statusValue!="")
		{
			$list = Mage::getModel('customer/customer')
					->getCollection()
					->addAttributeToSelect(self::CERTI_STATUS)				
					->addAttributeToFilter(self::CERTI_STATUS, $statusValue)
					->load();
			$result = $list->count();
		}
		
		return $result;
	}
}