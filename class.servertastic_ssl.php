<?php
/**********************************************************************
 *  ServerTastic HostBill Module. Custom developed for ServerTastic.
 *  (18.07.12)
 *  Updated: (15.07.17)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *  UPDATE BY Sławomir Kaleta (https://github.com/dusta/)
 *  Contact slaszka@gmail.com
 *  
 *
 * This software is furnished under a license and may be used and copied
 * only  in  accordance  with  the  terms  of such  license and with the
 * inclusion of the above copyright notice.  This software  or any other
 * copies thereof may not be provided or otherwise made available to any
 * other person.  No title to and  ownership of the  software is  hereby
 * transferred.
 *
 *
 **********************************************************************/

class servertastic_ssl extends SslModule {

    protected $description='Servertastic SSL Module';

    protected $options = array(
        'option1'   => array(
            'name'      => 'certificationtype',
            'value'     => '',
            'type'      => 'select',
            'default'   => array(
                'RapidSSL|4'
                )

            ),
        );
    
    protected $lang=array(
        'english'=>array(
            'certificationtype' => 'Certification Type',
            )
        );   
    
    protected $details = array(
        // 'option1' =>array (
        //         'name'      => 'username',
        //         'value'     => false,
        //         'type'      => 'input',
        //         'default'   => false
        // ),
        // 'option2' =>array (
        //         'name'      => 'password',
        //         'value'     => false,
        //         'type'      => 'input',
        //         'default'   => false
        // ),
        'option3' =>array (
                'name'      => 'domain',
                'value'     => false,
                'type'      => 'input',
                'default'   => false
        ),
        'option4' =>array (
                'name'      => 'referencehostbill',
                'value'     => true,
                'type'      => 'hidden',
                'default'   => false
        ),
        'option5' =>array (
                'name'      => 'referenceservertastic',
                'value'     => true,
                'type'      => 'input',
                'default'   => false
        ),
        'option6' =>array (
                'name'      => 'status',
                'value'     => false,
                'type'      => 'hidden',
                'default'   => false
        ),
        // 'option7' =>array (
        //         'name'      => 'SAN Count',
        //         'value'     => false,
        //         'type'      => 'input',
        //         'default'   => false
        // ),
        // 'option8' =>array (
        //         'name'      => 'Certyficate type',
        //         'value'     => false,
        //         'type'      => 'input',
        //         'default'   => false
        // ),
        // 'option9' =>array (
        //         'name'      => 'Years',
        //         'value'     => false,
        //         'type'      => 'input',
        //         'default'   => false
        // ),
        // 'option10' =>array (
        //         'name'      => 'Servers Count',
        //         'value'     => false,
        //         'type'      => 'input',
        //         'default'   => false
        // )
        'option11' =>array (
                'name'      => 'Order Token',
                'value'     => true,
                'type'      => 'hidden',
                'default'   => false
        ),
        'option12' =>array (
                'name'      => 'review_url',
                'value'     => true,
                'type'      => 'hidden',
                'default'   => false
        ),

        
    );

    protected $serverFields = array(
        'hostname'      => false,
        'ip'            => false,
        'maxaccounts'   => false,
        'status_url'    => false,
        'username'      => true,
        'password'      => false,
        'hash'          => false,
        'ssl'           => true,
        'nameservers'   => false,
    );
    
    protected $serverFieldsDescription = array(
        'username'  => 'API Key',
        'ssl'       => 'Test mode'
    );
        
    protected $commands = array('Create', 'Terminate', 'ResendConfigurationEmail', 'synchInfo', 'Expire', 'Renewal');

    private $api_key;
    private $test_mode;

    // public function __construct(){
    //     //var_dump(parent::__construct());
    // }

    public function Renewal(){

    }

    public function CertOptions($product){
        var_dump($product);
    }

    public function connect($connect) { 

        $this->api_key = $connect['username'];
        $this->test_mode = $connect['secure'];
        
    }

    public function Expire(){
        //... TO DO
        // Q: Canceled or Terminate ? 
    	$this->log('Run Expire');
    }  
    
    // public function IsRecorded(){
    //     if ($this->details['option6']['value'] && $this->details['option6']['value'] != 'Cancelled') {
    //         return true;
    //     }
    //     return false;
    // }

    public function CreateReference(){

        $serviceid = $this->product_details['id'].$_GET['id'];
        //if (!$this->details['option4']['value'])
        return $serviceid;
        
        //if ($this->details['option6']['value'] != 'Cancelled')
        //    return $this->details['option4']['value'];
        //
        //
        //
        //if ($this->details['option6']['value']=='Cancelled') {
        //    if(strpos($this->details['option4']['value'],'.')){
        //        $temp=explode('.',$this->details['option4']['value']);
        //        return $temp[0].'.'.($temp[1]+1);
        //    }
        //    else
        //        return $this->details['option4']['value'].'.2';
        //}

    }

    // private function getYears($cycle){
        
