<?php 
/*---------------------------------------------------------------------------\
|                   对curl的封装类，以便于使用                                              |
|----------------------------------------------------------------------------|
|         Copyright (C) 2010, Beijing ChenHui. All rights reserved           |
|         Version: 1.0                                                                                 |
|                                                                                                             | 
\---------------------------------------------------------------------------*/

class MyCurl 
{
     protected $_useragent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1';
     protected $_accept;
     protected $_accept_language;
     protected $_accept_charset;
     
     protected $_url;
     protected $_followlocation;
     protected $_timeout;
     protected $_maxRedirects;
     protected $_cookieFileLocation = './cookie.txt';
     protected $_cookieFields;
     protected $_post;
     protected $_put;
     protected $_delete;
     protected $_postFields;
     protected $_referer ="http://www.google.com";
     
     protected $_getHeaderOut=false;
     protected $_headerOut;

     protected $_session;
     protected $_webpage;
     protected $_includeHeader;
     protected $_noBody;
     protected $_status;
     protected $_binaryTransfer;
     public    $authentication = 0;
     public    $auth_name      = '';
     public    $auth_pass      = '';

     public function needGetHeaderOut(){
       $this->_getHeaderOut = true;
     }
     
     public function getHeaderOut(){
       return $this->_headerOut;
     }
     
     
     public function useAuth($use){
       $this->authentication = 0;
       if($use == true) $this->authentication = 1;
     }

     public function setName($name){
       $this->auth_name = $name;
     }
     public function setPass($pass){
       $this->auth_pass = $pass;
     }

     public function __construct($url,$followlocation = true,$timeOut = 30,$maxRedirecs = 4,$binaryTransfer = false,$includeHeader = true,$noBody = false)
     {
         $this->_url = $url;
         $this->_followlocation = $followlocation;
         $this->_timeout = $timeOut;
         $this->_maxRedirects = $maxRedirecs;
         $this->_noBody = $noBody;
         $this->_includeHeader = $includeHeader;
         $this->_binaryTransfer = $binaryTransfer;

         $this->_cookieFileLocation = dirname(__FILE__).'/cookie.txt';

     }

     public function setReferer($referer){
       $this->_referer = $referer;
     }
     
     public function setTempCookie($name,$val)
     {
        $this->_cookieFields .= "$name=$val;";
     }
     
     public function clearTempCookie()
     {
        $this->_cookieFields=NULL ;
     }

     public function setCookiFileLocation($path)
     {
        $this->_cookieFileLocation = $path;
     }

     public function setPost ($postFields)
     {
        $this->_post = true;
        $this->_postFields = $postFields;
     }
     
     public function setDelete ()
     {
        $this->_delete = true;
     }
     
     public function setPut($postFields)
     {
        $this->_put = true;
        $this->_postFields = $postFields;
     }
     

     public function setUserAgent($userAgent)
     {
         $this->_useragent = $userAgent;
     }

     public function setAccept($accept)
     {
         $this->_accept = $accept;
     }
     
     public function setAcceptLanguage($accept_language)
     {
         $this->_accept_language = $accept_language;
     }
     
     public function setAcceptCharset($accept_charset)
     {
         $this->_accept_charset = $accept_charset;
     }
 
