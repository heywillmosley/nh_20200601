<?php
/**
 *  Copyright 2014 Taxamo, Ltd.
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

/**
 * $model.description$
 *
 * NOTE: This class is auto generated by the swagger code generator program. Do not edit the class manually.
 *
 */
class ValidateTaxNumberOut {

  static $swaggerTypes = array(
      'tax_deducted' => 'bool',
      'buyer_tax_number' => 'string',
      'billing_country_code' => 'string'

    );

  /**
  * True if the transaction deducted from tax and no tax is applied. Either set automatically when VAT number validates with VIES correctly, but can also be provided in manual mode.
  */
  public $tax_deducted; // bool
  /**
  *  Buyer's tax number - EU VAT number for example. If using EU VAT number, it is possible to provide country code in it (e.g. IE1234567X) or simply use billing_country_code field for that. In the first case, if billing_country_code value was provided, it will be overwritten with country code value extracted from VAT number - but only if the VAT has been verified properly.
  */
  public $buyer_tax_number; // string
  /**
  * Billing two letter ISO country code.
  */
  public $billing_country_code; // string
  }