    //     switch($cycle){
    //         case 'Annually':
    //             return '1';
    //             break;
    //         case 'Biennially':
    //             return '2';
    //             break;
    //         case 'Triennially':
    //             return '3';
    //             break;
    //         case 'Quadrennially':
    //             return '4';
    //             break;
    //         default:
    //             return false;
    //             break;
    //     }
        
    // }


    public function log($message){
        if($this->test_mode){
    	    $file = dirname(__FILE__).'/servertastic_ssl/debug_test.txt';
            $current = file_get_contents($file);
            $current .= date('Y-m-d H:i:s').' '.$message."\n";
            file_put_contents($file, $current);
        }
    }
    
    public function Create() {
    	$this->log('Run Create');

        $sancount = $this->details['option7']['value']; 
        $certyficatetype = $this->details['option8']['value']; 

        // STATIC JUST FOR TEST 
        $certtype = ($certyficatetype) ? $certyficatetype : $this->options['option1']['value']; 
        $certproduct = current(explode("|",$certtype,2)); 
        $maxyears = '1'; 
        $years = '1'; 

        if(!isset($years) OR $years > $maxyears) {
        	$this->log('Wrong Period');
            $this->addError('Wrong period for this SSL Order');
            return false;
        }

        $certyears = $years;
	    $productcode = $certproduct.'-'.($years*12);

	    $postfields = array();
	    $postfields['st_product_code'] = $productcode;
	    $postfields['api_key'] = $this->api_key;
	    $postfields['end_customer_email'] = $this->client_data['email'];
        $postfields['reseller_unique_reference'] = $this->CreateReference();

	    $result = $this->SendCommand("System", 'order', "generatetoken.json", $postfields);
        if($result['success'] == 'Order placed'){

            $this->details['option4']['value'] = $postfields['reseller_unique_reference'];
            $this->details['option5']['value'] = $result['reseller_order_id'];
            $this->details['option11']['value'] = $result['order_token'];
            $this->details['option12']['value'] = $result['review_url'];
            $this->details['option6']['value'] = 'Order placed';

            $this->log('Order placed'.json_encode($result));
            $this->addInfo('Order placed');
            return true;

        }

        $this->log('Order Failed'.json_encode($result));
        $this->addError('Failed to generate token');
        return false;

    }

    public function synchInfo(){
    	$this->log('Run synchInfo');

        if(isset($this->details['option11']['value'])){
            $result = $this->SendCommand('System', 'order', 'review.json', array('order_token' => $this->details['option11']['value']));

            if($result['success'] == 'Review Order'){
                $this->details['option3']['value'] = $result['domain_name'];
                $this->details['option6']['value'] = $result['order_status'];

                $this->log('Successfully synchronized'.json_encode($result));
                $this->addInfo('Successfully synchronized');
                return true;
            }

            $this->log('Failed to synchronized'.json_encode($result));
            $this->addError('Failed to synchronized');
            return false;

        }

        $this->log('Invalid referencehostbill');
        $this->addError('Invalid referencehostbill');
        return false;

    }
    
    public function Terminate() {
        $this->log('Run Terminate');

        if ($this->details['option6']['value'] != 'Awaiting Configuration') {

        	$this->log('SSL Either not Provisioned or Not Awaiting Configuration so unable to cancel');
            $this->addError('SSL Either not Provisioned or Not Awaiting Configuration so unable to cancel');
            return false;
            
        }

        $postfields = array();
        $postfields['api_key'] = $this->api_key;
        $postfields["reseller_order_id"] = $this->details['option11']['value'];
        
        $result = $this->SendCommand('System', 'order', 'cancel.json', $postfields);
        if($result["status"]){

            $this->details['option6']['value'] = 'Cancelled';

            $this->log('Account has been terminated.'.json_encode($result));
            $this->addInfo('Account has been terminated.');
            return true;

        }

        $this->log('Error.'.json_encode($result));
        $this->addError("Error");
        return false;
        
    }

    public function ResendConfigurationEmail() {

        // Not working...

        $this->log('Run ResendConfigurationEmail');
        if (!$this->details['option4']['value']) {

        	$this->log('No SSL Order exists for this product');
            $this->addError('No SSL Order exists for this product');
            return false;
            
        }

        $postfields = array();	
        //$postfields['api_key'] = $this->api_key;
        $postfields["order_toke"] = $this->details['option11']['value'];
        $postfields["email_type"] = 'Fulfillment';

        $result = $this->SendCommand('Admin Area', 'order', "resendemail.json", $postfields);
        $this->addInfo(json_encode($result));
        //$this->addInfo('Resendemail to: '. $result['end_customer_email']);
        return true;

    }


    
    public function SendCommand($interface, $type, $action, $postfields) {
        if($this->test_mode){
            $url = "https://test-api2.servertastic.com/".$type."/".$action;
        }else{
            //$url = "https://api.servertastic.com/ssl/$type/$action";
            //Just in case
            $url = "https://test-api2.servertastic.com/".$type."/".$action;
        }

        $data = http_build_query($postfields);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.'?'.$data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_POST, 1);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result, true);
        return $result;   
    }

}