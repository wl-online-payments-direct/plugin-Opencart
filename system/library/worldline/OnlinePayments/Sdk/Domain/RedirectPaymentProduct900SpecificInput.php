<?php
/*
 * This class was auto-generated.
 */

namespace OnlinePayments\Sdk\Domain;

use OnlinePayments\Sdk\DataObject;
use UnexpectedValueException;

/**
 * @package OnlinePayments\Sdk\Domain
 */
class RedirectPaymentProduct900SpecificInput extends DataObject
{
    // Properties
    /**
     * @var string
     */
    private $captureTrigger;

    // Methods
    /**
     * @return string
     */
    public function getCaptureTrigger()
    {
        return $this->captureTrigger;
    }
    /**
     * @var string
     */
    public function setCaptureTrigger($value)
    {
        $this->captureTrigger = $value;
    }

    /**
     * @return object
     */
    public function toObject()
    {
        $object = parent::toObject();
        if ($this->captureTrigger !== null) {
            $object->captureTrigger = $this->captureTrigger;
        }
        return $object;
    }

    /**
     * @param object $object
     * @return $this
     * @throws UnexpectedValueException
     */
    public function fromObject($object)
    {
        parent::fromObject($object);
        if (property_exists($object, 'captureTrigger')) {
            $this->captureTrigger = $object->captureTrigger;
        }
        return $this;
    }
}
