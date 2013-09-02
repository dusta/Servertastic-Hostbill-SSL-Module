<?php
/**********************************************************************
 *  ServerTastic HostBill Module. Custom developed for ServerTastic.
 *  (18.07.12)
 * *
 *
 *  CREATED BY MODULESGARDEN       ->        http://modulesgarden.com
 *  CONTACT                        ->       contact@modulesgarden.com
 *
 *
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

class servertastic_ssl extends HostingModule {

    protected $description='Servertastic SSL Module';

    protected $options = array(
        'option1'   => array(
            'name'      => 'certificationtype',
            'value'     => '',
            'type'      => 'select',
            'default'   => array(
                'RapidSSL|4',
                'RapidSSLWildcard|4',
                'QuickSSLPremium|4',
                'TrueBizID|4',
                'TrueBizIDWildcard|4',
                'TrueBizIDEV|2',
                'TrueBizIDMD|4',
                'TrueBizIDEVMD|2',
                'SecureSite|3',
                'SecureSiteEV|2',
                'SecureSitePro|3',
                'SecureSiteProEV|2',
                'SGCSuperCerts|3',
                'SSLWebServer|3',
                'SSLWebServerWildcard|2',
                'SSLWebServerEV|2',
                'SSL123|2'
                )

            ),
        );
    
    protected $lang=array(
        'english'=>array(
            'certificationtype' => 'Certification Type',
            )
        );   
    
        protected $details = array(
            'option1' =>array (
                    'name'      => 'username',
                    'value'     => false,
                    'type'      => 'input',
                    'default'   => false
            ),
            'option2' =>array (
                    'name'      => 'password',
                    'value'     => false,
                    'type'      => 'input',
                    'default'   => false
            ),
            'option3' =>array (
                    'name'      => 'domain',
                    'value'     => false,
                    'type'      => 'input',
                    'default'   => false
            ),
            'option4' =>array (
                    'name'      => 'referencehostbill',
                    'value'     => false,
                    'type'      => 'hidden',
                    'default'   => false
            ),
            'option5' =>array (
                    'name'      => 'referenceservertastic',
                    'value'     => false,
                    'type'      => 'hidden',
                    'default'   => false
            ),
            'option6' =>array (
                    'name'      => 'status',
                    'value'     => false,
                    'type'      => 'hidden',
                    'default'   => false
            ),
            'option7' =>array (
                    'name'      => 'SAN Count',
                    'value'     => false,
                    'type'      => 'input',
                    'default'   => false
            ),
            'option8' =>array (
                    'name'      => 'Certyficate type',
                    'value'     => false,
                    'type'      => 'input',
                    'default'   => false
            ),
            'option9' =>array (
                    'name'      => 'Years',
                    'value'     => false,
                    'type'      => 'input',
                    'default'   => false
            ),
            'option10' =>array (
                    'name'      => 'Servers Count',
                    'value'     => false,
                    'type'      => 'input',
                    'default'   => false
            )
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
        'ssl'       => 'Test mode',
    );
        
    protected $commands = array(
        'Create',
        'Terminate',
        'ResendConfigurationEmail'
    );

    private $api_key;
    private $test_mode;

    public function connect($connect) { 

        $this->api_key = $connect['username'];
        $this->test_mode = $connect['secure'];
        
    }
    
    public function IsRecorded(){
        if ($this->details['option6']['value'] && $this->details['option6']['value']!='Cancelled') {
            return true;
        }
        return false;
    }

    public function CreateReference(){
        $serviceid=$this->product_details['id'];

        if (!$this->details['option4']['value']) {
            return $serviceid;
        }

        if ($this->details['option6']['value']!='Cancelled') {
            return $this->details['option4']['value'];
        }

        
        if ($this->details['option6']['value']=='Cancelled') {
            if(strpos($this->details['option4']['value'],'.')){
                $temp=explode('.',$this->details['option4']['value']);
                return $temp[0].'.'.($temp[1]+1);
            }
            else
                return $this->details['option4']['value'].'.2';
        }
    }
    
    public function SendCommand($interface,$type,$action,$postfields) {

        if($this->test_mode){
            $url = "https://test-api.servertastic.com/ssl/$type/$action";
        }else{
            $url = "https://api.servertastic.com/ssl/$type/$action";
        }

        $ch = curl_init();
	$url .= "?";
	foreach($postfields as $field => $data){
		$url .= "$field=".rawurlencode($data)."&";
	}
	curl_setopt($ch, CURLOPT_URL, rtrim($url, '&'));
	curl_setopt($ch, CURLOPT_TIMEOUT, 100);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
	$data = curl_exec($ch);
        
	if (curl_errno($ch)) {
            
		$result["response"]["status"]   = "ERROR";
		$result["response"]["message"]  = "CURL Error: ".curl_errno($ch)." - ".curl_error($ch);
                
                $this->addError("CURL Error: ".curl_errno($ch)." - ".curl_error($ch));
                return false;
                
	}
        else {
            $result = $this->xml2array($data);
            if($result["response"]["error"]) {
                
                $result["response"]["status"]   = "ERROR";
                $result["response"]["message"]  = "API Error: ".$result["response"]["error"]["code"].' - '.$result["response"]["error"]["message"];
                
                $this->addError("API Error: ".$result["response"]["error"]["code"].' - '.$result["response"]["error"]["message"]);
                return false;
                
            }
	}
	curl_close($ch);

        return $result;       
    }

    private function getYears($cycle){
        
        switch($cycle){
            case 'Annually':
                return 1;
                break;
            case 'Biennially':
                return 2;
                break;
            case 'Triennially':
                return 3;
                break;
            case 'Quadrennially':
                return 4;
                break;
            default:
                return false;
                break;
        }
        
    }
    
    public function Create() { 
        
        $sancount           = $this->details[option7][value];
        $certyficatetype    = $this->details[option8][value];
        $serverscount       = $this->details[option10][value];
        if ($this->IsRecorded()) {
            
            $this->addError("An SSL Order already exists for this order");
            return false;
            
        }
        $maxservercountarr                      = array();
        $maxservercountarr["SecureSiteProEV"]   = 499;
        $maxservercountarr["SecureSite"]        = $maxservercountarr["SecureSiteEV"]
                                                = $maxservercountarr["SecureSitePro"]
                                                = $maxservercountarr["SGCSuperCerts"]
                                                = $maxservercountarr["SSLWebServer"]
                                                = $maxservercountarr["SSLWebServerWildcard"]
                                                = $maxservercountarr["SSLWebServerEV"]
                                                = $maxservercountarr["SSL123"]
                                                = 500;

        $certtype                               = ($certyficatetype) ? $certyficatetype : $this->options['option1']['value'];
        $certproduct                            = current(explode("|",$certtype,2));	
        $maxyears                               = end(explode("|",$certtype,2));
        $years                                  = $this->getYears((string)$this->account_details['billingcycle']);
        if (!$years || $years>$maxyears) {
            
            $this->addError("Wrong period for this SSL Order");
            return false;
            
        }
        $certyears                              = $years;
        
	if(!$serverscount) 
            $serverscount = 1;
        
	$servercount                            = ($maxservercountarr[$certproduct]) ? ($serverscount <= $maxservercountarr[$certproduct]) ? $serverscount : $maxservercountarr[$certproduct] : '1';
	$productcode                            = $certproduct.'-'.($certyears*12);
	
	//Deal with the SAN counts (min|max)
	$sancountarr                = array();
	$sancountarr["SecureSite"]  = $sancountarr["SecureSiteEV"]
                                    = $sancountarr["SecureSitePro"]
                                    = $sancountarr["SecureSiteProEV"]
                                    = '0|24';
	$sancountarr["TrueBizIDMD"] = $sancountarr["TrueBizIDEVMD"]
                                    = '4|24';
	
	if(array_key_exists($certproduct,$sancountarr)){
            
            $min_san_count = current(explode("|",$sancountarr[$certproduct],2));	
            $max_san_count = end(explode("|",$sancountarr[$certproduct],2));
            
            if(!isset($sancount)){
                
                $this->addError("A SAN count value is required for this product");
                return false;
                
            }else{
                
                $san_count = $sancount;
                if(($san_count<$min_san_count)||($san_count>$max_san_count)){
                    
                    $this->addError("A SAN count value is required for this product");
                    return false;
                    
                }
            }
	}else{
		$san_count=0;
	}
	$postfields                                 = array();
	$postfields["st_product_code"]              = $productcode;
	$postfields["api_key"]                      = $this->api_key;
	$postfields["end_customer_email"]           = $this->client_data[email];
	$postfields["san_count"]                    = $san_count;
	$postfields["integration_source_id"]        = 3;
	if($servercount)
            $postfields["server_count"]             = $servercount;
        $postfields["reseller_unique_reference"]    = $this->CreateReference();
	
	$result = $this->SendCommand("System","order.xml","place",$postfields);

	if ($result["response"]["status"] == "ERROR"){
            
            $this->addError($result["response"]["message"]);
            return false;
            
        }
	if ($result["response"]["success"] == "Order placed") {
            
            $orderid = $result["response"]["reseller_order_id"];
            
            if(!$orderid){
                
                $this->addError('Unable to obtain Order-ID');
                return false;
                
            }
	
            $this->addInfo('Account has been created.');
            
            $this->details['option4']['value'] = $this->CreateReference();
            $this->details['option5']['value'] = $orderid;
            $this->details['option6']['value'] = 'Awaiting Configuration';
            return true;
		
	}
    
	if(!$orderid){
            
            $this->addError('Unable to obtain Order-ID');
            return false;
            
        }
    }
    
    public function Terminate() {
        
        if ($this->details['option6']['value']!='Awaiting Configuration') {
            
            $this->addError("SSL Either not Provisioned or Not Awaiting Configuration so unable to cancel");
            return false;
            
        }

        $postfields                         = array();
        $postfields["api_key"]              = $this->api_key;
        $postfields["reseller_order_id"]    = $this->details['option5']['value'];

        $result = $this->SendCommand("System","order","cancel",$postfields);

        if ($result["response"]["status"] == "ERROR"){
            
            $this->addError("SSL Either not Provisioned or Not Awaiting Configuration so unable to cancel");
            return false;
            
        }
        $this->details['option6']['value']='Cancelled';

        $this->addInfo('Account has been terminated.');
        return true;
        
    }

    public function ResendConfigurationEmail() {
        
        if (!$this->details['option4']['value']) {
            
            $this->addError('No SSL Order exists for this product');
            return false;
            
        }

            $postfields                         = array();	
            $postfields["api_key"]              = $this->api_key;
            $postfields["reseller_order_id"]    = $this->details['option5']['value'];
            $postfields["email_type"]           = "Invite";

            $result = $this->SendCommand("Admin Area","order","resendemail",$postfields);

    }

        public function xml2array($contents, $get_attributes = 1, $priority = 'tag') {

        $parser = xml_parser_create('');

        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);
        if (!$xml_values)
            return; //Hmm...
        $xml_array          = array ();
        $parents            = array ();
        $opened_tags        = array ();
        $arr                = array ();
        $current            = & $xml_array;
        $repeated_tag_index = array ();
        
        foreach ($xml_values as $data)
        {
            unset ($attributes, $value);
            extract($data);
            $result = array ();
            $attributes_data = array ();
            if (isset ($value))
            {
                if ($priority == 'tag')
                    $result = $value;
                else
                    $result['value'] = $value;
            }
            if (isset ($attributes) and $get_attributes)
            {
                foreach ($attributes as $attr => $val)
                {
                    if ($priority == 'tag')
                        $attributes_data[$attr] = $val;
                    else
                        $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                }
            }
            if ($type == "open")
            {
                $parent[$level -1] = & $current;
                if (!is_array($current) or (!in_array($tag, array_keys($current))))
                {
                    $current[$tag] = $result;
                    if ($attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    $current = & $current[$tag];
                }
                else
                {
                    if (isset ($current[$tag][0]))
                    {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        $repeated_tag_index[$tag . '_' . $level]++;
                    }
                    else
                    {
                        $current[$tag] = array (
                            $current[$tag],
                            $result
                        );
                        $repeated_tag_index[$tag . '_' . $level] = 2;
                        if (isset ($current[$tag . '_attr']))
                        {
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset ($current[$tag . '_attr']);
                        }
                    }
                    $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                    $current = & $current[$tag][$last_item_index];
                }
            }
            elseif ($type == "complete")
            {
                if (!isset ($current[$tag]))
                {
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $attributes_data)
                        $current[$tag . '_attr'] = $attributes_data;
                }
                else
                {
                    if (isset ($current[$tag][0]) and is_array($current[$tag]))
                    {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                        if ($priority == 'tag' and $get_attributes and $attributes_data)
                        {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag . '_' . $level]++;
                    }
                    else
                    {
                        $current[$tag] = array (
                            $current[$tag],
                            $result
                        );
                        $repeated_tag_index[$tag . '_' . $level] = 1;
                        if ($priority == 'tag' and $get_attributes)
                        {
                            if (isset ($current[$tag . '_attr']))
                            {
                                $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                                unset ($current[$tag . '_attr']);
                            }
                            if ($attributes_data)
                            {
                                $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                    }
                }
            }
            elseif ($type == 'close')
            {
                $current = & $parent[$level -1];
            }
        }
        return ($xml_array);
    }
    
}
?>
