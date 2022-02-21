<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomerAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomerAttributes\Helper;

use Exception;
use Magento\Catalog\Model\Product\Url;
use Magento\Customer\Model\Attribute;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Customer\Model\Session;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\Eav\Model\Entity\Attribute\Backend\Datetime;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Asset\Repository;
use Magento\MediaStorage\Model\File\Uploader;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Swatches\Model\Swatch;
use Mageplaza\Core\Helper\AbstractData;
use Mageplaza\CustomerAttributes\Model\AttributeMetadataConverter;
use Mageplaza\CustomerAttributes\Model\Entity\Attribute\Source\Table;
use Mageplaza\CustomerAttributes\Model\FileResult;
use Zend_Serializer_Exception;
use Zend_Validate_Exception;
use Zend_Validate_File_Upload;
use Zend_Validate_Regex;

/**
 * Class Data
 * @package Mageplaza\CustomerAttributes\Helper
 */
class Data extends AbstractData
{
    const TEMPLATE_MEDIA_PATH          = 'customer_address';
    const TEMPLATE_CUSTOMER_MEDIA_PATH = 'customer';

    /**
     * @var array
     */
    protected $userDefinedAttributeCodes = [];

    /**
     * @var Config
     */
    protected $eavConfig;

    /**
     * @var AttributeMetadataDataProvider
     */
    protected $attributeMetadataDataProvider;

    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Filesystem
     */
    protected $fileSystem;

    /**
     * @var Zend_Validate_File_Upload
     */
    protected $fileUpload;

    /**
     * @var AttributeMetadataConverter
     */
    protected $attributeMetadataConverter;

    /**
     * Data constructor.
     *
     * @param Context $context
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param Config $eavConfig
     * @param Session $customerSession
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param Repository $repository
     * @param UploaderFactory $uploaderFactory
     * @param Config $config
     * @param Filesystem $fileSystem
     * @param Zend_Validate_File_Upload $fileUpload
     * @param AttributeMetadataConverter $attributeMetadataConverter
     */
    public function __construct(
        Context $context,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        Config $eavConfig,
        Session $customerSession,
        AttributeMetadataDataProvider $attributeMetadataDataProvider,
        Repository $repository,
        UploaderFactory $uploaderFactory,
        Config $config,
        Filesystem $fileSystem,
        Zend_Validate_File_Upload $fileUpload,
        AttributeMetadataConverter $attributeMetadataConverter
    ) {
        $this->eavConfig                     = $eavConfig;
        $this->customerSession               = $customerSession;
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->repository                    = $repository;
        $this->uploaderFactory               = $uploaderFactory;
        $this->config                        = $config;
        $this->fileSystem                    = $fileSystem;
        $this->fileUpload                    = $fileUpload;
        $this->attributeMetadataConverter    = $attributeMetadataConverter;

        parent::__construct($context, $objectManager, $storeManager);
    }

    /**
     * @return array
     */
    public function getCustomerFormOptions()
    {
        return [
            ['label' => __('Customer Account Create'), 'value' => 'customer_account_create'],
            ['label' => __('Customer Account Edit'), 'value' => 'customer_account_edit'],
            ['label' => __('Admin Checkout'), 'value' => 'adminhtml_checkout']
        ];
    }

    /**
     * @return array
     */
    public function getAddressFormOptions()
    {
        return [
            ['label' => __('Customer Address Registration'), 'value' => 'customer_register_address'],
            ['label' => __('Customer Address Edit'), 'value' => 'customer_address_edit'],
            ['label' => __('Admin Checkout'), 'value' => 'adminhtml_customer_address'],
            ['label' => __('Frontend Checkout'), 'value' => 'checkout_index_index'],
            ['label' => __('Mageplaza OSC'), 'value' => 'onestepcheckout_index_index'],
        ];
    }