     public function createCurl($url = 'nul')
     {
        static $curl_loops = 0;
        static $curl_max_loops = 20;
        if ($curl_loops++ >= $curl_max_loops)
        {
            $curl_loops = 0;
            return false;
        } 
      
        if($url != 'nul'){
          $this->_url = $url;
        }

         $s = curl_init();

         if( strcasecmp(substr($this->_url,0,5),'https')==0  )
         { 
            //如果url是以https起始
            curl_setopt($s, CURLOPT_SSL_VERIFYHOST, 1); 
            curl_setopt($s, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($s, CURLOPT_CAPATH, $my_lib_path);
            curl_setopt($s, CURLOPT_CAINFO, "/common/curl_ssl_cacert.pem"); 
         }
        
         curl_setopt($s,CURLOPT_URL,$this->_url);
         curl_setopt($s,CURLOPT_HTTPHEADER,array('Expect:'));
         curl_setopt($s,CURLOPT_TIMEOUT,$this->_timeout);
         curl_setopt($s,CURLOPT_MAXREDIRS,$this->_maxRedirects);
         curl_setopt($s,CURLOPT_RETURNTRANSFER,true);
         curl_setopt($s,CURLOPT_FOLLOWLOCATION,false);
         
         if($this->_getHeaderOut)
            curl_setopt($s,CURLINFO_HEADER_OUT,true);

         //curl_setopt($s,CURLOPT_COOKIESESSION,true);
          
         curl_setopt($s,CURLOPT_COOKIEJAR,$this->_cookieFileLocation);
         curl_setopt($s,CURLOPT_COOKIEFILE,$this->_cookieFileLocation);

         if(strlen($this->_cookieFields)>0)
         {
             //echo 'use cookie:'.$this->_cookieFields.'<br>';
             curl_setopt($s,CURLOPT_COOKIE,$this->_cookieFields);
         }
         
         if($this->authentication == 1){
           curl_setopt($s, CURLOPT_USERPWD, $this->auth_name.':'.$this->auth_pass);
         }
         
         if($this->_delete)
         {
         /*CURLOPT_CUSTOMREQUEST 
A custom request method to use instead of "GET" or "HEAD" when doing a HTTP request. This is 

useful for doing "DELETE" or other, more obscure HTTP requests. Valid values are things like 

"GET", "POST", "CONNECT" and so on; i.e. Do not enter a whole HTTP request line here. For 

instance, entering "GET /index.html HTTP/1.0\r\n\r\n" would be incorrect. 
Note: Don't do this without making sure the server supports the custom request method first.
*/
            curl_setopt($s,CURLOPT_CUSTOMREQUEST,'DELETE');
         }
         else if($this->_post)
         {
             curl_setopt($s,CURLOPT_POST,true);
             curl_setopt($s,CURLOPT_POSTFIELDS,$this->_postFields);
         }
         else if($this->_put)
         {
             $fields = (is_array($this->_postFields)) ? http_build_query($this->_postFields) : $this->_postFields; 
             curl_setopt($s,CURLOPT_PUT,true);
             //curl_setopt($s,CURLOPT_HTTPHEADER, array('Content-Length: ' . strlen($fields))); 
             curl_setopt($s,CURLOPT_POSTFIELDS,$fields);
         }

         if($this->_includeHeader)
         {
             curl_setopt($s,CURLOPT_HEADER,true);
         }

         if($this->_noBody)
         {
             curl_setopt($s,CURLOPT_NOBODY,true);
         }
         /*
         if($this->_binary)
         {
             curl_setopt($s,CURLOPT_BINARYTRANSFER,true);
         }
         */
         curl_setopt($s,CURLOPT_USERAGENT,$this->_useragent);
         //curl_setopt($s,CURLOPT_REFERER,$this->_referer);
         
         $headers=array();
         if($this->_accept)
         {
             $headers[] = 'Accept:'.$this->_accept; 
         }
         if($this->_accept_language)
         {
             $headers[] = 'Accept-Language:'.$this->_accept_language; 
         }
         if($this->_accept_charset)
         {
             $headers[] = 'Accept-Charset:'.$this->_accept_charset; 
         }
         curl_setopt($s,CURLOPT_HTTPHEADER,$headers);
         

         $this->_webpage = curl_exec($s);
         $this->_httpRespCode = curl_getinfo($s,CURLINFO_HTTP_CODE);
         
         if($this->_getHeaderOut)
            $this->_headerOut = curl_getinfo($s,CURLINFO_HEADER_OUT);
            
         $this->_status = curl_getinfo($s) ;
         $this->_status['errno'] = curl_errno($s) ;
         $this->_status['error'] = curl_error($s) ;
      
         curl_close($s);
         
         //clear last post fields
         $this->_post = false;
         $this->_delete=false;
         $this->_postFields = NULL;
         
         //process riderest
         if($this->_httpRespCode == 301 || $this->_httpRespCode == 302)
         {
            //debugSimpleLogger($this->_webpage);
            preg_match('/Location:(.*?)\n/', $this->_webpage, $matches);
            $arrayUrl = @parse_url(trim(array_pop($matches)));
            
            if ($arrayUrl)
            {
                if(isset($arrayUrl['scheme']))
                   $new_url = $arrayUrl['scheme'] . '://' . $arrayUrl['host'] . $arrayUrl['path'] . (isset($arrayUrl['query']) ? '?'.$arrayUrl['query'] : '');
                else
                {
                    $arrayLastUrl = @parse_url(trim($this->_url));
                    $new_url = $arrayLastUrl['scheme'].'://'
                        .$arrayLastUrl['host']
                        .(substr($arrayUrl['path'],0,1)=='/'?$arrayUrl['path']:$arrayLastUrl['path'].'/'.$arrayUrl['path'])
                        .(isset($arrayUrl['query']) ? '?'.$arrayUrl['query'] : '');
                }
                //echo "riderect to ".print_r($new_url,true)."<br>";
                if($this->_followlocation) //需要自动跳转
                    return $this->createCurl($new_url);
                else  //否则返回要跳转的地址
                    return $new_url;
            }
         } 
         
         $curl_loops = 0;

         return true;
     }

   public function getHttpRespCode()
   {
        return $this->_httpRespCode;
   }
   
   public function hasError()
   {
        if (isset($this->_status['error']))
        {
            return (empty($this->_status['error']) ? false : $this->_status['error']) ;
        }
        else
        {
	        return false ;
        }
    }

   public function __tostring(){
      return $this->_webpage;
   }
   
   /*
   function __destruct()
   {
       debugSimpleLogger('mycurl.__destruct() called');
       unlink($this->_cookieFileLocation);
   }
   */
   
} 