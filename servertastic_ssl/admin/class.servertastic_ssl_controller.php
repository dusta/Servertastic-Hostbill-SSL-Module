<?php

class servertastic_ssl_controller extends HBController {

    public function accountdetails($params) {

        if($params['account']['status'] == 'Active') {
            //var_dump($params['account']);

            $status = $params['account']['extra_details']['option6']
            
            $configuration = false;
            if($status == 'Awaiting Configuration' OR $status == 'Order Placed' OR $status == 'Review Order') {
                $configuration = true;
            }

            if($status == 'Cancelled' OR $status == 'Completed'){
                //Something todo
            }


            //if($params['account']['extra_details']['option4']){
                // if($_POST["newapproveremail"]) {
                //     $postfields = array();	
                //     $postfields["api_key"] = $api_key;
                //     $postfields["reseller_order_id"] = $params['account']['extra_details']['option5'];
                //     $postfields["email"] = $_POST["newapproveremail"];
                //     $result = $this->SendCommand("Client Area", "order", "changeapproveremail", $postfields, $params);
                // }

                //$status = $params['account']['status'];
                //$postfields = array();
                //$postfields["api_key"] = $api_key;
                //$postfields["reseller_order_id"] = 'STR_37_236849'.$params['account']['extra_details']['option5'];
                //$postfields['order_token'] = '236849';
                //$params['id'] - referece number

                //echo '<pre>';
                // var_dump($this);

                //$result = $this->SendCommand("Client Area ", "order", "review", $postfields, $params);
                //echo '<pre>';
                //var_dump($result);
                //die();

                // if($result['response']["status"] == "ERROR"){
                //     $remotestatus =  $result['response']["message"];
                    
                // }else{

                //     $awaitingsslconfiguration = false;

                //     if($result['response']['order_status'] == 'Order Placed'){
                //         $remotestatus = $result['response']['order_status'];
                //         $awaitingsslconfiguration = true;
                //     }

                    // if( ($result['response']['order_status'] != "Awaiting Configuration" && $result['response']['order_status'] == "Order placed") || ($status != "Awaiting Configuration" && $remotestatus == "Invite Available") ) {
                    //         $this->module['details']['option6']='Awaiting Configuration';
                    //         $awaitingsslconfiguration = true;
                    // }

                    // if( ($result['response']['order_status'] != "Completed" && $result['response']['order_status'] == "Awaiting Customer Verification") || ($status != "Completed" && $remotestatus == "Awaiting Provider Approval") || ($status != "Completed" && $remotestatus == "Queued") || ($status != "Completed" && $remotestatus == "Completed") || ($status != "Completed" && $remotestatus == "ServerTastic Review") ) {
                    //         $this->module['details']['option6']='Completed';
                    // }


                    // if($result['response']['order_status'] == 'Cancelled'){
                    //     $this->module['details']['option6'] = 'Cancelled';
                    //     $awaitingsslconfiguration = false;
                    // }

                    //if($awaitingsslconfiguration == true)
                        //$remotestatus .= ' - <a href="'.$result['response']['invite_url'].'" target="_blank">Configure Now</a>'; 

                //}

                $order = array();
                $order['status'] = $status;
                $order['url'] = array();
                if($configuration == true)
                    $order['url']['configure'] = $params['account']['extra_details']['option12']; 

                $this->template->assign('order', $order);
                $this->template->assign('custom_template', APPDIR_MODULES.'Hosting/servertastic_ssl/admin/template.tpl');

            }
        //}
    }

}