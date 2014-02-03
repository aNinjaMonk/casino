
<!-- saved from url=(0072)https://raw.github.com/ronnywang/PHPMemcacheSASL/master/MemcacheSASL.php -->
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body><pre style="word-wrap: break-word; white-space: pre-wrap;">&lt;?php

class MemcacheSASL
{
    protected $_request_format = 'CCnCCnNNNN';
    protected $_response_format = 'Cmagic/Copcode/nkeylength/Cextralength/Cdatatype/nstatus/Nbodylength/NOpaque/NCAS1/NCAS2';

    const OPT_COMPRESSION = -1001;

    const MEMC_VAL_TYPE_MASK = 0xf;
    const MEMC_VAL_IS_STRING = 0;
    const MEMC_VAL_IS_LONG = 1;
    const MEMC_VAL_IS_DOUBLE = 2;
    const MEMC_VAL_IS_BOOL = 3;
    const MEMC_VAL_IS_SERIALIZED = 4;

    const MEMC_VAL_COMPRESSED = 16; // 2^4

    protected function _build_request($data)
    {
        $valuelength = $extralength = $keylength = 0;
        if (array_key_exists('extra', $data)) {
            $extralength = strlen($data['extra']);
        }
        if (array_key_exists('key', $data)) {
            $keylength = strlen($data['key']);
        }
        if (array_key_exists('value', $data)) {
            $valuelength = strlen($data['value']);
        }
        $bodylength = $extralength + $keylength + $valuelength;
        $ret = pack($this-&gt;_request_format, 
                0x80, 
                $data['opcode'], 
                $keylength,
                $extralength,
                array_key_exists('datatype', $data) ? $data['datatype'] : null,
                array_key_exists('status', $data) ? $data['status'] : null,
                $bodylength, 
                array_key_exists('Opaque', $data) ? $data['Opaque'] : null,
                array_key_exists('CAS1', $data) ? $data['CAS1'] : null,
                array_key_exists('CAS2', $data) ? $data['CAS2'] : null
            );

        if (array_key_exists('extra', $data)) {
            $ret .= $data['extra'];
        }

        if (array_key_exists('key', $data)) {
            $ret .= $data['key'];
        }

        if (array_key_exists('value', $data)) {
            $ret .= $data['value'];
        }
        return $ret;
    }

    protected function _show_request($data)
    {
        $array = unpack($this-&gt;_response_format, $data);
        return $array;
    }

    protected function _send($data)
    {
        $send_data = $this-&gt;_build_request($data);
        fwrite($this-&gt;_fp, $send_data);
        return $send_data;
    }

    protected function _recv()
    {
        $data = fread($this-&gt;_fp, 24);
        $array = $this-&gt;_show_request($data);
	if ($array['bodylength']) {
	    $bodylength = $array['bodylength'];
	    $data = '';
	    while ($bodylength &gt; 0) {
		$recv_data = fread($this-&gt;_fp, $bodylength);
		$bodylength -= strlen($recv_data);
		$data .= $recv_data;
	    }

	    if ($array['extralength']) {
		$extra_unpacked = unpack('Nint', substr($data, 0, $array['extralength']));
		$array['extra'] = $extra_unpacked['int'];
	    }
	    $array['key'] = substr($data, $array['extralength'], $array['keylength']);
	    $array['body'] = substr($data, $array['extralength'] + $array['keylength']);
	}
        return $array;
    }

    public function __construct()
    {
    }


    public function listMechanisms()
    {
        $this-&gt;_send(array('opcode' =&gt; 0x20));
        $data = $this-&gt;_recv();
        return explode(" ", $data['body']);
    }

    public function setSaslAuthData($user, $password)
    {
        $this-&gt;_send(array(
                    'opcode' =&gt; 0x21,
                    'key' =&gt; 'PLAIN',
                    'value' =&gt; '' . chr(0) . $user . chr(0) . $password
                    ));
        $data = $this-&gt;_recv();

        if ($data['status']) {
            throw new Exception($data['body'], $data['status']);
        }
    }

    public function addServer($host, $port, $weight = 0)
    {
        $this-&gt;_fp = stream_socket_client($host . ':' . $port);
    }

    public function addServers($servers)
    {
      for ($i = 0; $i &lt; count($servers); $i++) {
        $s = $servers[$i];
        if (count($s) &gt;= 2) {
          $this-&gt;addServer($s[0], $s[1]);
        } else {
          trigger_error("could not add entry #"
            .($i+1)." to the server list", E_USER_WARNING);
        }
      }
    }

