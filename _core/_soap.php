<?php

    function _SOAP_SentRemoteCommand($SOAPUser, $SOAPPassword, $RealmID, $COMMAND) {
        $SOAP = new SOAP(array(
        "soap_user" => "". $SOAPUser ."",
        "soap_pass" => "". $SOAPPassword ."",
        "soap_port" => "". _SOAPPSwitch($RealmID) ."",
        "addr" => "". _SOAPHSwitch($RealmID) ."",
        "uri" => "". _SOAPURISwitch($RealmID) .""));
        $SOAP->fetch($COMMAND);
        //echo "<br/>". $SOAP->fetch("". $COMMAND ."") ."<br/>";
        unset($SOAP);
    }

    class SOAP {
        private $client = NULL;
    public
        function __construct($conArr) {
            if(!$this->connect($conArr['soap_user'], $conArr['soap_pass'], $conArr['addr'], $conArr['soap_port'], $conArr['uri']))
                die("SOAP UNABAIBLE CONNECT");
        }
    public
        function connect($soapUser, $soapPass, $soapHost, $soapPort, $soap_uri) {
            $this->client = new SoapClient(NULL, array(
                    "location"      => "http://".$soapHost.":".$soapPort."/",
                    "uri"           => "urn:". $soap_uri ."",
                    "user_agent"    => "aframework",
                    "style"         => SOAP_RPC,
                    "login"         => $soapUser,
                    "password"      => $soapPass,
                    "trace"         => 1,
                    "exceptions"    => 0
                )
            );

            if(is_soap_fault($this->client)) {
                $client = $this->client;
                throw new Exception("SOAP Error | Faultcode: ".$client->faultcode." | Faultstring: ".$client->faultstring);
            }
            return true;
        }
    public
        function disconnect() {
            if($this->client != NULL)
                $this->client = NULL;
            else
                return false;
            return true;
        }
    public
        function fetch($command) {
            $client = $this->client;
            if($client == NULL)
                return false;
            $params = func_get_args();
            array_shift($params);
            $command = vsprintf($command, $params);
            $result = $client->executeCommand(new SoapParam($command, "command"));
            if(is_soap_fault($client)) {
                throw new Exception("SOAP Error | Faultcode: ".$client->faultcode." | Faultstring: ".$client->faultstring);
            }
            return $this->getResult($client->__getLastResponse());
        }
    private
        function getResult($xmlresponse) {
            //echo "SOAP CLASS SAY:" . $xmlresponse;
            $start = strpos($xmlresponse,'<?xml');
            $end = strrpos($xmlresponse,'>');
            $soapdata = substr($xmlresponse,$start,$end-$start+1);
            $xml_parser = xml_parser_create();
            xml_parse_into_struct($xml_parser, $soapdata, $vals, $index);
            xml_parser_free($xml_parser);
            if(array_key_exists("RESULT",$index))
                $result = $vals[$index['RESULT'][0]]['value'];
            if(!empty($result))
                return $result;
            return "SOAP Server do not respond!";
        }
    }
?>