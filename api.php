<?php


include 'dom.php' ;

$addressLink = $_GET['url'];
if (!empty($addressLink))
{
	if (strpos($addressLink, 'https://etherscan.io/address/') !==false)	// true
	{
		$addressLink = $_GET['url'];
	}else
	{
		$addressLink = 'https://etherscan.io/address/' . $_GET['url'];
	}
	getData($addressLink);
}else
{
	echo "NO link to work with ";
}


function getData($url)
{
$opts = array
        (
            'http' => array
            (
                'method'  => 'GET',               
                'header' => array
                (
                    'Connection: close',
                    'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                ), 
            )
        );
    $context  = stream_context_create($opts);

	$html = @file_get_html($url, false, $context);

	$tableNum  ;	
	if (strlen($html) >=0)
	{
		foreach($html->find('span[id=mainaddress]') as $mainAddress) 	  // Address Details 
	{
	    $contractAddress = $mainAddress->innertext;   				 //contrac tAddress
	}	
	
	$tableInfo = $html->find('table', 0); 			// account info table	

	foreach($tableInfo->find('tr') as $rowInfo)
	{	    
	    $tableRowInfo = array(); 					// initialize array to store the cell data from each row
	    foreach($rowInfo->find('td') as $cellInfo)
	    {	        
	        $tableRowInfo[] = $cellInfo->plaintext;		// push the cell's text to the array
	    }
	    $tableDataInfo[] = $tableRowInfo;
	}
	$balance = number_format(floatval($tableDataInfo [1][1]) , 4); 	// ETH Balance:

	$tableBuys = $html->find('table', 2);							// transaction info table

	$rowDataBuys = array();

	foreach($tableBuys->find('tr') as $rowBuys)
	{
	    
	    $flightBuys = array();					// initialize array to store the cell data from each row

	    foreach($rowBuys->find('td') as $cellBuys)
	    {	        
	        $flightBuys[] = $cellBuys->plaintext;				// push the cell's text to the array
	    }
	    $rowDataBuys[] = $flightBuys;
	}	
	

	$tableSells = $html->find('table', 3);				// internal transaction info table

	$rowDataSells = array();

	foreach($tableSells->find('tr') as $rowSells)
	{
	    
	    $flightSells = array();					// initialize array to store the cell data from each row

	    foreach($rowSells->find('td') as $cellSells)
	    {	        
	        $flightSells[] = $cellSells->plaintext;				// push the cell's text to the array
	    }
	    $rowDataSells[] = $flightSells;
	}
	$result  = array( 	'Address' 			=> $contractAddress , 
						'Balance' 			=> $balance ,
						'Usd_Value' 		=> $tableDataInfo [2][1] ,
						'No_Of_Transaction' => $tableDataInfo [3][1] ,
						'Token_Contract' 	=> $tableDataInfo [4][1] ,
						'Buys_Value_1' 		=> number_format(floatval($rowDataBuys [1][6]) , 2),		
						'Buys_Value_2' 		=> number_format(floatval($rowDataBuys [2][6]) , 2),		
						'Buys_Value_3' 		=> number_format(floatval($rowDataBuys [3][6]) , 2),		
						'Buys_Value_4' 		=> number_format(floatval($rowDataBuys [4][6]) , 2),		
						'Buys_Value_5' 		=> number_format(floatval($rowDataBuys [5][6]) , 2),
					);
	if (count($rowDataSells)>2) // Check if Contract has Sells 
	{
		$sellsArr  = array(						
						'Sells_Value_1' 	=> number_format(floatval($rowDataSells [1][6]) , 2),		
						'Sells_Value_2' 	=> number_format(floatval($rowDataSells [2][6]) , 2),		
						'Sells_Value_3' 	=> number_format(floatval($rowDataSells [3][6]) , 2),		
						'Sells_Value_4' 	=> number_format(floatval($rowDataSells [4][6]) , 2),		
						'Sells_Value_5' 	=> number_format(floatval($rowDataSells [5][6]) , 2),
					) ;	
	}else
	{
		$sellsArr  = array(						
						'Sells_Value_1' 	=> "NaN",		
						'Sells_Value_2' 	=> "NaN",
						'Sells_Value_3' 	=> "NaN",
						'Sells_Value_4' 	=> "NaN",
						'Sells_Value_5' 	=> "NaN",
					) ;
	}
	$final = $result + $sellsArr;
	$json = json_encode($final, JSON_UNESCAPED_UNICODE ) ;    
    echo  $json ; 
	
}
	
}