    public function addServersByString($servers)
    {
        $servers = explode(",", $servers);
        for ($i = 0; $i &lt; count($servers); $i++) {
            $servers[$i] = explode(":", $servers[$i]);
        }
        $this-&gt;addServers($servers);
    }

    public function get($key)
    {   
        $sent = $this-&gt;_send(array(
                    'opcode' =&gt; 0x00,
                    'key' =&gt; $key,
                    ));
	$data = $this-&gt;_recv();
	if (0 == $data['status']) {
            if ($data['extra'] &amp; self::MEMC_VAL_COMPRESSED) {
                $body = gzuncompress($data['body']);
            } else {
                $body = $data['body'];
            }

            $type = $data['extra'] &amp; self::MEMC_VAL_TYPE_MASK;

            switch ($type) {
            case self::MEMC_VAL_IS_STRING:
                $body = strval($body);
                break;

            case self::MEMC_VAL_IS_LONG:
                $body = intval($body);
                break;

            case self::MEMC_VAL_IS_DOUBLE:
                $body = doubleval($body);
                break;

            case self::MEMC_VAL_IS_BOOL:
                $body = $body ? true : false;
                break;

            case self::MEMC_VAL_IS_SERIALIZED:
                $body = unserialize($body);
                break;
            }

            return $body;
        }
        return FALSE;
    }

    /**
     * process value and get flag
     * 
     * @param int $flag
     * @param mixed $value 
     * @access protected
     * @return array($flag, $processed_value)
     */
    protected function _processValue($flag, $value)
    {
        if (is_string($value)) {
            $flag |= self::MEMC_VAL_IS_STRING;
        } elseif (is_long($value)) {
            $flag |= self::MEMC_VAL_IS_LONG;
        } elseif (is_double($value)) {
            $flag |= self::MEMC_VAL_IS_DOUBLE;
        } elseif (is_bool($value)) {
            $flag |= self::MEMC_VAL_IS_BOOL;
        } else {
            $value = serialize($value);
            $flag |= self::MEMC_VAL_IS_SERIALIZED;
        }

        if (array_key_exists(self::OPT_COMPRESSION, $this-&gt;_options) and $this-&gt;_options[self::OPT_COMPRESSION]) {
            $flag |= self::MEMC_VAL_COMPRESSED;
	    $value = gzcompress($value);
        }
        return array($flag, $value);
    }

    public function add($key, $value, $expiration = 0)
    {
        list($flag, $value) = $this-&gt;_processValue(0, $value);

        $extra = pack('NN', $flag, $expiration);
        $sent = $this-&gt;_send(array(
                    'opcode' =&gt; 0x02,
                    'key' =&gt; $key,
                    'value' =&gt; $value,
                    'extra' =&gt; $extra,
                    ));
        $data = $this-&gt;_recv();
        if ($data['status'] == 0) {
            return TRUE;
        }

        return FALSE;
    }

    public function set($key, $value, $expiration = 0)
    {
        list($flag, $value) = $this-&gt;_processValue(0, $value);

        $extra = pack('NN', $flag, $expiration);
        $sent = $this-&gt;_send(array(
                    'opcode' =&gt; 0x01,
                    'key' =&gt; $key,
                    'value' =&gt; $value,
                    'extra' =&gt; $extra,
                    ));
        $data = $this-&gt;_recv();
        if ($data['status'] == 0) {
            return TRUE;
        }

        return FALSE;
    }

    public function delete($key)
    {
        $sent = $this-&gt;_send(array(
                    'opcode' =&gt; 0x04,
                    'key' =&gt; $key,
                    ));
        $data = $this-&gt;_recv();
        if ($data['status'] == 0) {
            return TRUE;
        }

        return FALSE;
    }

    public function replace($key, $value, $expiration = 0)
    {
        list($flag, $value) = $this-&gt;_processValue(0, $value);

        $extra = pack('NN', $flag, $expiration);
        $sent = $this-&gt;_send(array(
                    'opcode' =&gt; 0x03,
                    'key' =&gt; $key,
                    'value' =&gt; $value,
                    'extra' =&gt; $extra,
                    ));
        $data = $this-&gt;_recv();
        if ($data['status'] == 0) {
            return TRUE;
        }

        return FALSE;
    }

    protected function _upper($num)
    {
        return $num &lt;&lt; 32;
    }

    protected function _lower($num)
    {
        return $num % (2 &lt;&lt; 32);
    }

