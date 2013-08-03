<?php
require_once 'Config.php';
class MemCached_Wrapper
{
        private $objMemCache;	//memcached实例对象

        public function __construct($strServer='resource')
        {
                $this->objMemCache = new Memcache;
//                $this->objMemCache = new Memcached();

                if (isset(MemCached_Config :: $arrMemCacheServer[$strServer])) {
                        $arrMemServer = MemCached_Config :: $arrMemCacheServer[$strServer];
                        $this->objMemCache->connect($arrMemServer[0]['host'],$arrMemServer[0]['port']);
//                        $this->objMemCache->addServers($arrMemServer);
                }
        }

        public function add($strKey, $strValue, $intExpire = 0)
        {
                return $this->objMemCache->add($strKey, $strValue,0, $intExpire);
        }

        public function get($mixedKey)
        { 
                return $this->objMemCache->get($mixedKey);
        }

        public function set($strKey, $strValue,  $intExpire = 0)
        {
                return $this->objMemCache->set($strKey, $strValue,0, $intExpire);
        }

        public function decrement ($strKey,$intValue = 1)
        {
                return $this->objMemCache->decrement ($strKey,$intValue);
        }

        public function inccrement ($strKey,$intValue = 1)
        {
                return $this->objMemCache->increment ($strKey,$intValue);
        }

        public function flush()
        {
                return $this->objMemCache->flush();
        }

        public function delete($strKey, $intTimeOut = 0)
        {
                return $this->objMemCache->delete($strKey, $intTimeOut);
        }

        public function flushAll  ()
        {
                foreach (Memcache_Config :: $arrMemCacheServer as $arrGroup)
                {
                        $objMemCache = $this->buildMemcacheObj ($arrServer);
                        $objMemCache->flush();
                        unset($objMemCache);
                }
                return true;
        }

        public function deleteAll ($strServer,$strKey,$intTimeOut = 0)
        {
                foreach (MemCached_Config::$arrAllMemCacheServer as $arrGroup)
                {
                        foreach ($arrGroup as $serverName => $arrServer) {
                                if ($serverName === $strServer) {
                                        $objMemCache = $this->buildMemcacheObj ($arrServer);
                                        $objMemCache->delete($strKey, $intTimeOut);
                                        unset ($objMemCache);
                                }
                        }
                }
                return true;
        }

        public function addAll($strKey, $strValue, $bolFlag = false, $intExpire = 0)
        {
                foreach (MemCached_Config::$arrAllMemCacheServer as $arrGroup)
                {
                        foreach ($arrGroup as $serverName  => $arrServer) {
                                if ($serverName === $strServer) {
                                        $objMemCache = $this->buildMemcacheObj ($arrServer);
                                        $objMemCache->add($strKey, $strValue, $bolFlag, $intExpire);
                                        unset ($objMemCache);
                                }
                        }
                }
                return true;
        }

        public function setAll($strKey, $strValue, $bolFlag = false, $intExpire = 0)
        {
                foreach (MemCached_Config::$arrAllMemCacheServer as $arrGroup)
                {
                        foreach ($arrGroup as $serverName => $arrServer) {
                                if ($serverName === $strServer) {
                                        $objMemCache = $this->buildMemcacheObj ($arrGroup);
                                        $objMemCache->set($strKey, $strValue, $bolFlag, $intExpire);
                                        unset ($objMemCache);
                                }
                        }
                }
                return true;
        }

        private function buildMemcacheObj ($arrGroup)
        {
                $objMemCache = new Memcached();
                $objMemCache->addServers($arrGroup);
                return $objMemCache;
        }
}

?>
