<?php
class ContributionPage extends DrupalTestCase 
{
    /*
     * Helper function to create
     * a Contribution Page
     *
     * @return $contributionPage id of created Contribution Page
     */
    function create( $id ) 
    {
        require_once "CRM/Contribute/BAO/ContributionPage.php";        
        $domain =  CRM_Core_Config::domainID( );
        $params = array(
                        'domain_id'                => $domain,
                        'title'                    => 'Help Test CiviCRM!',
                        'intro_text'               => 'Created for Test Coverage Online Contribution Page',
                        'contribution_type_id'     => 1,
                        'payment_processor_id'     => $id,
                        'is_monetary'              => 1,
                        'is_allow_other_amount'    => 1,
                        'min_amount'               => 10,
                        'max_amount'               => 10000,
                        'goal_amount'              => 100000,
                        'thankyou_title'           => 'Thanks for Your Support!',
                        'is_email_receipt'         => 1,
                        'receipt_from_email'       => 'donations@civicrm.org',
                        'cc_receipt'               => 'receipt@example.com',
                        'bcc_receipt'              => 'bcc@example.com',
                        'is_active'                => 1
                        );
        
        $contributionPage = CRM_Contribute_BAO_ContributionPage::create( $params );
        return $contributionPage->id;
    }
  
}

?>