    /**
     * @param string|null $addressType
     *
     * @return array
     */
    public function getInputType($addressType = null)
    {
        $inputTypes = [
            'text'               => [
                'label'            => __('Text Field'),
                'validate_filters' => ['alphanumeric', 'numeric', 'alpha', 'url', 'email'],
                'backend_type'     => 'varchar',
                'default_value'    => 'text',
            ],
            'textarea'           => [
                'label'            => __('Text Area'),
                'validate_filters' => [],
                'backend_type'     => 'text',
                'default_value'    => 'textarea',
            ],
            'date'               => [
                'label'            => __('Date'),
                'validate_filters' => ['date'],
                'backend_model'    => Datetime::class,
                'backend_type'     => 'datetime',
                'default_value'    => 'date',
                'component'        => 'Mageplaza_CustomerAttributes/js/form/element/date',
            ],
            'boolean'            => [
                'label'            => __('Yes/No'),
                'validate_filters' => [],
                'source_model'     => Boolean::class,
                'backend_type'     => 'int',
                'default_value'    => 'yesno',
                'elementTmpl'      => 'ui/form/element/select',
            ],
            'select'             => [
                'label'            => __('Dropdown'),
                'validate_filters' => [],
                'source_model'     => Table::class,
                'backend_type'     => 'int',
                'default_value'    => false,
                'component'        => 'Mageplaza_CustomerAttributes/js/form/element/select',
            ],
            'multiselect'        => [
                'label'            => __('Multiple-select'),
                'validate_filters' => [],
                'backend_model'    => ArrayBackend::class,
                'source_model'     => Table::class,
                'backend_type'     => 'varchar',
                'default_value'    => false,
            ],
            'select_visual'      => [
                'label'            => __('Single-select With Image'),
                'validate_filters' => [],
                'source_model'     => Table::class,
                'backend_type'     => 'int',
                'default_value'    => false,
                'component'        => 'Mageplaza_CustomerAttributes/js/form/element/select',
                'elementTmpl'      => 'Mageplaza_CustomerAttributes/form/element/radio-visual',
            ],
            'multiselect_visual' => [
                'label'            => __('Multiple Select With Image'),
                'validate_filters' => [],
                'backend_model'    => ArrayBackend::class,
                'source_model'     => Table::class,
                'backend_type'     => 'varchar',
                'default_value'    => false,
                'component'        => 'Mageplaza_CustomerAttributes/js/form/element/checkboxes',
                'elementTmpl'      => 'Mageplaza_CustomerAttributes/form/element/checkbox-visual',
            ],
            'image'              => [
                'label'            => __('Media Image'),
                'validate_filters' => [],
                'validate_types'   => ['max_file_size'],
                'backend_type'     => 'text',
                'default_value'    => false,
                'component'        => 'Mageplaza_CustomerAttributes/js/form/element/file-uploader' . $addressType,
                'elementTmpl'      => 'ui/form/element/uploader/uploader',
            ],
            'file'               => [
                'label'            => __('Single File Attachment'),
                'validate_filters' => [],
                'backend_type'     => 'text',
                'validate_types'   => ['max_file_size', 'file_extensions'],
                'default_value'    => false,
                'component'        => 'Mageplaza_CustomerAttributes/js/form/element/file-uploader' . $addressType,
                'elementTmpl'      => 'ui/form/element/uploader/uploader',
            ],
            'textarea_visual'    => [
                'label'            => __('Content'),
                'validate_filters' => [],
                'backend_type'     => 'text',
                'default_value'    => 'textarea_visual',
                'component'        => 'Mageplaza_CustomerAttributes/js/form/element/textarea',
            ],
            'multiline'          => [
                'label'            => __('Multiple Line'),
                'validate_filters' => ['alphanumeric', 'numeric', 'alpha', 'url', 'email'],
                'backend_type'     => 'text',
                'default_value'    => 'text',
            ]
        ];

        return $inputTypes;
    }

    /**
     * @return array
     */
    public function getValidateFilters()
    {
        return [
            ''             => __('None'),
            'alphanumeric' => __('Alphanumeric'),
            'numeric'      => __('Numeric Only'),
            'alpha'        => __('Alpha Only'),
            'url'          => __('URL'),
            'email'        => __('Email'),
            'date'         => __('Date')
        ];
    }

