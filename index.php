<?php

/*
Project Name    : XXX
Author          : Naveen Karduri
Title           : Senior Programmer Analyst
Plugin Type     : Project Specific
Child and Family Research Institute
Description :

condition 1:

Creates a new subject and copies data in “IR” (PID 002) (child project )
when the following conditions are met in “Data collection ”  (PID 001): (Parent project)

DAG = 'In' and [data_collection_form_complete] = '2'

condition 2:

Creates a new subject and copies data in “Lower Mainland” (PID 003) (child project)
when the following conditions are met in “Data Collection” (PID 001): (Parent project)

DAG = 'MA' and [data_collection_form_complete] = '2'
 
*/
 

require_once('RestCallRequest.php');

//Obtain details from DET post  - Do not change this part 

$project_id   = voefr('project_id');
$instrument   = voefr('instrument');
$record       = voefr('record');
$eventname    = voefr('redcap_event_name');
$instrument_status = voefr($instrument."_complete");
$DAG          = voefr('redcap_data_access_group');

#check if project ID or Record ID is empty

if(empty($project_id) or empty($record))
{
 exit;   
}

if($instrument == 'data_collection_form')
{   
#################################### Project Settings ##############################################

//API URL  - Do not change this URL
$apiurl         = '';  // api url 

// Data Collection
$parentapi          = '';  // API token 

//Interior Review
$interiorapi        = ''; // API token 

//Lower Mainland Review
$lowermainlandapi    = ''; // API token

//Following forms data is copied to sub study when conditions are met.
$forms          = array('data_collection_form');
 
#############################################################################################
  
# an array containing all the elements that must be submitted to the API
$data = array('content' => 'record', 'format' => 'json', 'records' => $record,'token' => $parentapi,'forms'=>$forms);

# create a new API request object
$request = new RestCallRequest($apiurl, 'POST', $data);

# initiate the API request
$request->execute();  
              
# get the content type of the data being returned
$response = $request->getResponseInfo();
$type = explode(";", $response['content_type']);
$contentType = $type[0];

#print the data to the screen
$result_data =  $request->getResponseBody(); 
                              
//print_r($result_data);
                              
$array_object = json_decode($result_data); // Decode your array if it is not done so.

$array = json_decode(json_encode($array_object), true);  

foreach($array as $key => $array_value)
 {
     $result_array = $array_value;      // Result of the Subject ID  
 } 

if($DAG == 'interior' && $result_array["$instrument"."_complete"] == '2')
{
      
$result_array['record_id'] = $record.'--1';

$result_data1 = json_encode(array($result_array),true);
  
#send data to Interior Study
           
# an array containing all the elements that must be submitted to the API
$data = array('content' => 'record', 'format' => 'json', 'token' =>$interiorapi,'data' => $result_data1,'overwriteBehavior'=>'overwrite');
            
# create a new API request object
$request = new RestCallRequest($apiurl, 'POST', $data);

# initiate the API request
$request->execute();
 
$result = $request->getResponseBody();  

$result_array['record_id'] = $record.'--2';

$result_data1 = json_encode(array($result_array),true);
  
#send data to Interior Study
           
# an array containing all the elements that must be submitted to the API
$data = array('content' => 'record', 'format' => 'json', 'token' =>$interiorapi,'data' => $result_data1,'overwriteBehavior'=>'overwrite');
            
# create a new API request object
$request = new RestCallRequest($apiurl, 'POST', $data);

# initiate the API request
$request->execute();
 
$result = $request->getResponseBody();  

# the following line will print out the entire HTTP request object 
# good for testing purposes to see what is sent back by the API and for debugging
//echo '<pre>' . print_r($request, true) . '</pre>' ;
           
}

else if($DAG == 'lower_mainland' && $result_array["$instrument"."_complete"] == '2')
{
 
 $result_array['record_id'] = $record.'--1';

 $result_data1 = json_encode(array($result_array),true);   
    
 #send data to Lower Mainland Study
           
# an array containing all the elements that must be submitted to the API
$data = array('content' => 'record', 'format' => 'json', 'token' =>$lowermainlandapi,'data' => $result_data1,'overwriteBehavior'=>'overwrite');
            
# create a new API request object
$request = new RestCallRequest($apiurl, 'POST', $data);

# initiate the API request
$request->execute();
 
$result = $request->getResponseBody();  

# the following line will print out the entire HTTP request object 
# good for testing purposes to see what is sent back by the API and for debugging
//echo '<pre>' . print_r($request, true) . '</pre>' ;

 $result_array['record_id'] = $record.'--2';

 $result_data1 = json_encode(array($result_array),true);   
    
 #send data to Lower Mainland Study
           
# an array containing all the elements that must be submitted to the API
$data = array('content' => 'record', 'format' => 'json', 'token' =>$lowermainlandapi,'data' => $result_data1,'overwriteBehavior'=>'overwrite');
            
# create a new API request object
$request = new RestCallRequest($apiurl, 'POST', $data);

# initiate the API request
$request->execute();
 
$result = $request->getResponseBody();  
   
} 
}       
 //DET function
 function voefr($var) {
    $result = isset($_REQUEST[$var]) ? $_REQUEST[$var] : "";
    return $result;
}

 
?>
