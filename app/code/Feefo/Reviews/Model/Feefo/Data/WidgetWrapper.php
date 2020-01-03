<?php

namespace Feefo\Reviews\Model\Feefo\Data;

use Feefo\Reviews\Api\Feefo\Data\WidgetWrapperInterface;
use Magento\Framework\Api\ObjectFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Feefo\Reviews\Api\Feefo\Data\WidgetConfigInterface;
use Feefo\Reviews\Api\Feefo\Data\WidgetSnippetInterface;

/**
 * Class WidgetWrapper
 *
 * Service Contract that describes widgets API of Feefo service
 */
class WidgetWrapper extends JsonableDataObject implements WidgetWrapperInterface
{
    /**
     * @var ObjectFactory
     */
    protected $objectFactory;

    /**
     * @var WidgetConfigInterface|bool
     */
    protected $widgetSettings = false;

    /**
     * @var WidgetSnippetInterface|bool
     */
    protected $widgetSnippets = false;

    /**
     * WidgetWrapper constructor.
     * @param JsonHelper $jsonHelper
     * @param ObjectFactory $objectFactory
     * @param array $data
     */
    public function __construct(
        JsonHelper $jsonHelper,
        ObjectFactory $objectFactory,
        array $data = []
    ) {
        parent::__construct($jsonHelper, $data);
        $this->objectFactory = $objectFactory;
    }

    /**
     * Retrieve widget settings
     *
     * @return false|WidgetConfigInterface
     */
    public function getWidgetSettings()
    {
        if (!$this->widgetSettings) {
            $widgetSettingsData = $this->getData(static::DATA_WIDGET_SETTINGS);
            $this->widgetSettings = $this->objectFactory->create(WidgetConfigInterface::class, [
                'data' => $widgetSettingsData
            ]);
        }

        return $this->widgetSettings;
    }

    /**
     * Retrieve widget snippets
     *
     * @return false|WidgetSnippetInterface
     */
    public function getSnippetsPreview()
    {
        if (!$this->widgetSnippets) {
            $widgetSettingsData = $this->getData(static::DATA_SNIPPET_PREVIEW);
            $this->widgetSnippets = $this->objectFactory->create(WidgetSnippetInterface::class, [
                'data' => $widgetSettingsData
            ]);
        }

        return $this->widgetSnippets;
    }

    /**
     * Retrieve redirect URL
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return $this->getData(static::DATA_REDIRECT_URL);
    }

    /**
     * Retrieve access token
     *
     * @return string
     */
    public function getAccessToken()
    {
        return $this->getData(static::DATA_ACCESS_TOKEN);
    }
}