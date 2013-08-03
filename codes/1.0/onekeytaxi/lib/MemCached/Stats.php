<?php
class MemCached_Stats
{
        static function getSingleMemCachedStats($arrServer)
        {
                $objMemCache = new Memcache();

                $objMemCache->addServers($arrServer);

                $arrRes = $objMemCache->getStats();

                return $arrRes;
        }

}
?>
