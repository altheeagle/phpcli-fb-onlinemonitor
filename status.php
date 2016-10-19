<?php
// IP or Name of the Fritz!Box
$fritzbox = "fritz.box";
// refreshrate
$pollrate = 0.5;

for ($i = 1; $i <= INF; $i++) {
    $client = new SoapClient(
      null,
      array(
        'location'   => "http://10.201.240.1:49000/igdupnp/control/WANCommonIFC1",
        'uri'        => "urn:schemas-upnp-org:service:WANCommonInterfaceConfig:1",
        'soapaction' => "",
        'noroot'     => True
      )
    );


    $status = $client->GetCommonLinkProperties();
    /*
    'NewWANAccessType' => string 'DSL' (length=3)
    'NewLayer1UpstreamMaxBitRate' => string '39040000' (length=8)
    'NewLayer1DownstreamMaxBitRate' => string '103880000' (length=9)
    'NewPhysicalLinkStatus' => string 'Up' (length=2)
    */
    $status2 = $client->GetAddonInfos();
    //print_r($status);
    // print_r($status2);
    // Bytes in Bits umrechnen (Ermittelte Werte * 8)
    $ByteSendRate      = $status2['NewByteSendRate'];
    $ByteReceiveRate   = $status2['NewByteReceiveRate'];

    if ( $status['NewPhysicalLinkStatus'] == 'up'){
        $linkColor = "1;31m";
    } else {
        $linkColor = "1;32m";
    }


    echo "                                                                               \r";
    $statusinfo = "\e[1m" . strtoupper($status['NewWANAccessType']) . "\e[0m (\e[" . $linkColor . $status['NewPhysicalLinkStatus'] . "\e[0m)";
    $outputup =  "\e[1mUpstream:\033[0m \e[" . HumanReadable($ByteSendRate) . "/s \e[0m" ;
    $outputdown =   "\e[1mDownstream:\e[0m \e[" . HumanReadable($ByteReceiveRate) . "/s \e[0m";

    echo $statusinfo . " " . $outputup ."\t" . $outputdown . "\r";

    sleep($pollrate);

}


function HumanReadable ($byte){
    
    switch (strlen ($byte)) {
        case (strlen($byte) >= 4 and strlen($byte) <7):
            // KByte
            $color = "1;32m"; //Light Green
            $rate =  round($byte / 1024,2);
            return $color . $rate . " KByte";
            break;
        case (strlen($byte) >= 7 and strlen($byte) <10): 
            //MByte
            $color = "1;33m"; //Yellow
            $rate = round($byte / 1024 / 1024,2);
            return $color . $rate . " MByte";
            break;
        case (strlen($byte) >= 10 and strlen($byte) <13):
            //GByte
            $color = "1;31m"; //Light Red
            $rate = round($byte / 1024 / 1024 / 1024,2);
            return $color . $rate . " GByte";
            break;
        default:
            $color = "1;36m"; //Light Cyan
            $rate = round($byte,2);
            return $color . $rate . " Byte";
    }
    
}