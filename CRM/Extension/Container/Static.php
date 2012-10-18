<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2012                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2012
 * $Id$
 */

/**
 * An extension container is a locally-accessible source tree which can be
 * scanned for extensions.
 */
class CRM_Extension_Container_Static implements CRM_Extension_Container_Interface {
  public function __construct($exts) {
    $this->exts = $exts;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->name;
  }

  /**
   * {@inheritdoc}
   */
  public function getKeys() {
    return array_keys($this->exts);
  }

  /**
   * {@inheritdoc}
   */
  public function getPath($key) {
    $e = $this->getExt($key);
    return $e['path'];
  }

  /**
   * {@inheritdoc}
   */
  public function getResUrl($key) {
    $e = $this->getExt($key);
    return $e['resUrl'];
  }

  protected function getExt($key) {
    if (isset($this->exts[$key])) {
      return $this->exts[$key];
    } else {
      throw new Exception("Missing extension: $key");
    }
  }
}
