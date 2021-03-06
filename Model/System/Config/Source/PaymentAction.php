<?php
namespace Kesterpay\Gateway\Model\System\Config\Source;

use Magento\Payment\Model\Method\AbstractMethod;

/**
 * Class Ccbrand Source model for CC flags
 *
 * @see        Official Website
 * @author    Kesterpay (and others)
 * @copyright 2018-2019 Kesterpay
 * @license   https://www.gnu.org/licenses/gpl-3.0.pt-br.html GNU GPL, version 3
 * @package   Kesterpay\Gateway\Model\System\Config\Source
 */
class PaymentAction implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $options = array();
        $options[] = array('value'=> AbstractMethod::ACTION_AUTHORIZE,'label'=> __('Não'));
        $options[] = array('value'=> AbstractMethod::ACTION_AUTHORIZE_CAPTURE,'label'=> __('Sim'));
        

        return $options;
    }
}