    public function increment($key, $offset = 1)
    {
        $initial_value = 0;
        $extra = pack('N2N2N', $this-&gt;_upper($offset), $this-&gt;_lower($offset), $this-&gt;_upper($initial_value), $this-&gt;_lower($initial_value), $expiration);
        $sent = $this-&gt;_send(array(
                    'opcode' =&gt; 0x05,
                    'key' =&gt; $key,
                    'extra' =&gt; $extra,
                    ));
        $data = $this-&gt;_recv();
        if ($data['status'] == 0) {
            return TRUE;
        }

        return FALSE;
    }

    public function decrement($key, $offset = 1)
    {
        $initial_value = 0;
        $extra = pack('N2N2N', $this-&gt;_upper($offset), $this-&gt;_lower($offset), $this-&gt;_upper($initial_value), $this-&gt;_lower($initial_value), $expiration);
        $sent = $this-&gt;_send(array(
                    'opcode' =&gt; 0x06,
                    'key' =&gt; $key,
                    'extra' =&gt; $extra,
                    ));
        $data = $this-&gt;_recv();
        if ($data['status'] == 0) {
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Get statistics of the server
     *
     * @param string $type The type of statistics to fetch. Valid values are 
     *                     {reset, malloc, maps, cachedump, slabs, items,
     *                     sizes}. According to the memcached protocol spec
     *                     these additional arguments "are subject to change
     *                     for the convenience of memcache developers".
     *
     * @link http://code.google.com/p/memcached/wiki/BinaryProtocolRevamped#Stat
     * @access public
     * @return array  Returns an associative array of server statistics or
     *                FALSE on failure. 
     */
    public function getStats($type = null)
    {
        $this-&gt;_send(
            array(
                'opcode' =&gt; 0x10,
                'key' =&gt; $type,
            )
        );

        $ret = array();
        while (true) {
            $item = $this-&gt;_recv();
            if (empty($item['key'])) {
                break;
            }
            $ret[$item['key']] = $item['body'];
        }
        return $ret;
    }

    public function append($key, $value)
    {
        // TODO: If the Memcached::OPT_COMPRESSION is enabled, the operation
        // should failed.
        $sent = $this-&gt;_send(array(
                    'opcode' =&gt; 0x0e,
                    'key' =&gt; $key,
                    'value' =&gt; $value,
                    ));
        $data = $this-&gt;_recv();
        if ($data['status'] == 0) {
            return TRUE;
        }

        return FALSE;
    }

    public function prepend($key, $value)
    {
        // TODO: If the Memcached::OPT_COMPRESSION is enabled, the operation
        // should failed.
        $sent = $this-&gt;_send(array(
                    'opcode' =&gt; 0x0f,
                    'key' =&gt; $key,
                    'value' =&gt; $value,
                    ));
        $data = $this-&gt;_recv();
        if ($data['status'] == 0) {
            return TRUE;
        }

        return FALSE;
    }

    public function getMulti(array $keys)
    {
        // TODO: from http://code.google.com/p/memcached/wiki/BinaryProtocolRevamped#Get,_Get_Quietly,_Get_Key,_Get_Key_Quietly
        //       Clients should implement multi-get (still important for reducing network roundtrips!) as n pipelined requests ...
        $list = array();

        foreach ($keys as $key) {
            $value = $this-&gt;get($key);
            if (false !== $value) {
                $list[$key] = $value;
            }
        }

        return $list;
    }


    protected $_options = array();

    public function setOption($key, $value)
    {
	$this-&gt;_options[$key] = $value;
    }

    /**
     * Set the memcache object to be a session handler
     *
     * Ex:
     * $m = new MemcacheSASL;
     * $m-&gt;addServer('xxx', 11211);
     * $m-&gt;setSaslAuthData('user', 'password');
     * $m-&gt;setSaveHandler();
     * session_start();
     * $_SESSION['hello'] = 'world';
     *
     * @access public
     * @return void
     */
    public function setSaveHandler()
    {
        session_set_save_handler(
            function($savePath, $sessionName){ // open
            },
            function(){ // close
            },
            function($sessionId){ // read
                return $this-&gt;get($sessionId);
            },
            function($sessionId, $data){ // write
                return $this-&gt;set($sessionId, $data);
            },
            function($sessionId){ // destroy
                $this-&gt;delete($sessionId);
            },
            function($lifetime) { // gc
            }
        );
    }
}
</pre></body></html>