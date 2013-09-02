<?php
    class servertastic_ssl_controller extends HBController {
        public function accountdetails($params) {

            if($params['account']['status']=='Active') {
                if($_POST["newapproveremail"]) {
			$postfields = array();	
			$postfields["api_key"] = $params['account']['options']['option1'];
                        $postfields["reseller_order_id"] = $params['account']['extra_details']['option5'];
			$postfields["email"] = $_POST["newapproveremail"];
			$result = $this -> SendCommand("Client Area","order","changeapproveremail",$postfields,$params);
                }

                $status = $params['account']['status'];
		$postfields = array();	
		$postfields["api_key"] = $params['account']['options']['option1'];
                $postfields["reseller_order_id"] = $params['account']['extra_details']['option5'];

		$result = $this->SendCommand("Client Area ","order","review",$postfields,$params);
                if ($result["response"]["status"] == "ERROR"){
                    
                    $this->template->assign('curlerror',$result["response"]["message"]);
                    $this->template->render(APPDIR_MODULES.'Hosting/servertastic_ssl/user/error.tpl');
                    
                }
		else{
                    $remotestatus = $result["response"]["order_status"];
                    $inviteurl = $result["response"]["invite_url"];

                    if($remotestatus == "Order placed" || $remotestatus == "Invite Available") {
                            $awaitingsslconfiguration = true;
                    }

                    if( ($status != "Awaiting Configuration" && $remotestatus == "Order placed") || ($status != "Awaiting Configuration" && $remotestatus == "Invite Available") ) {
                            $this->module['details']['option6']='Awaiting Configuration';
                            $awaitingsslconfiguration = true;
                    }

                    if( ($status != "Completed" && $remotestatus == "Awaiting Customer Verification") || ($status != "Completed" && $remotestatus == "Awaiting Provider Approval") || ($status != "Completed" && $remotestatus == "Queued") || ($status != "Completed" && $remotestatus == "Completed") || ($status != "Completed" && $remotestatus == "ServerTastic Review") ) {
                            $this->module['details']['option6']='Completed';
                    }

                    if( ($status != "Cancelled" && $remotestatus == "Cancelled") || ($status != "Cancelled" && $remotestatus == "Roll Back") ) {
                            $this->module['details']['option6']='Cancelled';
                    }

                    if($awaitingsslconfiguration) { $remotestatus .= ' - <a href="'.$inviteurl.'" target="_blank">Configure Now</a>'; }

                    $this->template->assign('product',$params['account']['catname'].' - '.$params['account']['name']);
                    $this->template->assign('status',$params['account']['status']);
                    $this->template->assign('date_created',$params['account']['date_created']);
                    $this->template->assign('billingcycle',$params['account']['billingcycle']);
                    $this->template->assign('id',$params['account']['extra_details']['option4']);
                    $this->template->assign('status',$params['account']['status']);
                    $this->template->assign('remotestatus',$remotestatus);
                    $this->template->render(APPDIR_MODULES.'Hosting/servertastic_ssl/user/template.tpl');
                }
                if($params['make']=='submit') {
                }
            }
        }
        public function SendCommand($interface,$type,$action,$postfields,$params) {

            if($params['account']['options']['option3']){
                $url = "https://test-api.servertastic.com/ssl/$type/$action";
            }else{
                $url = "https://api.servertastic.com/ssl/$type/$action";
            }
            $ch = curl_init();
            $url .= "?";
            foreach($postfields as $field => $data){
                    $url .= "$field=".rawurlencode($data)."&";
            }
            $url = substr($url,0,-1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 100);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            if (curl_errno($ch)) {
                    $result["response"]["status"] = "ERROR";
                    $result["response"]["message"] = "CURL Error: ".curl_errno($ch)." - ".curl_error($ch);
            } else {
            $result = $this->xml2array($data);
                    if($result["response"]["error"]) {
                            $result["response"]["status"] = "ERROR";
                            $result["response"]["message"] = "API Error: ".$result["response"]["error"]["code"].' - '.$result["response"]["error"]["message"];
                    }
            }
            curl_close($ch);

            return $result;       
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
            $xml_array = array ();
            $parents = array ();
            $opened_tags = array ();
            $arr = array ();
            $current = & $xml_array;
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