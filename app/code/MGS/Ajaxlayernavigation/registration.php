<?php
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'MGS_Ajaxlayernavigation',
    __DIR__
);

if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'Model/ResourceModel/Layer/Filter/License/License.php')) {
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Model/ResourceModel/Layer/Filter/License/License.php');
}
