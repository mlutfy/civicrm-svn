<?php
/*
 +----------------------------------------------------------------------+
 | CiviCRM version 1.0                                                  |
 +----------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                    |
 +----------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                      |
 |                                                                      |
 | CiviCRM is free software; you can redistribute it and/or modify it   |
 | under the terms of the Affero General Public License Version 1,      |
 | March 2002.                                                          |
 |                                                                      |
 | CiviCRM is distributed in the hope that it will be useful, but       |
 | WITHOUT ANY WARRANTY; without even the implied warranty of           |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                 |
 | See the Affero General Public License for more details at            |
 | http://www.affero.org/oagpl.html                                     |
 |                                                                      |
 | A copy of the Affero General Public License has been been            |
 | distributed along with this program (affero_gpl.txt)                 |
 +----------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo 01/15/2005
 * $Id$
 *
 */

/**
 *
 */
class CRM_Mailing_Form_Test extends CRM_Core_Form {

    public function buildQuickForm() {
        $this->addButtons(
            array(
                array(  'type'  => 'prev',
                        'name'  => '<< Previous'),
                array(  'type'  => 'next',
                        'name'  => ts('Next >>'),
                        'isDefault' => true ),
                array(  'type'  => 'cancel',
                        'name'  => ts('Cancel') )
                )
            );
        $values = array(
            'textFile'  => $this->get('textFile'),
            'htmlFile'  => $this->get('htmlFile'),
            'header'    => $this->get('mailingHeader'),
            'footer'    => $this->get('mailingFooter'),
            'name'      => $this->get('mailing_name'),
        );
        
        $this->addFormRule(array('CRM_Mailing_Form_Test', 'testMail'), $values);
        $session    =& CRM_Core_Session::singleton();
        $email = $session->get('ufEmail');
        $this->assign('email', $email);
    }

    public function &testMail($params, &$files, &$options) {
        $session    =& CRM_Core_Session::singleton();
        $contactId  = $session->get('userID');
        $email      = $session->get('ufEmail');
        
        /* Create a new mailing object for test purposes only */
        $mailing    =& new CRM_Mailing_BAO_Mailing();
        $mailing->domain_id = $session->get('domainID');
        $mailing->header_id = $options['header'];
        $mailing->footer_id = $options['footer'];
        $mailing->name = $options['name'];
        $mailing->from_name = ts('CiviCRM Test Mailer');
        $mailing->from_email = $email;
        $mailing->replyTo_email = $email;
        $mailing->subject = ts('Test Mailing: ') . $options['name'];

        $mailing->body_html = file_get_contents($options['htmlFile']);
        $mailing->body_text = file_get_contents($options['textFile']);
        
        $mime =& $mailing->compose(null, null, null, 
                                    $contactId, $email, $recipient, true);

//      FIXME:  Get the smtp server out of config
        $mailer =& Mail::factory('smtp', array('host' => 'localhost'));

        $body = $mime->get();
        $headers = $mime->headers();
        
        
        PEAR::setErrorHandling( PEAR_ERROR_CALLBACK,
                                array('CRM_Mailing_BAO_Mailing', 'catchSMTP'));
        $result = $mailer->send($recipient, $headers, $body);
        CRM_Core_Error::setCallback();
        
        if ($result === true) {
            return true;
        }
        
        $errors = array( 
            '_qf_default' => 
            ts('The test mailing could not be delivered due to the following error:<br /> <tt>%1</tt>', array('1' => $result->getMessage()))
        );
        return $errors;
    }

    /**
     * Display Name of the form
     *
     * @access public
     * @return string
     */
    public function getTitle( ) {
        return ts( 'Test Mailing' );
    }

}

?>
