<?php

namespace H4w\Datafrete\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function prepareProductsList($cartProducts)
    {        
        $storeScope    = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productsList  = [];

        $addToProductsList = function($parentId, $parent, $sku, $name, $qty = 1, $price = 0, $height = 0, $width = 0, $length = 0, $weight = 0, $sizeUnit = 'cm', $weightUnit = 'kg' ) use (&$productsList) {
            
            $parentQtd = 1;
            
			if ($sizeUnit === 'cm') {
                $height = round($height / 100, 5);
                $width  = round($width  / 100, 5);
                $length = round($length / 100, 5);
            }

            if ($weightUnit === 'g') {
                $weight = round($weight / 1000,5);
            }

            if ($parent === $sku) {
                $productsList[$parentId] = [
                    'sku'         => $sku,
                    'descricao'   => $name,
                    'qtd'         => $qty,
                    'preco'       => $price,
                    'altura'      => $height,
                    'largura'     => $width,
                    'comprimento' => $length,
                    'peso'        => $weight,
                    'volume'      => 1
                ];
                
                return true;
            }

            if (isset($productsList[$parentId])) {
                $parentQtd                              = $productsList[$parentId]['qtd'] ?: 1;
                $productsList[$parentId]['altura']      = 0;
                $productsList[$parentId]['largura']     = 0;
                $productsList[$parentId]['comprimento'] = 0;
                $productsList[$parentId]['peso']        = 0;
            }

            $productsList[$sku] = [
                'sku'         => $sku,
                'descricao'   => $name,
                'qtd'         => $parentQtd * $qty,
                'preco'       => 0,
                'altura'      => $height,
                'largura'     => $width,
                'comprimento' => $length,
                'peso'        => $weight,
                'volume'      => 1,
            ];
            
            return true;
        };

        $heightAttribute = $this->scopeConfig->getValue('carriers/datafrete/heightAttribute', $storeScope);
        $widthAttribute  = $this->scopeConfig->getValue('carriers/datafrete/widthAttribute', $storeScope);
        $lengthAttribute = $this->scopeConfig->getValue('carriers/datafrete/lengthAttribute', $storeScope);
        $weightAttribute = $this->scopeConfig->getValue('carriers/datafrete/weightAttribute', $storeScope);
        $sizeUnit        = $this->scopeConfig->getValue('carriers/datafrete/sizeUnit', $storeScope);
        $weightUnit      = $this->scopeConfig->getValue('carriers/datafrete/weightUnit', $storeScope);
        
        foreach ($cartProducts as $cartProduct) {
            //carrega o produto
            $product  = $parent = $objectManager->create('Magento\Catalog\Model\Product')->load($cartProduct->getProduct()->getId());
            $parentId = $cartProduct->getId();
            //verifica se possui um produto pai
            if (!empty($cartProduct->getParentItemId())) {
                $parentId = $cartProduct->getParentItemId();
                //carrega o produto pai
                $parent   = $objectManager->create('Magento\Catalog\Model\Product')->load($cartProduct->getParentItem()->getProduct()->getId());
            }
            
            //pegar o valor total da linha
            $cartLineValue = $cartProduct->getRowTotal() ?: 0;
            //pegar o total do desconto da linha
            $discountLineValue = $cartProduct->getDiscountAmount() ?: 0;
            
            //se retornou o valor total do carrinho...
            if($cartLineValue > 0) {
                //se valor da linha for maior ou igual ao desconto, e tem desconto..
                if($cartLineValue >= $discountLineValue && $discountLineValue > 0) {
                    $cartLineValue = round( $cartLineValue - $discountLineValue ,5);
                } else if($discountLineValue > 0) {//senao, se tem desconto..
                    //zerar o valor da linha, pois o desconto é superior ao valor da linha..
                    $cartLineValue = 0;
                }
                if($cartProduct->getQty() > 1) {
                    $cartLineValue = round($cartLineValue / $cartProduct->getQty(), 5);
                }
            } else {//senao, pega o preço do produto..
                $cartLineValue = $cartProduct->getPrice();
                //se tem desconto, deverá ser divido pela quantidade de itens, pois é o desconto da linha
                if($discountLineValue > 0) {
                    if($cartProduct->getQty() > 1) {
                        $discountLineValue = round($discountLineValue / $cartProduct->getQty(), 5);
                    }
                    //se o desconto for maior do que o valor do produto..
                    if($discountLineValue > $cartLineValue) {
                        $cartLineValue = 0;//manda zero...
                    } else {
                        $cartLineValue =  round($cartLineValue - $discountLineValue, 5);
                    }
                }
            }

            $addToProductsList(
                $parentId,
                $parent->getSku(),
                $product->getSku(),
                $product->getName(),
                $cartProduct->getQty(),
                $cartLineValue,
                $product->getData($heightAttribute),
                $product->getData($widthAttribute),
                $product->getData($lengthAttribute),
                $product->getData($weightAttribute),
				$sizeUnit,
				$weightUnit
            );

        }
        
        return array_values($productsList);
    }

    public function prepareAdditionalInformation()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        $additionalInformation = [
            'tipo_ordenacao' => (int) $this->scopeConfig->getValue('carriers/datafrete/displayOrder', $storeScope),
            'doc_empresa'    => $this->getOnlyNumbers($this->scopeConfig->getValue('carriers/datafrete/accessTaxvat', $storeScope)),
		    'plataforma'     => $this->scopeConfig->getValue('carriers/datafrete/platformName', $storeScope)
        ];

        return $additionalInformation;
    }

    public function getOnlyNumbers($str)
    {
        return preg_replace('/[^0-9]/s', '', $str);
    }

	public function buildShippingMethodName($title)
	{
		$title 		  = trim(strip_tags((function_exists('mb_strtolower')) ? mb_strtolower($title, 'UTF-8') : strtolower($title)));
		$arraySearch  = ['á','à','ã','â','ä','é','è','ẽ','ê','ë','í','ì','ĩ','î','ï','ó','ò','õ','ô','ö','ú','ù','ũ','û','ü'];
	    $arrayReplace = ['a','a','a','a','a','e','e','e','e','e','i','i','i','i','i','o','o','o','o','o','u','u','u','u','u'];
	    $arrayStrip   = ["~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]", "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;", "â€”", "â€“", ",", "<", ".", ">", "/", "?"];

	    $title = str_replace(' ', '-', $title);
	    $title = str_replace($arraySearch, $arrayReplace, $title);
	    $title = str_replace($arrayStrip, '', $title);
	    $title = preg_replace('/\s+/', '-', $title);
	    $title = preg_replace('/[^a-zA-Z0-9\-]/', '', $title);

	    return $title;
	}

}