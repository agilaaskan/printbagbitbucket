<?php
/**
 * @author Mconnect Team
 * @package Mconnect_Csproduct
 */
namespace Mconnect\Csproduct\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Website implements ArrayInterface
{
    public function toOptionArray()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
        $websites = $storeManager->getWebsites();

        $websitesList = [];
        foreach ($websites as $website) {
            $websitesList[] = [
                               'label' => $website->getName(),
                               'value' => $website->getWebsiteId()
                            ];
        }
        return $websitesList;
    }
}