    /**
     * @param string $inputType
     *
     * @return string|false
     */
    public function getDefaultValueByInput($inputType)
    {
        $inputTypes = $this->getInputType();
        if (isset($inputTypes[$inputType])) {
            $value = $inputTypes[$inputType]['default_value'];
            if ($value) {
                return 'default_value_' . $value;
            }
        }

        return false;
    }

    /**
     * @param string $inputType
     *
     * @return string|null
     */
    public function getBackendModelByInputType($inputType)
    {
        $inputTypes = $this->getInputType();
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }

        return null;
    }

    /**
     * @param string $inputType
     *
     * @return string|null
     */
    public function getSourceModelByInputType($inputType)
    {
        $inputTypes = $this->getInputType();
        if (!empty($inputTypes[$inputType]['source_model'])) {
            return $inputTypes[$inputType]['source_model'];
        }

        return null;
    }

    /**
     * @param string $inputType
     *
     * @return string|null
     */
    public function getBackendTypeByInputType($inputType)
    {
        $inputTypes = $this->getInputType();
        if (!empty($inputTypes[$inputType]['backend_type'])) {
            return $inputTypes[$inputType]['backend_type'];
        }

        return null;
    }

    /**
     * @param string $inputType
     * @param string|null $addressType
     *
     * @return string|false
     */
    public function getComponentByInputType($inputType, $addressType = null)
    {
        $inputTypes = $this->getInputType($addressType);
        if (!empty($inputTypes[$inputType]['component'])) {
            return $inputTypes[$inputType]['component'];
        }

        return null;
    }

    /**
     * @param string $inputType
     *
     * @return string|false
     */
    public function getElementTmplByInputType($inputType)
    {
        $inputTypes = $this->getInputType();
        if (!empty($inputTypes[$inputType]['elementTmpl'])) {
            return $inputTypes[$inputType]['elementTmpl'];
        }

        return null;
    }

    /**
     * @param $data
     * @param $validateRules
     *
     * @return string
     * @throws Zend_Serializer_Exception
     */
    public function getValidateRules($data, $validateRules = [])
    {
        $inputType  = $data['frontend_input'];
        $inputTypes = $this->getInputType();
        $rules      = [];

        if (isset($inputTypes[$inputType])) {
            if (in_array($inputType, ['file', 'image'])) {
                foreach ($inputTypes[$inputType]['validate_types'] as $validateType) {
                    if (isset($validateRules[$validateType])) {
                        unset($validateRules[$validateType]);
                    }
                    if (!empty($data[$validateType])) {
                        $rules[$validateType] = $data[$validateType];
                    } elseif (!empty($data['scope_' . $validateType])) {
                        $rules[$validateType] = $data['scope_' . $validateType];
                    }
                }
            } elseif ($inputType === 'date' && !empty($data['is_user_defined'])) {
                $rules['input_validation'] = 'date';
            } elseif (!empty($data['input_validation']) && !empty($inputTypes[$inputType]['validate_filters'])) {
                if (in_array($data['input_validation'], $inputTypes[$inputType]['validate_filters'], true)) {
                    $rules['input_validation'] = $data['input_validation'];
                }
            }
        }

        if (isset($validateRules['input_validation'])) {
            unset($validateRules['input_validation']);
        }

        $result = $this->serialize(array_merge($validateRules, $rules));

        return empty($this->unserialize($result)) ? null : $result;
    }

    /**
     * Generate code from label
     *
     * @param string $label
     *
     * @return string
     * @throws Zend_Validate_Exception
     */
    public function generateCode($label)
    {
        $code              = substr(
            preg_replace(
                '/[^a-z_0-9]/',
                '_',
                $this->getObject(Url::class)->formatUrlKey($label)
            ),
            0,
            30
        );
        $validatorAttrCode = new Zend_Validate_Regex(['pattern' => '/^[a-z][a-z_0-9]{0,29}[a-z0-9]$/']);
        if (!$validatorAttrCode->isValid($code)) {
            $code = 'attr_' . ($code ?: substr(sha1(time()), 0, 8));
        }

        return $code;
    }

    /**
     * @param Attribute $attribute
     *
     * @return array
     */
    public function getAdditionalData($attribute)
    {
        $additionalData = (string) $attribute->getData('additional_data');
        if (!empty($additionalData)) {
            return self::jsonDecode($additionalData);
        }

        return [];
    }

    /**
     * Returns array of user defined attribute codes
     *
     * @param string $entityTypeCode
     *
     * @return array
     * @throws LocalizedException
     */
    public function getUserDefinedAttributeCodes($entityTypeCode)
    {
        if (empty($this->userDefinedAttributeCodes[$entityTypeCode])) {
            $this->userDefinedAttributeCodes[$entityTypeCode] = [];
            foreach ($this->eavConfig->getEntityAttributeCodes($entityTypeCode) as $attrCode) {
                $attribute = $this->eavConfig->getAttribute($entityTypeCode, $attrCode);
                if ($attribute && $attribute->getIsUserDefined()) {
                    $this->userDefinedAttributeCodes[$entityTypeCode][] = $attribute->getAttributeCode();
                }
            }
        }

        return $this->userDefinedAttributeCodes[$entityTypeCode];
    }

    /**
     * @param $entityType
     * @param $formCode
     * @param bool $bypassFilter
     *
     * @return array
     * @throws LocalizedException
     */
    public function getAttributeWithFilters($entityType, $formCode, $bypassFilter = false)
    {
        $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection($entityType, $formCode);
        $result     = [];

        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisible() && ($bypassFilter || $this->filterAttribute($attribute))) {
                $result[] = $attribute;
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Eav\Model\Attribute $attribute
     *
     * @return bool
     * @throws LocalizedException
     */
    public function filterAttribute($attribute)
    {
        $storeId = $this->getScopeId();
        $groupId = $this->getGroupId();
        $stores  = $attribute->getMpStoreId() ?: 0;
        $stores  = explode(',', $stores);
        $groups  = $attribute->getMpCustomerGroup() ?: 0;
        $groups  = explode(',', $groups);

        $isVisibleStore = in_array(0, $stores) || in_array($storeId, $stores);

        return $isVisibleStore && in_array($groupId, $groups);
    }

    /**
     * @return int
     * @throws LocalizedException
     */
    public function getScopeId()
    {
        $scope = $this->_request->getParam(ScopeInterface::SCOPE_STORE) ?: $this->storeManager->getStore()->getId();

        if ($website = $this->_request->getParam(ScopeInterface::SCOPE_WEBSITE)) {
            $scope = $this->storeManager->getWebsite($website)->getDefaultStore()->getId();
        }

        return $scope;
    }

    /**
     * @return int
     */
    public function getGroupId()
    {
        if ($this->customerSession->isLoggedIn()) {
            return $this->customerSession->getCustomer()->getGroupId();
        }

        return 0;
    }

    /**
     * Check the current page is OSC
     *
     * @return bool
     */
    public function isOscPage()
    {
        $moduleEnable = $this->isModuleOutputEnabled('Mageplaza_Osc');
        $isOscModule  = $this->_request->getRouteName() === 'onestepcheckout';

        return $moduleEnable && $isOscModule;
    }

    /**
     * @return string
     */
    public function getBaseTmpMediaPath()
    {
        return self::TEMPLATE_MEDIA_PATH . '/tmp';
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getBaseTmpMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $this->getBaseTmpMediaPath();
    }

    /**
     * @param $file
     *
     * @return string
     * @throws NoSuchEntityException
     */
    public function getTmpMediaUrl($file)
    {
        return $this->getBaseTmpMediaUrl() . '/' . $this->_prepareFile($file);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    protected function _prepareFile($file)
    {
        return ltrim(str_replace('\\', '/', $file), '/');
    }

    /**
     * Move file from temporary directory into base directory
     *
     * @param array $file
     * @param string $baseTmpMediaPath
     *
     * @return string
     * @throws FileSystemException
     * @throws LocalizedException
     */
    public function moveTemporaryFile($file, $baseTmpMediaPath)
    {
        /** @var Filesystem $fileSystem */
        $fileSystem     = $this->getObject(Filesystem::class);
        $directoryRead  = $fileSystem->getDirectoryRead(DirectoryList::MEDIA);
        $directoryWrite = $fileSystem->getDirectoryWrite(DirectoryList::MEDIA);

        $path          = $baseTmpMediaPath . '/' . $file['file'];
        $newName       = Uploader::getNewFileName($directoryRead->getAbsolutePath($path));
        $baseMediaPath = explode('/', $baseTmpMediaPath);
        $baseMediaPath = $baseMediaPath ? $baseMediaPath[0] : $baseTmpMediaPath;
        $newPath       = $baseMediaPath . Uploader::getDispretionPath($newName);

        if (!$directoryWrite->create($newPath)) {
            throw new LocalizedException(
                __('Unable to create directory %1.', $newPath)
            );
        }

        if (!$directoryWrite->isWritable($newPath)) {
            throw new LocalizedException(
                __('Destination folder is not writable or does not exists.')
            );
        }

        $directoryWrite->renameFile($path, $newPath . '/' . $newName);

        return Uploader::getDispretionPath($newName) . '/' . $newName;
    }

    /**
     * @return bool|string
     */
    public function getTinymceConfig()
    {
        if ($this->versionCompare('2.3.0')) {
            $config = [
                'tinymce4' => [
                    'toolbar'     => 'formatselect | bold italic underline | alignleft aligncenter alignright | '
                        . 'bullist numlist | link table charmap',
                    'plugins'     => implode(
                        ' ',
                        [
                            'advlist',
                            'autolink',
                            'lists',
                            'link',
                            'charmap',
                            'media',
                            'noneditable',
                            'table',
                            'contextmenu',
                            'paste',
                            'code',
                            'help',
                            'table'
                        ]
                    ),
                    'content_css' => $this->repository->getUrl('mage/adminhtml/wysiwyg/tiny_mce/themes/ui.css')
                ]
            ];

            return self::jsonEncode($config);
        }

        return false;
    }

    /**
     * Returns array of user defined attribute codes
     *
     * @param string $entityTypeCode
     *
     * @return array
     * @throws LocalizedException
     */
    public function getUserDefinedFileAttributeCodes($entityTypeCode)
    {
        $userDefinedFileAttributeCodes = [];
        foreach ($this->eavConfig->getEntityAttributeCodes($entityTypeCode) as $attrCode) {
            $attribute = $this->eavConfig->getAttribute($entityTypeCode, $attrCode);
            if ($attribute && $attribute->getIsUserDefined() && in_array(
                $attribute->getFrontendInput(),
                ['file', 'image']
            )) {
                $userDefinedFileAttributeCodes[] = $attribute->getAttributeCode();
            }
        }

        return $userDefinedFileAttributeCodes;
    }

    /**
     * @param string $fileUpload
     * @param string $fileDb
     * @param string $attrCode
     *
     * @return bool
     * @throws InputException
     */
    public function validateFile($fileUpload, $fileDb, $attrCode)
    {
        $fileUploadDecode = self::jsonDecode($fileUpload);
        $fileDbDecode     = self::jsonDecode($fileDb);
        $fields           = ['file', 'name', 'size', 'url'];
        if ($fileUploadDecode) {
            foreach ($fields as $field) {
                $fieldUpload = isset($fileUploadDecode[$field]) ? $fileUploadDecode[$field] : '';
                $fieldDb     = isset($fileDbDecode[$field]) ? $fileDbDecode[$field] : '';
                if ($field === 'size') {
                    $fieldUpload = (int) $fieldUpload;
                    $fieldDb     = (int) $fieldDb;
                }
                if (!$fieldDb || !$fieldUpload || ($fieldDb !== $fieldUpload)) {
                    throw new InputException(
                        __('Something went wrong while uploading file (attribute %1)', $attrCode)
                    );
                }
            }
        }

        return true;
    }

    /**
     * @param string $attributeCode
     * @param array $files
     * @param bool $isReturnUrl
     *
     * @return FileResult
     */
    public function uploadFile($attributeCode, $files, $isReturnUrl = true)
    {
        try {
            $uploader = $this->uploaderFactory->create(['fileId' => $files[$attributeCode]]);
            $this->validateFileUploader($attributeCode, $uploader, $files);

            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);

            $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
            $result         = $uploader->save($mediaDirectory->getAbsolutePath($this->getBaseTmpMediaPath()));

            if ($isReturnUrl) {
                unset($result['tmp_name'], $result['path']);

                $result['url'] = $this->getTmpMediaUrl($result['file']);
            }
        } catch (Exception $e) {
            $this->_logger->critical($e);
            $result = [
                'error' => __($e->getMessage())
            ];
        }

        return new FileResult($result);
    }

    /**
     * @param string $attrCode
     * @param Uploader $uploader
     * @param array $files
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function validateFileUploader($attrCode, $uploader, $files)
    {
        $attribute = $this->config->getAttribute('customer_address', key($files));
        if (!$attribute || !$attribute->getId()) {
            throw new NoSuchEntityException(__('No such entity id!'));
        }

        $fileExtensions = $attribute->getFileExtensions();
        if ($fileExtensions) {
            $extensions = array_map('trim', explode(',', $fileExtensions));
            $uploader->setAllowedExtensions($extensions);
        } elseif ($attribute->getFrontendInput() === 'image') {
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
        }

        if ($attribute->getMaxFileSize() && $files[$attrCode]['size'] > $attribute->getMaxFileSize()) {
            throw new LocalizedException(
                __(
                    '%1 must be less than or equal to %2 bytes.',
                    $files[$attrCode]['name'],
                    $attribute->getMaxFileSize()
                )
            );
        }
    }

    /**
     * @return array
     * @throws Zend_Validate_Exception
     */
    public function convertFilesArray()
    {
        $files = [];

        foreach ($this->fileUpload->getFiles()['custom_attributes'] as $itemKey => $item) {
            if (is_array($item)) {
                $files[key($item)][$itemKey] = current($item);
            }
        }

        return $files;
    }

    /**
     * @param string $attributeCode
     * @param string $entityType
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getAttributeMetadata($attributeCode, $entityType)
    {
        /** @var AbstractAttribute $attribute */
        $attribute = $this->attributeMetadataDataProvider->getAttribute($entityType, $attributeCode);

        if ($attribute && ($attributeCode === 'id' || $attribute->getId() !== null)) {
            $attributeMetadata = $this->attributeMetadataConverter->createMetadataAttribute($attribute);

            return $attributeMetadata;
        } else {
            throw new NoSuchEntityException(
                __(
                    'No such entity with %fieldName = %fieldValue, %field2Name = %field2Value',
                    [
                        'fieldName'   => 'entityType',
                        'fieldValue'  => $entityType,
                        'field2Name'  => 'attributeCode',
                        'field2Value' => $attributeCode
                    ]
                )
            );
        }
    }

    /**
     * @param string $entityTypeCustomer
     * @param int $attributeSetIdCustomer
     *
     * @return array
     */
    public function getAttributeCodes($entityTypeCustomer, $attributeSetIdCustomer)
    {
        return $this->attributeMetadataDataProvider->getAllAttributeCodes(
            $entityTypeCustomer,
            $attributeSetIdCustomer
        );
    }

    /**
     * @param string $field
     * @param array $condition
     *
     * @return bool|string
     */
    public function filterFrontendInput($field, $condition)
    {
        if ($field === 'frontend_input' &&
            isset($condition['in']) &&
            (
                in_array('textarea', $condition['in'], true) ||
                in_array('textarea_visual', $condition['in'], true)
            )
        ) {
            $sql = '';
            foreach ($condition['in'] as $cond) {
                if ($sql) {
                    $sql .= ' OR ';
                }

                if ($cond === 'textarea') {
                    $sql .= "(`frontend_input` = '$cond' AND `additional_data` IS NULL)";
                } elseif ($cond === 'textarea_visual') {
                    $likeValue = '"' . Swatch::SWATCH_INPUT_TYPE_KEY . '":"' . Swatch::SWATCH_INPUT_TYPE_VISUAL . '"';
                    $sql       .= "(`frontend_input` = 'textarea' AND `additional_data` LIKE '%$likeValue%' )";
                } else {
                    $sql .= "`frontend_input` = '$cond'";
                }
            }

            return $sql;
        }

        return false;
    }
}
